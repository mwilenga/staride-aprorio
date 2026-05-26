<?php

namespace App\Http\Controllers\PaymentMethods\MomoPay;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use App\Models\Driver;
use App\Models\DriverCard;
use App\Models\PaymentOption;
use App\Models\PaymentOptionsConfiguration;
use App\Models\Merchant;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Onesignal;
use App\Models\Transaction;
use ModemPay\Laravel\Facades\ModemPay;
use Illuminate\Support\Str;
use App\Models\DriverCashout;
use App\Models\BusinessSegment\BusinessSegmentCashout;

class MomoPayController extends Controller
{
    use ApiResponseTrait,MerchantTrait;
    
    public function getMomoPayPayConfig($merchant_id){
        $cashout_option = PaymentOption::where('slug', 'MOMOPAY_CASHOUT')->first();
        $cashoutOption = PaymentOptionsConfiguration::where([['merchant_id', '=', $merchant_id],['payment_option_id','=',$cashout_option->id]])->first();
        
        $string_file = $this->getStringFile($merchant_id);
        
        if (empty($cashoutOption)) {
            return $this->failedResponse(trans("$string_file.configuration_not_found"));
        }
        return $cashoutOption;
    }

    private function CashoutToken($api_user,$api_key,$subscription_key,$checkoutUrl){
        $key_text = $api_user . ':' . $api_key;
        $token = base64_encode($key_text);
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $checkoutUrl.'disbursement/token/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic '. $token,
            'Cache-Control: no-cache',
            'Ocp-Apim-Subscription-Key: '.$subscription_key,
            'Content-Length: 0'
        ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    public function Cashout(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'calling_from' => 'required',
                // 'payment_method_id' => 'required',
                'amount' => 'required',
                'currency'=>'required',
                'phone'=> 'required',
                'payment_option_id'=>'required'
            ]);
    
            if ($validator->fails()){
                $errors = $validator->messages()->all();
                return $this->failedResponse($errors[0]);
            }
            
            $calling_from = $request->calling_from;
            
            if($calling_from == "DRIVER") {
                $driver = $request->user('api-driver');
                $countryCode = $driver->country->country_code;
                $id = $driver->id;
                $merchant_id = $driver->merchant_id;
                $lang = isset($driver->Country->default_language) ? $driver->Country->default_language : 'en';
            }
           elseif($calling_from == "BUSINESS_SEGMENT") {
                if($request->from_admin == 1){
                    $bs = get_business_segment(false);
                }else{
                    $bs = $request->user('business-segment-api');
                }
                $id = $bs->id;
                $merchant_id = $bs->merchant_id;
                $alias_name = $bs->Merchant->alias_name;
                $status = 4;
            }
        
           
        $configs = $this->getMomoPayPayConfig($merchant_id);
         
        $cashoutConfig = $configs;
        

        $checkoutUrl = $cashoutConfig->gateway_condition == 1 ? 'https://proxy.momoapi.mtn.com/' : 'https://sandbox.momodeveloper.mtn.com/';
       
        $currency = $cashoutConfig->gateway_condition == 1 ? $request->currency : 'EUR';
        $api_user= $cashoutConfig->api_public_key;
        $api_key=$cashoutConfig->api_secret_key;
        $subscription_key=$cashoutConfig->auth_token;
        $key_text = $api_user . ':' . $api_key;
        $token =  Str::uuid()->toString();
        $phone = $request->phone;
        $amount = $request->amount;
        $string_file = $this->getStringFile($merchant_id);
        $authToken=json_decode($this->CashoutToken($api_user, $api_key, $subscription_key, $checkoutUrl),true);
        if(empty($authToken)){
            throw new \Exception('Token not generated');
        }
        
        $targetEnv = $cashoutConfig->operator;
        $trans_id = 'TRANS_CASHOUT_'.time();
            $data = [
                "amount" => $amount,
                "currency" => $currency,
                "externalId" => $trans_id,
                "payee" => [
                    "partyIdType" => "MSISDN",
                    "partyId" => $phone
                ],
                "payerMessage" => "Cashout",
                "payeeNote" => "Cashout"
            ];



            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => $checkoutUrl.'disbursement/v1_0/transfer',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'X-Reference-Id: '.$token,
                'X-Target-Environment: '.$targetEnv,
                'Content-Type: application/json',
                'Cache-Control: no-cache',
                'Ocp-Apim-Subscription-Key: '.$subscription_key,
                'Authorization: Bearer '.$authToken['access_token']
            ),
            ));
            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
             \Log::channel('mom_cashout')->emergency(['cashout_request'=>$request->all(),'response'=> $response,'time'=>date('Y-m-d H:i:s')]);
            if ($httpcode == 202) 
                {
                    DB::table('transactions')->insert([
                    'user_id' => $calling_from == "USER" ? $id : NULL,
                    'driver_id' => $calling_from == "DRIVER" ? $id : NULL,
                    'merchant_id' => $merchant_id,
                    'payment_transaction_id'=> $trans_id,
                    'amount' => $amount,
                    'payment_option_id' => $cashoutConfig->payment_option_id,
                    'request_status'=> 1,
                    'status_message'=> 'PENDING',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                    $res = [
                    'transaction_id' => $trans_id,
                ];
                    if($calling_from == "DRIVER"){
                    DriverCashout::insertGetId([
                        'driver_id' => $driver->id,
                        'merchant_id' => $driver->merchant_id,
                        'amount' => $request->amount,
                        'cashout_status' => 1,
                        'action_by' => 'Online Payment',
                        'transaction_id' => $trans_id,
                        'comment' => trans("$string_file.cashout_request")
                    ]);
    
                    $paramArray = array(
                        'driver_id' => $driver->id,
                        'booking_id' => NULL,
                        'amount' => $request->amount,
                        'narration' => 28,
                    );
                    WalletTransaction::WalletDeduct($paramArray);
                    $title = trans("$string_file.cashout_request");
                    $message = trans("$string_file.cashout_request_sent");
    
                    $notification_data['notification_type'] = "WALLET_HISTORY";
                    $notification_data['segment_type'] = "WALLET_HISTORY";
                    $notification_data['segment_data'] = [];
                    $notification_data['segment_sub_group'] = null;
                    $arr_param = ['driver_id'=>$driver->id,'data'=>$notification_data,'message'=>$message,'merchant_id'=>$driver->merchant_id,'title'=>$title,'large_icon'=>""];
                    Onesignal::DriverPushMessage($arr_param);

                    }else{
                     BusinessSegmentCashout::create([
                        'business_segment_id' => $bs->id,
                        'merchant_id' => $bs->merchant_id,
                        'amount' => $request->amount,
                        'cashout_status' => 1,
                        'action_by' => 'Business Segment Online Payment',
                        'transaction_id' => $trans_id,
                        'comment' => trans("$string_file.cashout_request")
                    ]);
                    $paramArray = array(
                        'business_segment_id' => $bs->id,
                        'booking_id' => null,
                        'amount' => $request->amount,
                        'narration' => 4,
                    );
                    WalletTransaction::BusinessSegmntWalletDebit($paramArray);
                }
                
                if($request->from_admin == 1){
                    return ['message'=>trans("$string_file.cashout_request_registered_successfully"),'result'=>1];
                }else{
                    return $this->successResponse(trans("$string_file.cashout_request_registered_successfully"),$res);
                }
            }else{
                return $this->failedResponse($response['error']);
            }
        }catch(\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }

        
    }

    
    
    
}