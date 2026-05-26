<?php

namespace App\Http\Controllers\PaymentMethods\Yas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\Transaction;
use App\Models\PaymentOption;
use App\Models\PaymentOptionsConfiguration;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;

class YasController extends Controller
{
    use ApiResponseTrait, MerchantTrait;

     public function getBaseUrl($env)
    {
        return $env == 1 ? 'https://tgpp-mbanking-stp-gw-gen.togocom.tg/push-api/v1/debit' : 'https://tgpp-mbanking-stp-gw-gen.togocom.tg/push-api/v1/debit';
    }
    public function getYasPayConfig($merchant_id)
    {
        $payment_option = PaymentOption::where('slug', 'YASPAY')->first();
        $paymentOption = PaymentOptionsConfiguration::where([['merchant_id', '=', $merchant_id], ['payment_option_id', '=', $payment_option->id]])->first();
        $string_file = $this->getStringFile($merchant_id);
        if (empty($paymentOption)) {
            return $this->failedResponse(trans("$string_file.configuration_not_found"));
        }
        return $paymentOption;
    }

    public function makePaymentUsingyas($request, $paymentConfig, $calling_from)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'payment_method_id' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        if ($calling_from == "DRIVER") {
            $driver = $request->user('api-driver');
            $id = $driver->id;
            $merchant_id = $driver->merchant_id;
            $status = 2;
        } elseif ($calling_from == "USER") {
            $user = $request->user('api');
            $id = $user->id;
            $merchant_id = $user->merchant_id;
            $status = 1;
        }


        $url = $this->getBaseUrl($paymentConfig->gateway_condition);
        $token = $request->token;
        $refCommande = 'ORD-' . time() . '-' . rand(1000, 9999);
        $transactionId = "trans" . $id . '' . time();
        $data = [
            "numeroClient" => $request->customernumber ?? '',
            "montant" => (int) $request->amount ?? 0,
            "refCommande" => $refCommande,
            "idRequete" => $transactionId,
            "dateHeureRequete" => date("Y-m-d H:i:s"),
            "description" => $request->description ?? 'test'
        ];
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "Authorization: " . $token, 
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($ch);
        $result = json_decode($response, true);

        curl_close($ch);

        if (isset($result['fault'])) {
            return [
                'result' => 0,
                'response' => $result['fault']['message'] ?? 'Authentication failed',

            ];

        }

        if (isset($result['code']) && $result['code'] == "2000") {
            $calling_for = $request->calling_for == 'BOOKING' ? 3 : ($request->type == "USER" ? 1 : 2); 
            DB::table('transactions')->insert([
                'user_id' => $calling_from == "USER" ? $id : NULL,
                'driver_id' => $calling_from == "DRIVER" ? $id : NULL,
                'status' => $calling_for,
                'merchant_id' => $merchant_id,
                'payment_transaction_id' => $transactionId,
                'amount' => $request->amount,
                'payment_option_id' => $paymentConfig->payment_option_id,
                'request_status' => 1,
                'status_message' => 'PENDING',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return [
                'transaction_id' => $result['idRequete'] ?? '',
                'success_url' => route('yas-success'),
                'fail_url' => route('yas-fail'),
            ];

        } else {
            return [
                'result' => 0,
                'response' => $result['message'] ?? 'transaction not done'
            ];
        }



    }

    public function YasCallBack(Request $request)
    { 
        \Log::channel('yaspay')->emergency(['callback_url' => $request->all()]);
        $data = $request->all();
        if(isset($data['idRequete'])){
        $order_reference = $data['idRequete'];
        $payment_reference = $data['refCommande'];
        if (!empty($data)) {
            $trans = DB::table('transactions')->where(['request_status' => 1, 'payment_transaction_id' => $order_reference])->first();

            if (!empty($trans)) {

                $payment_status = $data['statutRequete'] == "SUCCES" ? 2 : 3;
                if ($payment_status == 2) {

                    DB::table('transactions')
                        ->where(['payment_transaction_id' => $order_reference])
                        ->update([
                            'request_status' => 2,

                            'payment_transaction' => json_encode($request->all()),
                            'reference_id' => $payment_reference,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'status_message' => 'SUCCESS'
                        ]);
                } else {

                    DB::table('transactions')
                        ->where(['payment_transaction_id' => $order_reference])
                        ->update([
                            'request_status' => 3,
                            'card_is_active' => 2,
                            'payment_transaction' => json_encode($request->all()),
                            'reference_id' => $payment_reference,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'status_message' => 'FAIL'
                        ]);
                }
            }
        }
        return "success";
        }
        return "data not received in callback";
    }

    public function createtoken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'calling_from' => 'required',
            'payment_method_id' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $calling_from = $request->calling_from;

        if ($calling_from == "DRIVER") {
            $driver = $request->user('api-driver');
            $merchant_id = $driver->merchant_id;
        } else {
            $user = $request->user('api');
            $merchant_id = $user->merchant_id;
        }
        $string_file = $this->getStringFile($merchant_id);
        $payment_option_config = $this->getYasPayConfig($merchant_id);

        if (!$payment_option_config) {
            return $this->failedResponse('Configuration not found');
        }
        $username = $payment_option_config->api_public_key;
        $password = $payment_option_config->api_secret_key;
        $data = [
            "nomUtilisateur" => $username,
            "motDePasse" => $password,
        ];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://tgpp-mbanking-stp-gw-gen.togocom.tg/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response, true);

        if (isset($res['statut']) && $res['statut']['code'] == '2000' && isset($res['data']) && isset($res['data']['token'])) {
            return $this->successResponse(trans("$string_file.success"), $res);
        }
        else if (isset($res['statut']) && $res['statut']['code'] == '4001') {
              return $this->failedResponse('Authentication Failed');
        }
        else {
            return $this->failedResponse('Token Not Created');
        }
    }

    public function YasPaysuccess(Request $request)
    {
        \Log::channel('yaspay')->emergency(['success_url' => $request->all()]);
        echo '<h3>Success</h3>';
    }

    public function YasPayFail(Request $request)
    {
        \Log::channel('yaspay')->emergency(['fail_url' => $request->all()]);
        echo '<h3>Failed</h3>';
    }

    public function PaymentStatus(Request $request)
    {
        $transactionId = $request->transaction_id;
        $transaction_table = DB::table("transactions")->where('payment_transaction_id', $transactionId)->first();
        $payment_status = $transaction_table->request_status == 2 ? true : false;
        $data = [];
        if ($transaction_table->request_status == 1) {
            $request_status_text = "processing";
            $transaction_status = 1;
            $data = ['payment_status' => $payment_status, 'request_status' => $request_status_text, 'transaction_status' => $transaction_status];
        } else if ($transaction_table->request_status == 2) {
            $request_status_text = "success";
            $transaction_status = 2;
            $data = ['payment_status' => $payment_status, 'request_status' => $request_status_text, 'transaction_status' => $transaction_status];
        } else {
            $request_status_text = "failed";
            $transaction_status = 3;
            $data = ['payment_status' => $payment_status, 'request_status' => $request_status_text, 'transaction_status' => $transaction_status];

        }
        return $data;
    }


}