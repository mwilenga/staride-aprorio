<?php

namespace App\Http\Controllers\PaymentMethods\Debito;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\Booking;
use App\Models\Order;
use App\Models\HandymanOrder;
use App\Models\Onesignal;
use App\Models\PaymentOption;
use App\Models\PaymentOptionsConfiguration;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DebitoController extends Controller
{
    use ApiResponseTrait, MerchantTrait;
    public function getDebitoConfig($merchant_id){
        $payment_option = PaymentOption::where('slug', 'DEBITO')->first();
        $paymentOption = PaymentOptionsConfiguration::where([['merchant_id', '=', $merchant_id],['payment_option_id','=',$payment_option->id]])->first();
        $string_file = $this->getStringFile($merchant_id);
        if (empty($paymentOption)) {
            return $this->failedResponse(trans("$string_file.configuration_not_found"));
        }
        return $paymentOption;
    }
    
    public function paymentRequest($request,$payment_option_config, $calling_from){
            $status = 3;
            if($calling_from == "DRIVER") {
                $user = $request->user('api-driver');
                $countryCode = $user->country->country_code;
                $id = $user->id;
                $merchant_id = $user->merchant_id;
                $status = 2;
                $lang = isset($user->Country->default_language) ? $user->Country->default_language : 'en';
            }
            else{
                $user = $request->user('api');
                $countryCode = $user->country->country_code;
                $id = $user->id;
                $status = 1;
                $merchant_id = $user->merchant_id;
                $lang = isset($user->Country->default_language) ? $user->Country->default_language : 'en';
            }
            $paymentOption=$this->getDebitoConfig($merchant_id);
            $apiKey = $paymentOption->api_secret_key;
        
            $phone = $request->phone;
            $amount = $request->amount;
            $string_file = $this->getStringFile($merchant_id);
            
            $ref = date('YmdHis');
            $walletId = trim($request->wallet_id);
            $paymentType = strtolower(trim($request->payment_type));
            $apiUrl = in_array($paymentType, ['emola', 'mpesa'])
                ? "https://my.debito.co.mz/api/v1/wallets/{$walletId}/c2b/{$paymentType}"
                : "https://my.debito.co.mz/api/v1/wallets/{$walletId}/card-payment";
           
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                  "msisdn": "'.$phone.'",
                  "amount": "'.$amount.'",
                  "reference_description": "Pagamento de serviço",
                  "internal_notes": "Observação opcional\\n\\n### Regras importantes\\n- `msisdn` deve respeitar o formato nacional (ex.: 84xxxxxxx para eMola).\\n- `amount` deve ser numérico e >= 1 (máx configurado: 50000)."
                }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer '.$apiKey
            ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            
            \Log::channel('debito')->emergency($response);
             $response = json_decode($response,true);
            if($response && isset($response['status']) &&  $response['status'] == "PROCESSING"){
                DB::table('transactions')->insert([
                    'user_id' => $calling_from == "USER" ? $id : NULL,
                    'driver_id' => $calling_from == "DRIVER" ? $id : NULL,
                    'status' => $status,
                    'booking_id' => $request->booking_id,
                    'order_id' => $request->order_id,
                    'merchant_id' => $merchant_id,
                    'payment_transaction_id'=>$response['debito_reference'],
                    'amount' => $amount,
                    'reference_id'=> $ref,
                    'payment_option_id' => $paymentOption->payment_option_id,
                    'request_status'=> 1,
                    'status_message'=> 'PENDING',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                return ['transaction_id'=>$response['debito_reference']];
            }else{
                throw new \Exception("Something Went Wrong");
            }
    }
    
    public function PaymentStatus(Request $request){
        $transaction_id = $request->transaction_id; 
        $transaction_table =  DB::table("transactions")->where('payment_transaction_id', $transaction_id)->first();
        $payment_option_config=$this->getDebitoConfig($transaction_table->merchant_id);
        $apiKey = $payment_option_config->api_secret_key;
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://my.debito.co.mz/api/v1/transactions/'.$transaction_id.'/status',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Authorization: Bearer '.$apiKey,
            ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            \Log::channel('debito')->emergency($response);
            $response = json_decode($response,true);
            if($response && isset($response['debito_reference']) && isset($response['status']) && $response['status'] == "SUCCESSFUL"){ //success
                DB::table('transactions')
                    ->where(['payment_transaction_id' => $transaction_id])
                    ->update(['request_status' => 2, 'payment_transaction' => json_encode($response), 'updated_at' => date('Y-m-d H:i:s'),'status_message'=> 'SUCCESS']);
                    
                $request_status_text = "success";
                $transaction_status = 2;
                $data = ['payment_status' => true, 'request_status' => $request_status_text, 'transaction_status' => $transaction_status];
            }elseif($response && isset($response['debito_reference']) && isset($response['status']) && $response['status'] == "FAILED"){ //failed
                  DB::table('transactions')
                    ->where(['payment_transaction_id' => $transaction_id])
                    ->update(['request_status' => 3, 'payment_transaction' => json_encode($response), 'updated_at' => date('Y-m-d H:i:s'),'status_message'=> 'FAIL']);
                $request_status_text = "failed";
                $transaction_status = 3;
                $data = ['payment_status' => false, 'request_status' => $request_status_text, 'transaction_status' => $transaction_status];
            }else{
                $request_status_text = "PROCESSING";
                $transaction_status = 1;
                $data = ['payment_status' => false, 'request_status' => $request_status_text, 'transaction_status' => $transaction_status];
            }
            return $data;
    }
    
     public function Callback(Request $request){
        \Log::channel('debito')->emergency(['callback_response'=>$request->all()]);
        $response = $request->all();
        $response= json_decode($response,true);
        $transactionId = $response['debito_reference'];
        $status = $response['status'];
        if($status == "FAILED" || $status == "REJECTED"){
            DB::table('transactions')
                ->where(['payment_transaction_id' => $transactionId])
                ->update(['request_status'=>3,'updated_at' => date('Y-m-d H:i:s'),'payment_transaction'=> json_encode($response)]);
        }elseif($status == "SUCCESSFUL"){
            DB::table('transactions')
               ->where(['payment_transaction_id' => $transactionId])
               ->update(['request_status' => 2, 'payment_transaction' => json_encode($response), 'updated_at' => date('Y-m-d H:i:s'),'status_message'=> 'SUCCESS']);
                    
        }
    }
    public function getDebitoPaymentOption(Request $request){
        $validator = Validator::make($request->all(),[
            'calling_from' => 'required',
        ]);
        if ($validator->fails()){
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $merchant_id = $request->calling_from == 'USER' ? $request->user('api')->merchant_id : $request->user('api-driver')->merchant_id;
        $payment_options = $paymentOption=$this->getDebitoConfig($merchant_id);
        $payment_options = json_decode($paymentOption->additional_data, true);
        $result = [];
        if (is_array($payment_options)) {
            foreach ($payment_options as $key => $value) {
                $result[] = [
                    'key' => $key,
                    'value' => (string) $value // convert to string as per your format
                ];
            }
        }
            $string_file = $this->getStringFile($merchant_id);
            
            return $this->successResponse(trans("$string_file.success"), $result);
    }
   
}