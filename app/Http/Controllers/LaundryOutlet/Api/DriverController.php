<?php

namespace App\Http\Controllers\LaundryOutlet\Api;

use App\Driver;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\BookingRequestDriver;
use App\Models\LaundryOutlet\LaundryOutletOrder;
use App\Traits\ApiResponseTrait;
use App\Traits\LaundryServiceTrait;
use App\Traits\MerchantTrait;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    //
    use LaundryServiceTrait, ApiResponseTrait, MerchantTrait;

    public function orderPickupVerify(Request $request): JsonResponse
    {
        try {
            $return = $this->LaundryOrderOTPVerification($request);
        } catch (Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse($return['message']);
    }

    public function deliverOrder(Request $request): JsonResponse
    {

        $driver = $request->user('api-driver');
        $string_file = $this->getStringFile($driver->merchant_id);
        $request_fields = [
            'action' => 'required',
            'laundry_outlet_order_id' => ['required', 'integer', Rule::exists('laundry_outlet_orders', 'id')->where(function ($query) {
            }),],
            'latitude' => 'required',
            'longitude' => 'required'
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $msg = trans("$string_file.success");
        try {
            DB::beginTransaction();
            $action = $request->action;
            $order = LaundryOutletOrder::find($request->laundry_outlet_order_id);
            $order_history = array_column(json_decode($order->order_status_history, true), 'order_status');
            switch ($action) {
                case "PICK_ORDER_STORE":
                    if (in_array(15, $order_history)) {
                        $order->order_status = 16;
                        $order->save();
                        $this->saveLaundryOrderStatusHistory($request, $order);
                        $this->NotifyUser($order);

                    } else {
                        return $this->failedResponse(trans("$string_file.not_picked"));
                    }
                    break;
                case "DELIVER_AT_STORE":
                    if (in_array(10, $order_history)) {
                        $order->order_status = 7;
                        $order->save();
                        $this->LaundryOrderSettlement($order);
                        $this->saveLaundryOrderStatusHistory($request, $order);
                        // $request = $request->merge(["notification_type" => "DELIVERED_AT_STORE"]);
                        // $this->NotifyDriver($request, $driver->id, $order);
                        $this->NotifyUser($order);

                        $driver = Driver::find($order->driver_id);
                        $driver->free_busy = 2;
                        $driver->save();
                        BookingRequestDriver::where('driver_id', $driver->id)
                            ->where('laundry_outlet_order_id', $order->id)
                            ->update([
                                'request_status' => 3,
                            ]);

                    } else {
                        return $this->failedResponse(trans("$string_file.not_picked"));
                    }
                    break;

            }
        } catch (Exception $e) {
            DB::rollback();
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        DB::commit();
        return $this->successResponse($msg);
    }


    //cancel order by driver
    public function cancelOrder(Request $request): array
    {
        DB::beginTransaction();
        try {
            $request_status = $request->status;
            $order = LaundryOutletOrder::Find($request->id);
            $driver = $request->user('api-driver');
            $string_file = $this->getStringFile($order->merchant_id);
            if ($order->order_status < 7 && !empty($order->id)) {
                $booking_request = BookingRequestDriver::where(['driver_id' => $driver->id, 'laundry_outlet_order_id' => $request->id])->first();
                $booking_request->request_status = 4;
                $booking_request->save();

                $message = trans('api.order_cancelled');
                $order->order_status = $request_status;
                $order->cancel_reason_id = $request->cancel_reason_id;
                $order->save();

                // save status history
                $this->saveLaundryOrderStatusHistory($request, $order);

                // change driver status
                $driver->free_busy = 2; // driver is free now
                $driver->save();

                // refund amount o user wallet if payment was online/wallet
                if (!empty($order->payment_method_id) && in_array($order->payment_method_id, [2, 4, 3])) {
                    $user = $order->User;
                    $user->wallet_balance = $user->wallet_balance + $order->final_paid_amount;
                    $user->save();
                    // send wallet credit notification
                    $paramArray = array(
                        'user_id' => $user->id,
                        'merchant_id' => $user->merchant_id,
                        'booking_id' => NULL,
                        'amount' => $order->final_amount_paid,
                        'order_id' => $order->id,
                        'narration' => 11,
                        'platform' => 2,
                        'payment_method' => $order->payment_method_id,
                        'payment_option_id' => $order->payment_option_id,
                        'transaction_id' => NULL
                    );
                    // p($paramArray);
                    WalletTransaction::UserWalletCredit($paramArray);
                }

                //send onesignal message to outlet
                $this->sendPushNotificationToLaundryOutlet($order, null, "CANCEL_ORDER");
            } else {
                $message = trans("$string_file.order_not_found");
                throw new Exception($message);
            }
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }
        DB::commit();
        /**send notification to user*/
        $this->NotifyUser($order);
        $return_data['message'] = $message;
        $return_data['data'] = [];
        return $return_data;
    }
}
