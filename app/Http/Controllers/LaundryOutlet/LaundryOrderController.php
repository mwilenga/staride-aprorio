<?php

namespace App\Http\Controllers\LaundryOutlet;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Controllers\Helper\Merchant as MerchantHelper;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Http\Controllers\PaymentMethods\Payment;
use App\Models\BookingConfiguration;
use App\Models\BookingRating;
use App\Models\BookingRequestDriver;
use App\Models\Driver;
use App\Models\LaundryOutlet\LaundryOutletConfiguration;
use App\Models\LaundryOutlet\LaundryOutletOrder;
use App\Models\PriceCard;
use App\Traits\LaundryServiceTrait;
use App\Traits\MerchantTrait;
use App\Traits\OrderTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;


class LaundryOrderController extends Controller
{
    //
    use LaundryServiceTrait, MerchantTrait, OrderTrait;

    public function todayOrder(Request $request)
    {
        $outlet = get_laundry_outlet(false);
        $order = new LaundryOutletOrder();
        $request->merge(['status' => "TODAY"]);
        $arr_orders = $order->getOrders($request, true);
        $req_param['merchant_id'] = $outlet->merchant_id;
        $arr_status = $this->getLaundryOrderStatus($req_param);
        $search_route = route('laundry-outlet.today-order');
        $request->merge(['search_route' => $search_route]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        $time_format = $outlet->Merchant->Configuration->time_format;
        $config = LaundryOutletConfiguration::where('laundry_outlet_id', $outlet->id)->first();
        $booking_config = BookingConfiguration::where('merchant_id', $outlet->merchant_id)->first();
        return view('laundry-outlet.order.today-order', compact('arr_orders', 'arr_status', 'search_view', 'arr_search', 'config', 'booking_config', 'time_format'));
    }

    public function orderSearchView($request)
    {
        $data['arr_search'] = $request->all();
        $order_search = View::make('laundry-outlet.order.order-search')->with($data)->render();
        return $order_search;
    }

    public function upcomingOrder(Request $request)
    {
        $outlet = get_laundry_outlet(false);
        $hide_user_info_from_store = $outlet->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $order = new LaundryOutletOrder();
        $request->merge(['status' => "UPCOMING"]);
        $arr_orders = $order->getOrders($request, true);
        $req_param['merchant_id'] = $outlet->merchant_id;
        $arr_status = $this->getLaundryOrderStatus($req_param);
        $search_route = route('laundry-outlet.today-order');
        $request->merge(['search_route' => $search_route]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        $time_format = $outlet->Merchant->Configuration->time_format;
        $config = LaundryOutletConfiguration::where('laundry_outlet_id', $outlet->id)->first();
        $booking_config = BookingConfiguration::where('merchant_id', $outlet->merchant_id)->first();
        return view('laundry-outlet.order.upcoming-order', compact('arr_orders', 'arr_status', 'search_view', 'arr_search', 'hide_user_info_from_store', 'config', 'booking_config', 'time_format'));
    }

    public function arrivedOrders(Request $request)
    {
        $outlet = get_laundry_outlet(false);
        $order = new LaundryOutletOrder();
        $request->merge(['status' => "ARRIVED"]);
        $arr_orders = $order->getOrders($request, true);
        $req_param['merchant_id'] = $outlet->merchant_id;
        $arr_status = $this->getLaundryOrderStatus($req_param);
        $search_route = route('laundry-outlet.arrived-order');
        $request->merge(['search_route' => $search_route]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        $time_format = $outlet->Merchant->Configuration->time_format;
        $config = LaundryOutletConfiguration::where('laundry_outlet_id', $outlet->id)->first();
        $booking_config = BookingConfiguration::where('merchant_id', $outlet->merchant_id)->first();
        $expire_minute = $config->order_expire_time;
        return view('laundry-outlet.order.arrived-order', compact('arr_orders', 'arr_status', 'search_view', 'arr_search', 'config', 'booking_config', 'time_format', 'expire_minute'));
    }


    public function getRejectedOrder(Request $request)
    {
        $order = new LaundryOutletOrder();
        $outlet = get_laundry_outlet(false);
        $hide_user_info_from_store = $outlet->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $request->merge(['status' => "REJECTED"]);
        $arr_orders = $order->getOrders($request, true);
        $req_param['merchant_id'] = $outlet->merchant_id;
        $arr_status = $this->getLaundryOrderStatus($req_param);
        $arr_search = $request->all();
        $flag = 2;
        $search_route = route('laundry-outlet.rejected-order');
        $request->merge(['search_route' => $search_route]);
        $search_view = $this->orderSearchView($request);
        $time_format = $outlet->Merchant->Configuration->time_format;
        return view('laundry-outlet.order.rejected-order', compact('arr_orders', 'arr_status', 'arr_search', 'search_view', 'hide_user_info_from_store', 'time_format', 'flag'));
    }

    public function orderDetail(Request $request, $id)
    {
        $order_obj = new LaundryOutletOrder();
        $request->merge(['id' => $id]);
        $order = $order_obj->getOrders($request);
        $outlet = $order->LaundryOutlet;
        $req_param['merchant_id'] = $order->merchant_id;
        $arr_status = $this->getLaundryOrderStatus($req_param);
        $hide_user_info_from_store = $order->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        return view('laundry-outlet.order.order-detail', compact('order', 'arr_status', 'outlet', 'hide_user_info_from_store'));
    }


    public function getPickupVerificationOrder(Request $request)
    {
        $order = new LaundryOutletOrder();
        $request->merge(['status' => "PICKUP_VERIFICATION"]);
        $arr_orders = $order->getOrders($request, true);
        $outlet = get_laundry_outlet(false);
        $req_param['merchant_id'] = $outlet->merchant_id;
        $arr_status = $this->getLaundryOrderStatus($req_param);
        $search_route = route('laundry-outlet.pending-pick-order');
        $request->merge(['search_route' => $search_route]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        $time_format = $outlet->Merchant->Configuration->time_format;
        return view('laundry-outlet.order.pending-processing', compact('arr_orders', 'arr_status', 'search_view', 'arr_search', 'time_format'));
    }

    public function orderOntheWay(Request $request)
    {
        $order = new LaundryOutletOrder();
        $request->merge(['status' => "ONTHEWAY"]);
        $arr_orders = $order->getOrders($request, true);
        $outlet = get_laundry_outlet(false);
        $hide_user_info_from_store = $outlet->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $req_param['merchant_id'] = $outlet->merchant_id;
        $arr_status = $this->getLaundryOrderStatus($req_param);
        $search_route = route('laundry-outlet.order-ontheway');
        $request->merge(['search_route' => $search_route]);
        $search_view = $this->orderSearchView($request);
        $time_format = $outlet->Merchant->Configuration->time_format;
        $arr_search = $request->all();
        return view('laundry-outlet.order.order-ontheway', compact('arr_orders', 'arr_status', 'search_view', 'arr_search', 'hide_user_info_from_store', 'time_format'));
    }


    public function pendingOrderDelivery(Request $request)
    {
        $order = new LaundryOutletOrder();
        $request->merge(['status' => "PENDING_ORDER_DELIVERY"]);
        $arr_orders = $order->getOrders($request, true);
        $outlet = get_laundry_outlet(false);
        $req_param['merchant_id'] = $outlet->merchant_id;
        $arr_status = $this->getLaundryOrderStatus($req_param);
        $search_route = route('laundry-outlet.pending-order-delivery');
        $request->merge(['search_route' => $search_route]);
        $search_view = $this->orderSearchView($request);
        $time_format = $outlet->Merchant->Configuration->time_format;
        $arr_search = $request->all();

        return view('laundry-outlet.order.pending-order-delivery', compact('arr_orders', 'arr_status', 'search_view', 'arr_search', 'time_format'));
    }

    public function OrderDelay(Request $request)
    {
        $request->validate([
            'delay_date_time' => 'required|date|after:now',
        ]);

        try {
            $order = LaundryOutletOrder::findOrFail($request->order_id);
            $order->update([
                // 'order_status' => 20,
                'delay_date_time' => $request->delay_date_time,
            ]);
            
            $this->saveLaundryOrderStatusHistory($request, $order);
            $this->NotifyUser($order);
            return back()->withSuccess('Order delay time updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }



    public function getCancelledOrder(Request $request)
    {
        $order = new LaundryOutletOrder();
        $request->merge(['status' => "CANCELLED"]);
        $arr_orders = $order->getOrders($request, true);
        $outlet = get_laundry_outlet(false);
        $hide_user_info_from_store = $outlet->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $req_param['merchant_id'] = $outlet->merchant_id;
        $arr_status = $this->getLaundryOrderStatus($req_param);
        $search_route = route('laundry-outlet.cancelled-order');
        $request->merge(['search_route' => $search_route]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        $time_format = $outlet->Merchant->Configuration->time_format;
        return view('laundry-outlet.order.cancelled-order', compact('arr_orders', 'arr_status', 'search_view', 'arr_search', 'hide_user_info_from_store', 'time_format'));
    }

    public function getCompletedOrder(Request $request)
    {
        $order = new LaundryOutletOrder();
        $is_order_completed = "yes";
        $request->merge(['arr_order_status' => "COMPLETED", 'is_order_completed' => $is_order_completed]);
        $arr_orders = $order->getOrders($request, true);
        $outlet = get_laundry_outlet(false);
        $req_param['merchant_id'] = $outlet->merchant_id;
        $arr_status = $this->getLaundryOrderStatus($req_param);
        $search_route = route('laundry-outlet.completed-order');
        $request->merge(['search_route' => $search_route]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        $time_format = $outlet->Merchant->Configuration->time_format;
        return view('laundry-outlet.order.completed-order', compact('arr_orders', 'arr_status', 'search_view', 'arr_search', 'time_format'));
    }

    public function orderInvoice(Request $request, $id)
    {
        $outlet = get_laundry_outlet(false);
        $order_obj = new LaundryOutletOrder();
        $request->merge(['id' => $id]);
        $order = $order_obj->getOrders($request);
        $arr_status = $this->getLaundryOrderStatus(['merchant_id' => $order->merchant_id]);
        $data = $request->all();
        return view('laundry-outlet.order.invoice', compact('order', 'arr_status', 'data', 'outlet'));
    }


    public function rejectOrder(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $outlet = get_laundry_outlet(false);

            $order = LaundryOutletOrder::Find($id);
            $string_file = $this->getStringFile(NULL, $order->Merchant);
            if (!empty($order) && $order->order_status == 1) {
                $request->request->add(['id' => $order->id, 'latitude' => $outlet->latitude, 'longitude' => $outlet->longitude]);
                $order->order_status = 3;
                $order->save();
                if ($order->payment_status == 1) {
                    $amount = $order->final_amount_paid;
                    $order->refund = 1;
                    $paramArray = [
                        'laundry_outlet_order_id' => $order->id,
                        'amount' => $amount,
                        'user_id' => $order->user_id,
                        'narration' => $amount,
                    ];
                    WalletTransaction::UserWalletCredit($paramArray);
                }
                // save status history
                $this->saveLaundryOrderStatusHistory($request, $order);
                /**send notification to user*/
                $this->NotifyUser($order);
            } else {
                $message = trans("$string_file.not_found");
                throw new Exception($message);
            }
            $merchant_id = $outlet->merchant_id;
            $string_file = $this->getStringFile($merchant_id);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
        DB::commit();
        $order_rejected_message = trans("$string_file.order_rejected_successfully");
        return redirect()->route('laundry-outlet.rejected-order')->withSuccess($order_rejected_message);
    }

    // self pickup order acceptance
    public function acceptOrder(Request $request, $id)
    {
        $outlet = get_laundry_outlet(false);
        $request->merge(['laundry_outlet_order_id' => $id]);
        try {
            $request->merge(['latitude' => $outlet->latitude, 'longitude' => $outlet->longitude]);
            $message = $this->acceptLaundryOutletOrder($request, $outlet);
        } catch (Exception $e) {
            $message = $e->getMessage();
            return redirect()->back()->withErrors($message);
        }

        return redirect()->route('laundry-outlet.arrived-order')->withSuccess($message);
    }

    public function orderCancel(Request $request, $id)
    {
        try {
            $outlet = get_laundry_outlet(false);
            $request->merge(['laundry_outlet_order_id' => $id]);
            $message = $this->cancelOrderByLaundryOutlet($request, $outlet);
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
        DB::commit();
        return redirect()->route('laundry-outlet.rejected-order')->withSuccess($message);
    }

    public function processOrder(Request $request, $id)
    {
        $outlet = get_laundry_outlet(false);
        $order_obj = new LaundryOutletOrder();
        $request->merge(['id' => $id]);
        $order = $order_obj->getOrders($request);
        $req_param['merchant_id'] = $order->merchant_id;
        $arr_status = $this->getLaundryOrderStatus($req_param);
        return view('laundry-outlet.order.process-order', compact('order', 'arr_status', 'outlet'));
    }

    public function startOrderProcessing(Request $request, $id)
    {
        $outlet = get_laundry_outlet(false);
        $request->merge(['id' => $id]);
        try {
            $message = $this->LaundryOrderProcessing($request, $outlet);
        } catch (Exception $e) {
            $message = $e->getMessage();
            return redirect()->back()->withErrors($message);
        }
        $order_obj = new LaundryOutletOrder();
        $request->merge(['id' => $id]);
        $order = $order_obj->getOrders($request);
        if ($order->ServiceType->type == 1) {
            return redirect()->route('laundry-outlet.pending-order-delivery')->withSuccess($message);
        }
        return redirect()->route('laundry-outlet.pending-pick-order')->withSuccess($message);
    }

    public function orderPickupVerify(Request $request)
    {
        try {
            $return = $this->LaundryOrderOTPVerification($request);
            $message = $return['message'];
        } catch (Exception $e) {
            $message = $e->getMessage();
            return redirect()->route('laundry-outlet.pending-pick-order')->withErrors($message);
        }
        return redirect()->route('laundry-outlet.pending-order-delivery')->withSuccess($message);
    }


    public function deliverOrder(Request $request, $id)
    {
        $outlet = get_laundry_outlet(false);
        $order_obj = new LaundryOutletOrder();
        $request->merge(['id' => $id]);
        $order = $order_obj->getOrders($request);
        $hide_user_info_from_store = $outlet->Merchant->ApplicationConfiguration->hide_user_info_from_store;

        $arr_status = $this->getLaundryOrderStatus(['merchant_id' => $order->merchant_id]);
        $data = $request->all();

        //        //check commsion from store when self pickup
        //        if ($outlet->commission_method == 1) {
        //            $commission_cut = round($outlet->commission, 2);
        //        } else {
        //            $commission_cut_per = ($order->final_amount_paid * $outlet->commission) / 100;
        //            $commission_cut = round($commission_cut_per, 2);
        //        }
        //
        //        if ($outlet->wallet_amount < $commission_cut) {
        //            return redirect()->back()->withErrors(trans('admin.wallet_balance_low'));
        //        }
        //        $paramArray = array(
        //            'laundry_outlet_id' => $outlet->id,
        //            'booking_id' => null,
        //            'amount' => $commission_cut,
        //            'narration' => 6,
        //            'laundry_outlet_order_id' => $order->id ?? ""
        //        );
        //        WalletTransaction::LaundryOutletWalletDebit($paramArray);
        //  p('inn');
        return view('laundry-outlet.order.deliver-order', compact('order', 'arr_status', 'data', 'outlet', 'hide_user_info_from_store'));
    }


    public function deliverOrderRequest(Request $request, $id)
    {

        try {
            $request->merge(['laundry_outlet_order_id' => $id]);
            $resp = [];
            $order = LaundryOutletOrder::find($id);
            $string_file = $this->getStringFile($order->merchant_id);
            if (!empty($order->id) && ($order->order_status == 13 || $order->order_status == 16)) {
                $resp = $this->saveDeliverOrderRequest($request, $order);
            } else {
                return redirect()->back()->withErrors(trans("$string_file.order_not_found"));
            }
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }

        return redirect()->route('laundry-outlet.completed-order')->withSuccess($resp['msg']);
    }

    public function saveDeliverOrderRequest($request, $order): array
    {
        DB::beginTransaction();
        $string_file = $this->getStringFile($order->merchant_id);
        $success_msg = trans("$string_file.user_collected_order");
        try {
            //            $prev_order_status = $order->order_status;
            $lat = ($order->order_status == 13) ? $order->LaundryOutlet->latitude : $request->latitude;
            $lng = ($order->order_status == 13) ? $order->LaundryOutlet->longitude : $request->longitude;
            $order->order_status = 14; // delivered order
            $order->is_order_completed = 1; // delivered order
            if ($order->payment_status != 1) {
                $order->payment_status = 1; // payment done
            }
            $order->save();

            $request->merge(['order_status' => $order->order_status, 'latitude' => $lat, 'longitude' => $lng]);
            $this->saveLaundryOrderStatusHistory($request, $order);

            // update transaction table
            $this->LaundryOrderTransaction($request, $order);  //update store earning and merchant earnings
            $order = $order->fresh();

            // order settlement
            $this->LaundryOrderSettlement($order);
            $this->NotifyUser($order);
            $this->sendPushNotificationToLaundryOutlet($order, null, "ORDER_COMPLETED");
        } catch (Exception $e) {
            DB::rollBack();
            return ["status" => false, "msg" => $e->getMessage()];
        }
        DB::commit();
        return ["status" => true, "msg" => $success_msg];
    }


    public function orderAssign(Request $request, $id)
    {
      
        $outlet = get_laundry_outlet(false);
        $order_obj = new LaundryOutletOrder();
        $request->merge(['id' => $id]);
        $order = $order_obj->getLaundryOrderInfo($request);

        $driver_not = false;
        $arr_not_drivers = [];

        $request->merge([
            'latitude' => $order->drop_latitude,
            'longitude' => $order->drop_longitude, //user's location
            'merchant_id' => $outlet->merchant_id,
            'segment_id' => $outlet->segment_id,
            'segment_slug' => "LAUNDRY_OUTLET",
            'user_id' => $order->user_id,
            'service_type_id' => $order->service_type_id,
            'driver_vehicle_id' => $order->driver_vehicle_id,
            'driver_not' => $driver_not,
            'arr_not_drivers' => $arr_not_drivers,
            ''
        ]);
        $arr_driver = Driver::getDeliveryCandidate($request);
        
       
        $req_param['merchant_id'] = $order->merchant_id;
        $arr_status = $this->getLaundryOrderStatus($req_param);
        $data = $request->all();
        return view('laundry-outlet.order.assign', compact('order', 'arr_status', 'data', 'arr_driver', 'driver_not'));
    }

    //send request to manual selected  drivers
    public function orderAssignToDriver(Request $request)
    {
       

        $outlet = get_laundry_outlet(false);
        try {
            $message = $this->manualAssign($request, $outlet);
            return redirect()->route('laundry-outlet.today-order')->withSuccess($message);
        } catch (Exception $e) {
            $message = $e->getMessage();
            return redirect()->back()->withErrors($message);
        }
    }


    public function manualAssign($request, $outlet)
    {

        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $validator = Validator::make(
            $request->all(),
            [
                'laundry_outlet_order_id' => [
                    'required',
                    'integer',
                    Rule::exists('laundry_outlet_orders', 'id')->where(function ($query) {
                        $query->whereIn('order_status', [1, 13,20]); // order can be assigned when status is 1
                    }),
                    'driver_id' => 'required',
                ]
            ],
            [
                'driver_id.required' => trans_choice("$string_file.have_to_choose", 3, ['NUM' => trans("$string_file.one"), 'OBJECT' => trans("$string_file.driver")]),
            ]
        );

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new Exception($errors[0]);
        }
        try {
            $order_id = $request->laundry_outlet_order_id;
            $order_obj = new LaundryOutletOrder();
            $request->merge(['id' => $order_id]);
            $order = $order_obj->getLaundryOrderInfo($request);

            $request->merge(['id' => $order_id]);
            return $this->LaundryOrderPickupNotification($request, $order);
        } catch (Exception $e) {
            $message = $e->getMessage();
            throw new Exception($message);
        }
    }

    // send request to auto selecting drivers
    public function orderAutoAssign(Request $request)
    {
        try {
            $outlet = get_laundry_outlet(false);

            $order_obj = new LaundryOutletOrder();
            $request->merge(['id' => $request->laundry_outlet_order_id]);
            $order = $order_obj->getLaundryOrderInfo($request);

            $request->merge([
                'latitude' => $order->drop_latitude,
                'longitude' => $order->drop_longitude,
                'merchant_id' => $order->merchant_id,
                'segment_id' => $order->segment_id
            ]);
            $message = $this->autoAssign($request, $outlet);
            return redirect()->route('laundry-outlet.today-order')->withSuccess($message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return redirect()->back()->withErrors($message);
        }
    }


    // send request to auto selecting drivers
    public function autoAssign($request, $outlet)
    {
        $validator = Validator::make($request->all(), [
            //            'order_id' => 'required',
            'laundry_outlet_order_id' => [
                'required',
                'integer',
                Rule::exists('laundry_outlet_orders', 'id')->where(function ($query) {
                    $query->whereIn('order_status', [1, 13]);
                }),
            ]
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new \Exception($errors[0]);
        }
        try {
            $order_id = $request->laundry_outlet_order_id;
            $order_obj  = new LaundryOutletOrder();
            $request->merge(['id' => $order_id]);
            $order = $order_obj->getLaundryOrderInfo($request);
            return $this->LaundryOrderPickupNotification($request, $order);
            //            return $message;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \Exception($message);
        }
    }

    public function orderAcceptReject(Request $request): array
    {
        DB::beginTransaction();
        try {
            $request_status = $request->status;
            $order = LaundryOutletOrder::sharedLock()->Find($request->id);

            $driver = $request->user('api-driver');
            $string_file = $this->getStringFile($driver->merchant_id);
            if (empty($order)) {
                $message = trans("$string_file.order_not_found");
                throw new Exception($message);
            }

            $booking_request = BookingRequestDriver::where([['laundry_outlet_order_id', "=", $order->id], ['driver_id', "=", $driver->id]])->first();
            $driver_request_status = null;
            $message = "";
            $data = [];
            if ($order->order_status == 1 || $order->order_status == 13 && !empty($order->id)) {
                if ($request_status == "REJECT") {
                    $message = trans("$string_file.rejected");
                    $driver_request_status = 3;
                } elseif ($request_status == "ACCEPT") {
                    $order_status = ($order->order_status == 1) ? 6 : 15; // accepted by driver
                    if ($order->order_status == 1) {
                        $order->driver_id = $driver->id;
                    } else {
                        $order->drop_driver_id = $driver->id;
                    }

                    $user_drop_location[0] = [
                        'drop_latitude' => ($order->order_status == 1) ? $order->drop_latitude : $order->LaundryOutlet->latitude,
                        'drop_longitude' => ($order->order_status == 1) ? $order->drop_longitude : $order->LaundryOutlet->longitude,
                        'drop_location' => "",
                    ];
                    $google_key = $order->Merchant->BookingConfiguration->google_key;

                    // distance b/w store and user vice versa
                    $lat = ($order->order_status == 1) ? $order->LaundryOutlet->latitude : $order->drop_latitude;
                    $long = ($order->order_status == 1) ? $order->LaundryOutlet->longitude : $order->drop_longitude;

                    $dummy_google_result = ['total_distance' => "0", 'total_distance_text' => "", 'total_time' => "0", 'total_time_minutes' => "", 'total_time_text' => "", 'image' => "", "poly_points" => ""];
                    $user_distance = GoogleController::GoogleStaticImageAndDistance($lat, $long, $user_drop_location, $google_key, "", $string_file);

                    if (empty($user_distance)) {
                        $user_distance = $dummy_google_result;
                    }
                    if ($order->order_status == 1) {
                        $order->estimate_distance = $user_distance['total_distance_text'];
                        $order->estimate_time = $user_distance['total_time_text'];
                        $order->travel_distance = $user_distance['total_distance'];
                        $order->travel_time = $user_distance['total_time'];
                    } else {
                        $order->drop_estimate_distance = $user_distance['total_distance_text'];
                        $order->drop_estimate_time = $user_distance['total_time_text'];
                        $order->drop_travel_distance = $user_distance['total_distance'];
                        $order->drop_travel_time = $user_distance['total_time'];
                    }

                    $order->otp_for_pickup = rand(1000, 9999);
                    $message = trans("$string_file.accepted");
                    $driver_request_status = 2; // accepting request
                    $order->order_status = $order_status;

                    // change driver status
                    $driver->free_busy = 2;
                    $driver->save();

                    // get driver price card
                    $price_card = PriceCard::where([['status', '=', 1], ['country_area_id', '=', $order->country_area_id], ['merchant_id', '=', $order->merchant_id], ['service_type_id', '=', $order->service_type_id], ['segment_id', '=', $order->segment_id], ['price_card_for', '=', 1]])->first();
                    $driver_distance = $order->travel_distance; // distance in meter
                    if (empty($price_card)) {
                        throw new Exception(trans("$string_file.no_price_card_for_area"));
                    }

                    $distance_charges = 0;
                    $price_card_detail_id = NULL;
                    $delivery_charge_slabs = $price_card->PriceCardDetail->toArray();

                    $request->request->add(['for' => 1, 'distance' => $driver_distance, 'cart_amount' => NULL]);
                    $slab = $this->getDistanceSlab($request, $delivery_charge_slabs);
                    if (isset($slab['id']) && isset($slab['slab_amount'])) {
                        $distance_charges = $slab['slab_amount'];
                        $price_card_detail_id = $slab['id'];
                    }

                    if ($order->order_status == 6) {
                        $bill_details = json_decode($order->bill_details, true);
                        $driver_bill = ['price_card_detail_id' => $price_card_detail_id, 'slab_amount' => $distance_charges, 'distance' => $order->travel_distance, 'pick_up_fee' => $price_card->pick_up_fee, 'drop_off_fee' => $price_card->drop_off_fee];
                        $bill_details['driver'] = $driver_bill;
                        $order->bill_details = json_encode($bill_details);
                    } else {
                        $bill_details = json_decode($order->drop_bill_details, true);
                        $driver_bill = ['price_card_detail_id' => $price_card_detail_id, 'slab_amount' => $distance_charges, 'distance' => $order->travel_distance, 'pick_up_fee' => $price_card->pick_up_fee, 'drop_off_fee' => $price_card->drop_off_fee];
                        $bill_details['driver'] = $driver_bill;
                        $order->drop_bill_details = json_encode($bill_details);
                    }
                    $order->save();

                    $this->LaundryOrderTransaction($request, $order);
                    $this->saveLaundryOrderStatusHistory($request, $order);

                    // send already order accepted notification to other drivers
                    //in some cases same player  id will be used for driver accepted or rejected then it has to be exclude from list
                    $ongoing_request_drivers = BookingRequestDriver::select('driver_id')
                        ->with(['Driver' => function ($q) use ($driver) {
                            return $q->where('player_id', '!=', $driver->player_id);
                        }])
                        ->whereHas('Driver', function ($q) use ($driver) {
                            return $q->where('player_id', '!=', $driver->player_id);
                        })
                        ->where([['laundry_outlet_order_id', '=', $order->id], ['request_status', '=', 1]])->get();

                    $ids = array_pluck($ongoing_request_drivers, 'driver_id');
                    if (!empty($ids)) {
                        $request->request->add(['notification_type' => 'BOOKING_ACCEPTED_BY_OTHER_DRIVER']);
                        $this->NotifyDriver($request, $ids, $order);
                    }
                }

                if (!empty($booking_request)) {
                    $booking_request->request_status = $driver_request_status;
                    $booking_request->save();
                }

                $order_obj = new LaundryOutletOrder();
                $order = $order_obj->getLaundryOrderInfo($request);
                $data["current_status"] = $order->order_status;
            } else {
                $message = trans("$string_file.order_already_accepted");
                throw new Exception($message);
            }
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }
        // BookingRequestDriver::where('laundry_outlet_order_id', $request->id)->whereIn('driver_id', [$driver->id])->update(['request_status' => 3]);

        DB::commit();
        $this->NotifyUser($order);
        $return_data['message'] = $message;
        $return_data['data'] = $data;
        return $return_data;
    }

    //    public function LaundryOrderInfoResponse(Request $request, $order)
    //    {
    //        $icon = "";
    //        $order_description = "";
    //        $button_text = "";
    //        $path_type = "STILL";
    //        $string_file = $this->getStringFile(NULL, $order->Merchant);
    //        $user_address = $order->drop_location;
    //        $business_segment_address = $order->LaundryOutlet->address;
    //        $outlet_name = $order->LaundryOutlet->full_name;
    //        $drop_location = [];
    //        $dummy_google_result = ['total_distance' => "0", 'total_distance_text' => "", 'total_time' => "0", 'total_time_minutes' => "", 'total_time_text' => "", 'image' => "", "poly_points" => ""];
    //        $call_google_api = false;
    //        $merchant_helper = new MerchantHelper();
    //
    //        $call_google_api = false;
    //        $drop_location[0] = [
    //            'drop_latitude' => $order->drop_latitude,
    //            'drop_longitude' => $order->drop_longitude,
    //            'drop_location' => $user_address,
    //        ];
    //
    //        if (empty($google_result) || !is_array($google_result)) {
    //            $google_result = $dummy_google_result;
    //        }
    //        $cancel_able = false;
    //        $merchant_id = $order->merchant_id;
    //        $otp = "";
    //        if ($order->Merchant->Configuration->order_process_otp_bypass == 2 && !empty($order->otp_for_pickup)) {
    //            $otp = $order->otp_for_pickup;
    //        }
    //        $order_details = [
    //            'order_name' => $order->Segment->Name($merchant_id) . ' ' . trans($string_file . ".order"),
    //            'order_id' => $order->id,
    //            'order_number' => $order->merchant_order_id,
    //            'segment_id' => $order->segment_id,
    //            'segment_group_id' => $order->Segment->segment_group_id,
    //            'segment_sub_group' => $order->Segment->sub_group_for_app,
    //            'payment_mode' => $order->PaymentMethod->MethodName($merchant_id) ? $order->PaymentMethod->MethodName($merchant_id) : $order->PaymentMethod->payment_method,
    //            //            'payment_mode'=>$order->PaymentMethod->payment_method,
    //            'order_description' => $order_description,
    //            // 'order_price' => $order->CountryArea->Country->isoCode . ' ' . $order->final_amount_paid,
    //            'order_price' => $order->CountryArea->Country->isoCode . ' ' . $merchant_helper->PriceFormat($order->final_amount_paid, $merchant_id),
    //            'cancel_able' => $cancel_able,
    //            // 'otp_for_pickup' => !empty($order->otp_for_pickup) ? $order->otp_for_pickup : "",
    //            'otp_for_pickup' => $otp,
    //            'confirmed_otp_for_pickup' => $order->Merchant->Configuration->order_process_otp_bypass== 1 ? true : ($order->confirmed_otp_for_pickup == 1 ? true : false),
    //            //            'widget_image'=>isset($order->Segment->Merchant[0]['pivot']->icon) && !empty($order->Segment->Merchant[0]['pivot']->icon) ? get_image($order->Segment->Merchant[0]['pivot']->icon, 'segment', $order->merchant_id, true) :
    //            //                get_image($order->Segment->icon, 'segment_super_admin', NULL, false)
    //            'widget_image' => isset($order->LaundryOutlet->business_logo) && !empty($order->LaundryOutlet->business_logo) ? get_image($order->LaundryOutlet->business_logo, 'business_logo', $order->merchant_id, true) : get_image($order->Segment->icon, 'segment_super_admin', NULL, false),
    //            'delivery_mode' => isset($order->delivery_mode)? $order->delivery_mode : 2,
    //        ];
    //        // its object in case of taxi and delivery, but array in case of food and grocery because of app ui
    //        $customer_details[] = [
    //            'customer_name' => $order->User->first_name . ' ' . $order->User->last_name,
    //            'customer_image' => get_image($order->User->UserProfileImage, 'user', $order->merchant_id, true, false),
    //            'customer_phone' => $order->User->UserPhone,
    //        ];
    //        $user_name = $order->User->first_name . ' ' . $order->User->last_name;
    //        /*******Some strings translations ********/
    //        $sos_string = trans("$string_file.sos");
    //        $order_string = trans($string_file . ".order");
    //        $pick_order_string = trans($string_file . ".pick") . ' ' . $order_string;
    //        $moving_to_pickup_string = trans($string_file . ".moving_towards_pickup") . ' ' . $outlet_name;
    //        $goto_pickup_string = trans("$string_file.arrived_at_pickup") . ' ' . $outlet_name;
    //        $action_buttons[] = [
    //            'button_icon' => $user_name,
    //            'button_text' => trans("$string_file.customer_support"),
    //            'button_text_colour' => "FFFFFF",
    //            'button_action' => $sos_string,
    //        ];
    //        // $order_status_action =  [6,7,10,11];
    //        // if($order->Segment->slag == "FOOD")
    //        // {
    //        $order_status_action =  [6];
    //        // }
    //        $order_list = $order->LaundryOutletOrderDetail;
    //        $service_data = [];
    //        foreach ($order_list as $service) {
    //            $service_data[] = ['value' => $service->quantity . ' X ' . $service->Service->Name($order->merchant_id) ];
    //        }
    //        $order_status_holders = [];
    //        $req_param['string_file'] = $string_file;
    //        $config_status = $this->getLaundryOrderStatus($req_param);
    //        $pending_icon = view_config_image("static-images/inactive-status.png");
    //        $completed_icon = view_config_image("static-images/tic-with-white-back.png");
    //        $current_icon = view_config_image("static-images/working-on.png");
    //        $order_status = [];
    //        $arr_completed_order = [];
    //        if (!empty($order->order_status_history)) {
    //            $status_completed = json_decode($order->order_status_history, true);
    //            $status_completed =  array_column($status_completed, NULL, 'order_status');
    //            $arr_completed_order = array_keys($status_completed);
    //        }
    //        $arr_completed_steps = $status_completed;
    //        $api_to_call = "";
    //        foreach ($order_status_action as $order_status) {
    //            $status_description = [];
    //            $completed_time = "";
    //            //            $api_to_call = "";
    //            $moving_to_pickup = [];
    //            if ($order_status == 6) {
    //                if (in_array($order_status, $arr_completed_order)) {
    //                    $completed_time = $status_completed[$order_status]['order_timestamp'];
    //                    $icon = $completed_icon;
    //                    $api_to_call = "ARRIVE_AT_STORE";
    //                }
    //                if ($order->order_status == $order_status) {
    //                    $descriptive_text = !empty($business_segment_address) ? [['value' => $business_segment_address]] : [];
    //                    $status_description = [
    //                        "highlighted_text" => $outlet_name,
    //                        "descriptive_text" => $descriptive_text,
    //                        "navigation_visibility" => true,
    //                    ];
    //                    $moving_to_pickup = [
    //                        'status_time' => "",
    //                        'tick_icon' => $current_icon,
    //                        'status_text' => $moving_to_pickup_string,
    //                        'status_description' => !empty($status_description) ? [$status_description] : $status_description,
    //                    ];
    //                    $status_description = [];
    //                    $button_text = $goto_pickup_string;
    //                }
    //            }
    //            // elseif ($order_status == 7) {
    //            //     //p($order->order_status);
    //            //     if ($order->order_status == $order_status) {
    //            //         $status_description = [
    //            //             "highlighted_text" => $business_segment_name,
    //            //             "descriptive_text" => $service_data,
    //            //             "navigation_visibility" => false,
    //            //         ];
    //            //         $path_type = "ANIMATED";
    //            //         $icon = $current_icon;
    //            //         $button_text = $pick_order_string;
    //            //         $api_to_call = "PICK_ORDER";
    //            //     } elseif (in_array($order_status, $arr_completed_order)) {
    //            //         $completed_time = $status_completed[$order_status]['order_timestamp'];
    //            //         $icon = $completed_icon;
    //            //     } else {
    //            //         $icon = $pending_icon;
    //            //     }
    //            // } elseif ($order_status == 9) {
    //            //     if ($order->order_status == $order_status) {
    //            //         $path_type = "ANIMATED";
    //            //         $status_description = [
    //            //             "highlighted_text" => $business_segment_name,
    //            //             "descriptive_text" => $service_data,
    //            //             "navigation_visibility" => false,
    //            //         ];
    //            //         $icon = $current_icon;
    //            //         if (in_array(7, array_keys($arr_completed_steps))) {
    //            //             $button_text = $pick_order_string;
    //            //             $api_to_call = "PICK_ORDER";
    //            //         } else {
    //            //             $button_text = $goto_pickup_string;
    //            //             $api_to_call = "ARRIVE_AT_STORE";
    //            //         }
    //            //     } elseif (in_array($order_status, $arr_completed_order)) {
    //            //         $completed_time = $status_completed[$order_status]['order_timestamp'];
    //            //         $icon = $completed_icon;
    //            //     } else {
    //            //         $icon = $pending_icon;
    //            //     }
    //            // } elseif ($order_status == 12) {
    //            //     if ($order->order_status == $order_status) {
    //            //         $path_type = "STILL";
    //            //         $descriptive_text = !empty($user_address) ? [['value' => $user_address]] : [];
    //            //         $status_description = [
    //            //             "highlighted_text" => $user_name,
    //            //             "descriptive_text" => $descriptive_text,
    //            //             "navigation_visibility" => true,
    //            //         ];
    //            //         $icon = $current_icon;
    //            //         $button_text = trans("$string_file.deliver") . ' ' . $order_string;
    //            //         $api_to_call = "DELIVER_ORDER";
    //            //     } else if (in_array($order_status, $arr_completed_order)) {
    //            //         $completed_time = $status_completed[$order_status]['order_timestamp'];
    //            //         $icon = $completed_icon;
    //            //     } else {
    //            //         $icon = $pending_icon;
    //            //     }
    //            // }
    //            elseif ($order_status == 10) {
    //                if (in_array($order_status, $arr_completed_order)) {
    //                    $completed_time = $status_completed[$order_status]['order_timestamp'];
    //                    $icon = $completed_icon;
    //                } elseif ($order->order_status == $order_status) {
    //                    $icon = $current_icon;
    //                } else {
    //                    $icon = $pending_icon;
    //                }
    //            }
    //            $status_text =  $config_status[$order_status];
    //            $order_status_holders[] = [
    //                'status_time' => $completed_time,
    //                'tick_icon' => $icon,
    //                'status_text' => $status_text,
    //                'status_description' => !empty($status_description) ? [$status_description] : $status_description,
    //            ];
    //            if ($order->order_status == $order_status && $order_status == 6 && !empty($moving_to_pickup)) {
    //                array_push($order_status_holders, $moving_to_pickup);
    //            }
    //        }
    //        //        if($order->order_status <= 7)
    //        if (!in_array(11, $arr_completed_order)) {
    //            $destination_latitude = $order->drop_latitude;
    //            $destination_longitude = $order->drop_longitude;
    //        } else {
    //            $destination_latitude = $order->LaundryOutlet->latitude;
    //            $destination_longitude = $order->LaundryOutlet->longitude;
    //        }
    //        $return_data = [
    //            'order_details' => $order_details,
    //            'customer_details' => $customer_details,
    //            'order_status_holders' => $order_status_holders,
    //            'order_current_status' => $order->order_status,
    //            'button_text' => $button_text,
    //            'api_to_call' => $api_to_call,
    //            'status_button_type' => "STRICT", //SLIDER
    //            'destination_location' => [
    //                'lat' => $destination_latitude,
    //                'lng' => $destination_longitude,
    //            ],
    //            'path_type' => $path_type, //NA,ANIMATED,STILL
    //            'poly_line' => $google_result['poly_points'], //NA,ANIMATED,STILL
    //            "action_buttons" => $action_buttons,
    //        ];
    //        return $return_data;
    //    }


    public function orderPaymentInfo(Request $request): array
    {
        $merchant_helper = new MerchantHelper();
        try {
            $order = LaundryOutletOrder::Find($request->id);

            $driver_payout =  0;
            if ($order->Merchant->Configuration->laundry_billing == 1) {
                $driver_payout = $order->order_status == 7  ? $order->final_amount_paid : $order->OrderTransaction->driver_total_payout_amount;
            } elseif ($order->Merchant->Configuration->laundry_billing == 2) {
                $driver_payout = $order->order_status == 7 ? $order->OrderTransaction->driver_total_payout_amount : $order->final_amount_paid;
            }

            $payment_status = false;
            if (($order->payment_method_id == 1 || $order->payment_method_id == 5) && $order->Merchant->Configuration->laundry_billing == 1) {
                $payment_status = ($order->order_status == 17 || $order->payment_status == 1) ? true : false;
            } else if (($order->payment_method_id == 1 || $order->payment_method_id == 5) && $order->Merchant->Configuration->laundry_billing == 2) {
                $payment_status = ($order->order_status == 7 || $order->payment_status == 1) ? true : false;
            } else {
                $payment_status = ($order->payment_status == 1 || $order->payment_method_id == 3);
            }


            if (($order->order_status == 7 || $order->order_status == 17) && !empty($order->id) && $order->is_order_completed != 1) {

                $service_list = $order->LaundryOutletOrderDetail;
                $service_data = [];
                foreach ($service_list as $service) {
                    $service_data[] = ['value' => $service->quantity . ' X ' . $service->Service->Name($order->merchant_id) . ' '];
                }
                $order_details = [
                    'order_id' => $order->id,
                    'order_number' => $order->merchant_order_id,
                    'order_items' => $service_data,
                ];
                $payment_details = [
                    'payment_method_id' => $order->PaymentMethod->id,
                    'payment_mode' => $order->PaymentMethod->MethodName($order->merchant_id) ? $order->PaymentMethod->MethodName($order->merchant_id) : $order->PaymentMethod->payment_method,
                    'payment_status' => $payment_status,
                    'order_price' => $order->CountryArea->Country->isoCode . ' ' . $merchant_helper->PriceFormat($driver_payout, $order->merchant_id),
                ];
                $customer_details[] = [
                    'customer_name' => $order->LaundryOutlet->full_name,
                    'customer_image' => "",
                    'customer_phone' => $order->LaundryOutlet->phone_number,
                ];
                $address_details[] = [
                    'value' => $order->drop_location,
                ];

                $data = [
                    'order_details' => $order_details,
                    'payment_details' => $payment_details,
                    'customer_details' => $customer_details,
                    'address_details' => $address_details,
                    'rating_mandatory' => false,
                ];
            } else {
                $string_file = $this->getStringFile($order->merchant_id);
                $message = trans("$string_file.order_not_found");
                throw new Exception($message);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $data;
    }


    public function updateLaundryOrderPaymentStatus(Request $request): bool
    {
        DB::beginTransaction();
        try {
            $request_status = $request->payment_status;
            $order = LaundryOutletOrder::Find($request->id);
            if (($order->order_status == 7 || $order->order_status == 17) && !empty($order->payment_status != 1)) {
                $order->payment_status = $request_status;
                $order->save();
                $order_transaction = $order->OrderTransaction;
                // In case of Cash Payment Method, Do the payment here
                if ($order->payment_method_id == 1) {
                    $payment = new Payment();
                    $currency = $order->CountryArea->Country->isoCode;
                    $array_param = array(
                        'laundry_outlet_order_id' => $order->id,
                        'payment_method_id' => $order->payment_method_id,
                        'amount' => $order->final_amount_paid,
                        'user_id' => $order->user_id,
                        'card_id' => $order->card_id,
                        'quantity' => $order->quantity,
                        'order_name' => $order->merchant_order_id,
                        'currency' => $currency,
                        'booking_transaction' => $order_transaction,
                        'driver_sc_account_id' => $order->Driver->sc_account_id
                    );
                    $payment->MakePayment($array_param);
                }
            } else {
                $string_file = $this->getStringFile($order->merchant_id);
                $message = trans("$string_file.order_not_found");
                throw new Exception($message);
            }
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }
        DB::commit();
        return true;
    }


    public function completeOrder(Request $request): array
    {
        $data = [];
        DB::beginTransaction();
        try {
            $order = LaundryOutletOrder::Find($request->booking_order_id);
            $string_file = $this->getStringFile($order->merchant_id);
            if (($order->order_status == 17 || $order->order_status == 7) && !empty($order->id)) {

                //in case of drop mark order as completed
                if ($order->order_status == 17) {
                    //                    $order->is_order_completed = 1;
                    //                    $order->save();

                    $this->saveDeliverOrderRequest($request, $order);
                    $this->NotifyUser($order);
                }

                // change driver status
                $driver = $request->user('api-driver');
                $driver->free_busy = 2; // driver is free now
                $driver->save();

                $message = trans('api.order_completed');

                // rate to user by driver
                BookingRating::updateOrCreate(
                    ['laundry_outlet_order_id' => $order->id],
                    [
                        'driver_rating_points' => $request->rating,
                        'driver_comment' => $request->comment
                    ]
                );
                $user_id = $order->user_id;
                $avg = BookingRating::whereHas('LaundryOutletOrder', function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                })->avg('driver_rating_points');
                $user = $order->User;
                $user->rating = round($avg, 2);
                $user->save();
            } else {
                $message = trans("$string_file.order_not_found");
                throw new Exception($message);
            }
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }
        DB::commit();
        $return_data['message'] = $message;
        $return_data['data'] = $data;
        return $return_data;
    }


    public function getOngoingOrders(Request $request): array|string
    {
        $data = [];
        $merchant_helper = new MerchantHelper();
        try {
            $driver = $request->user('api-driver');
            $merchant_id = $driver->merchant_id;
            $string_file = $this->getStringFile($merchant_id, $driver->Merchant);
            $order_string = trans("$string_file.order");
            $order_obj = new LaundryOutletOrder();
            $orders = $order_obj->getDriverLaundryOrders($request);
            foreach ($orders as $order) {
                $merchant_segment = $order->Segment->Merchant->where('id', $order->merchant_id);
                $merchant_segment = collect($merchant_segment->values());
                $order_info = [
                    'id' => $order->id,
                    'status' => $order->order_status,
                    'segment_name' => $order->Segment->Name($order->merchant_id) . ' ' . $order_string,
                    'segment_slug' => $order->Segment->slag,
                    'segment_group_id' => $order->Segment->segment_group_id,
                    'segment_sub_group' => $order->Segment->sub_group_for_app,
                    'number' => $order->merchant_order_id,
                    'master_booking_id' => $order->id,
                    'segment_service' => $order->ServiceType->ServiceName($order->merchant_id), //"Normal Food Delivery",
                    'time' => $order->order_timestamp,
                    'segment_image' => isset($merchant_segment[0]['pivot']->segment_icon) && !empty($merchant_segment[0]['pivot']->segment_icon) ? get_image($merchant_segment[0]['pivot']->segment_icon, 'segment', $order->merchant_id, true, false) : get_image($order->Segment->icon, 'segment_super_admin', NULL, false, false)
                ];
                $user_info = [
                    'user_name' => $order->User->first_name . ' ' . $order->User->last_name,
                    'user_image' => get_image($order->User->UserProfileImage, 'user', $order->merchant_id),
                    'user_phone' => $order->User->UserPhone ?? "",
                    'user_rating' => $order->User->rating ?? "0.00",
                ];
                $pickup = $order->LaundryOutlet;
                $pick_details = [
                    'lat' => $pickup->latitude,
                    'lng' => $pickup->longitude,
                    'address' => $pickup->address,
                    'icon' => view_config_image("static-images/pick-icon.png"),

                ];
                $drop_details = [
                    'lat' => $order->drop_latitude,
                    'lng' => $order->drop_longitude,
                    'address' => $order->drop_location,
                    'icon' => view_config_image("static-images/drop-icon.png"),
                ];
                $payment_details = [
                    'payment_mode' => $order->PaymentMethod->MethodName($merchant_id) ? $order->PaymentMethod->MethodName($merchant_id) : $order->PaymentMethod->payment_method,
                    'amount' => $order->CountryArea->Country->isoCode . ' ' . $merchant_helper->PriceFormat($order->final_amount_paid, $merchant_id),
                    'paid' => $order->payment_status == 1,
                ];
                $data[] = [
                    'info' => $order_info,
                    'user_info' => $user_info,
                    'pick_details' => $pick_details,
                    'drop_details' => $drop_details,
                    'payment_details' => $payment_details,
                ];
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            return $message;
        }
        return $data;
    }
}
