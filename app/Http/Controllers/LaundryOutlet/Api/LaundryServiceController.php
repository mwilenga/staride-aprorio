<?php

namespace App\Http\Controllers\LaundryOutlet\Api;

use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Controllers\Helper\HolderController;
use App\Http\Controllers\Helper\Merchant;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Http\Controllers\Merchant\PriceCardController;
use App\Models\BookingTransaction;
use App\Models\CountryArea;
use App\Models\LaundryOutlet\LaundryOutlet;
use App\Models\LaundryOutlet\LaundryOutletConfiguration;
use App\Models\LaundryOutlet\LaundryOutletOrder;
use App\Models\LaundryOutlet\LaundryOutletOrderDetail;
use App\Models\LaundryOutlet\LaundryService;
use App\Models\LaundryOutlet\LaundryServiceCart;
use App\Models\PriceCard;
use App\Models\ServiceTimeSlot;
use App\Models\UserAddress;
use App\Traits\ApiResponseTrait;
use App\Traits\AreaTrait;
use App\Traits\LaundryServiceTrait;
use App\Traits\MerchantTrait;
use App\Traits\OrderTrait;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use DateTimeZone;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LaundryServiceController extends Controller
{
    //
    use MerchantTrait, AreaTrait, ApiResponseTrait, OrderTrait, LaundryServiceTrait;

    /**
     * @throws Exception
     */
    public function saveServiceCart(Request $request): JsonResponse
    {
        $user = $request->user('api');
        $request_fields = [
            'segment_id' => "required_if:service_update,==,NO",
            'service_type_id' => "required_if:service_update,==,NO",
            'longitude' => 'required_if:service_update,==,NO',
            'latitude' => 'required_if:service_update,==,NO',
            'service_update' => 'required|in:YES,NO',
            'service_details' => 'required_if:service_update,==,NO',
            'cart_id' => 'required_if:service_update,==,YES',
            'quantity' => 'required_if:service_update,==,YES',

        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $merchant_id = $request->merchant_id;
            $string_file = $this->getStringFile($merchant_id);
            $this->getAreaByLatLong($request, $string_file);
            $id = $request->cart_id;
            if ($request->service_update == "YES" && !empty($id)) {
                $laundry_service_id = $request->laundry_service_id;
                $service_quantity = $request->quantity;
                $service_cart = LaundryServiceCart::where('id', $id)->first();

                if (empty($service_cart->id)) {
                    throw new Exception(trans("$string_file.cart_not_found"));
                }
                $service_details = $service_cart->service_details;
                $service_details = json_decode($service_details, true);
                $updated_services = [];
                foreach ($service_details as $service) {
                    $quantity = $service['laundry_service_id'] == $laundry_service_id ? $service_quantity : $service['quantity'];
                    $updated_services[] = ['laundry_service_id' => $service['laundry_service_id'], 'quantity' => $quantity];
                }
                $service_cart->service_details = json_encode($updated_services);
            } else {
                $segment_id = $request->segment_id;
                $service_type_id = $request->service_type_id;
                $country_area_id = $request->area;

                $price_card = PriceCard::where([['status', '=', 1], ['country_area_id', '=', $country_area_id], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $service_type_id], ['segment_id', '=', $segment_id], ['price_card_for', '=', 2]])->first();
                if (empty($price_card)) {
                    return $this->failedResponse(trans("$string_file.no_price_card_for_area"));
                }

                $merchant_id = $user->merchant_id;
                $user_id = $user->id;
                $service_cart = LaundryServiceCart::where('id', $id)->orWhere(function ($q) use ($user_id, $segment_id, $merchant_id) {
                    $q->where([['user_id', '=', $user_id], ['merchant_id', '=', $merchant_id], ['segment_id', '=', $segment_id]]);
                })->first();

                if (empty($service_cart->id)) {
                    $service_cart = new LaundryServiceCart();
                    $service_cart->user_id = $user_id;
                    $service_cart->merchant_id = $request->merchant_id;
                    $service_cart->segment_id = $request->segment_id;
                    $service_cart->laundry_outlet_id = $request->laundry_outlet_id;
                }
                // save cart data
                $service_cart->service_type_id = $request->service_type_id;
                $service_cart->service_details = $request->service_details;
                $service_cart->price_card_id = $price_card->id;
            }
            // return cart data
            $calling_from = "save_cart";
            $service_cart->save();
            $service_cart->area = $request->area;
            $return_cart = $this->getCartData($service_cart, false, NULL, $calling_from, $request);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.success"), $return_cart);
    }

    public function getCartData($service_cart, $find_by_cart_id = true, $promo_code = null, $calling_from = "", $request = NULL)
    {
        $area_id = isset($service_cart->area) ? $service_cart->area : NULL;
        $area_id = ($area_id == NULL && $request->area) ? $request->area : $area_id;
        if (isset($service_cart['area'])) {
            unset($service_cart['area']);
        }
        if ($find_by_cart_id == true) {
            $service_cart = LaundryServiceCart::Find($service_cart);
        }
        $merchant_helper = new Merchant();
        $trip_calculation_method = $service_cart->Merchant->Configuration->trip_calculation_method;
        $format_price = $service_cart->Merchant->Configuration->format_price;
        $arr_cart_service = json_decode($service_cart->service_details, true);
        $arr_service_id = array_column($arr_cart_service, 'laundry_service_id');
        $outletConfig = LaundryOutletConfiguration::where("laundry_outlet_id", $service_cart->laundry_outlet_id)->first();


        $arr_cart_service_list = array_column($arr_cart_service, NULL, 'laundry_service_id');
        $arr_service_list = LaundryService::select('id', 'price', 'merchant_id', 'laundry_outlet_id', 'status', 'service_image', 'service_cover_image')
            ->whereIn('id', $arr_service_id)->where([['delete', '=', NULL]])
            ->get();

        $total_cart_quantity = 0;
        $total_cart_amount = 0;
        $total_tax_amount = 0;
        $laundry_outlet_id = NULL;
        $arr_cart_service = [];
        $service_data = [];
        if (isset($area_id) && !empty($area_id)) {
            $country_area = CountryArea::find($area_id);
            $currency = isset($country_area->Country) ? $country_area->Country->isoCode : "";
        } else {
            $currency = isset($service_cart->User->Country) ? $service_cart->User->Country->isoCode : "";
        }
        $string_file = $this->getStringFile(NULL, $service_cart->User->Merchant);
        $total_product_discount = 0;

        foreach ($arr_service_list as $key => $service) {
            $service_price = $service->price;
            $service_discount = !empty($service->discount) && $service->discount > 0 ? $service->discount : 0;
            $merchant_id = $service->merchant_id;
            $laundry_outlet_id = $service->laundry_outlet_id;
            $quantity = (int)$arr_cart_service_list[$service->id]['quantity'];

            $service_image = $service->service_image ? get_image($service->service_image, 'laundry_service_image', $service->merchant_id) : "";
            $service_total_price = ($service_price - $service_discount) * $quantity;
            $discounted_price = !empty($service_discount) ? ($service_price - $service_discount) : "";

            $service_data['laundry_service_id'] = $service->id;
            $service_data['quantity'] = $quantity;
            $service_data['currency'] = "$currency";
            $service_data['service_price'] = $merchant_helper->PriceFormat($merchant_helper->TripCalculation($service_price, $merchant_id, $trip_calculation_method), $merchant_id, $format_price, $trip_calculation_method);
            $service_data['discount'] = $merchant_helper->PriceFormat($merchant_helper->TripCalculation($service_discount, $merchant_id, $trip_calculation_method), $merchant_id, $format_price, $trip_calculation_method);
            $service_data['discounted_price'] = !empty($discounted_price) ? $merchant_helper->PriceFormat($merchant_helper->TripCalculation($discounted_price, $merchant_id, $trip_calculation_method), $merchant_id, $format_price, $trip_calculation_method) : "";
            $service_data['total_price'] = $merchant_helper->TripCalculation($service_total_price, $merchant_id, $trip_calculation_method);
            $service_data['total_price_formatted'] = $merchant_helper->PriceFormat($merchant_helper->TripCalculation($service_total_price, $merchant_id, $trip_calculation_method), $merchant_id, $format_price, $trip_calculation_method);
            $service_data['service_name'] = $service->Name($service->merchant_id);
            $service_data['service_title'] = $service->Name($service->merchant_id);
            $service_data['service_status'] = $service->status;
            $service_data['service_cover_image'] = !empty($service->service_cover_image) ? get_image($service->service_cover_image, 'laundry_service_cover_image', $service->merchant_id) : $service_image;

            $arr_cart_service[] = $service_data;
            $total_cart_quantity += $quantity;
            $total_cart_amount += $service_total_price;
        }
        $tax_percentage = isset($service_cart->PriceCard->tax) ? $service_cart->PriceCard->tax : 0;

        $service_cart->laundry_outlet_id = $laundry_outlet_id;
        $service_cart->tax = ($total_cart_amount * $tax_percentage) / 100;
        $service_cart->cart_amount = (string)$total_cart_amount;

        $service_cart->save();
        $delivery_charge = 0;
        $service_type = 6; // self  pickup
        //drop_distance_from_restaurant
        if ($service_cart->ServiceType->type == 1) // home delivery and pickup
        {
            $service_type = $service_cart->ServiceType->type;
            $price_card_detail_id = NULL;
            if (!empty($request) && $calling_from != "delete_cart") {
                // in case of demo user order_pickup point will be same as user current location
                if ($service_cart->User->login_type == 1) {
                    $from = $request->latitude . ',' . $request->longitude;
                } else {
                    $pickup_lat = $service_cart->LaundryOutlet->latitude;
                    $pickup_long = $service_cart->LaundryOutlet->longitude;
                    $from = $pickup_lat . ',' . $pickup_long;
                }

                $to = $request->latitude . ',' . $request->longitude;
                $google_key = $service_cart->Merchant->BookingConfiguration->google_key;
                $units = ($service_cart->LaundryOutlet->CountryArea->Country['distance_unit'] == 1) ? 'metric' : 'imperial';
                $google_result = GoogleController::GoogleDistanceAndTime($from, $to, $google_key, $units, false, 'groceryCart', $string_file);
                // distance in meter
                $user_distance = isset($google_result['distance_in_meter']) ? $google_result['distance_in_meter'] : NULL;

                // distance in km
                $service_cart->estimate_distance = isset($google_result['distance']) ? $google_result['distance'] : NULL;

                $delivery_charge_slabs = $service_cart->PriceCard->PriceCardDetail->where('status', 1)->toArray();
                if (!empty($user_distance)) {
                    $request->merge(['for' => 2, 'distance' => $user_distance, 'cart_amount' => $total_cart_amount]);
                    $slab = $this->getDistanceSlab($request, $delivery_charge_slabs);
                    if (isset($slab['id']) && isset($slab['slab_amount'])) {
                        $delivery_charge = $slab['slab_amount'];
                        $price_card_detail_id = $slab['id'];
                    }
                }
                $service_cart->delivery_charge = $delivery_charge;
                $service_cart->save();
                // just for bill details
                $service_cart->price_card_detail_id = $price_card_detail_id;
            } else {
                $delivery_charge = $service_cart->delivery_charge;
            }
        }

        $discount_amount = 0;
        $promocode = "";
        if (!empty($promo_code->id)) {
            if ($promo_code->promoCode == "FIRSTDELFREE") {
                $discount_amount = $delivery_charge;
            } else {
                $promo_details = $promo_code;
                // flat discount promo_code_value_type ==1
                $discount_amount = $promo_details->promo_code_value;
                if ($promo_details->promo_code_value_type == 2) {
                    // percentage discount promo_code_value_type == 2
                    $promoMaxAmount = $promo_details->promo_percentage_maximum_discount;
                    $discount_amount = ($total_cart_amount * $discount_amount) / 100;
                    $discount_amount = !empty($promoMaxAmount) && ($discount_amount > $promoMaxAmount) ? $promoMaxAmount : $discount_amount;
                }
            }
            $promocode = $promo_code->promoCode;
        }

        $service_cart->tip_status = $service_cart->Merchant->ApplicationConfiguration->tip_status == 1;
        $tax_per = $service_cart->PriceCard->tax;
        if ($tax_per > 0) {
            $total_tax_amount = ($total_cart_amount * $tax_per / 100);
        }
        $time_charges = 0;
        $time_charges_enable = false;
        $time_charges_placeholder = "";
        if (isset($service_cart->Merchant->Configuration->user_time_charges) && $service_cart->Merchant->Configuration->user_time_charges == 1) {
            $time_charges_details = $service_cart->PriceCard->time_charges_details;
            if (!empty($time_charges_details)) {
                $time_charges_details = json_decode($time_charges_details, true);
                $now = DateTime::createFromFormat('H:i', date('H:i'));
                $begintime = DateTime::createFromFormat('H:i', $time_charges_details['time_from']);
                $endtime = DateTime::createFromFormat('H:i', $time_charges_details['time_to']);
                if ($begintime <= $now || $now <= $endtime) {
                    if ($time_charges_details['charges_type'] == 1) {
                        $time_charges = $time_charges_details['charges'];
                    } else {
                        $time_charges = ($total_cart_amount * $time_charges_details['charges'] / 100);
                    }
                    $time_charges_enable = true;
                    $time_charges_placeholder = $time_charges_details['charge_parameter'];
                }
            }
        }

        $total_amount = $merchant_helper->TripCalculation($total_cart_amount, $merchant_id, $trip_calculation_method);
        $discount_amount = $merchant_helper->TripCalculation($discount_amount, $merchant_id, $trip_calculation_method);
        $tax_amount = $merchant_helper->TripCalculation($total_tax_amount, $merchant_id, $trip_calculation_method);
        $delivery_charge = $merchant_helper->TripCalculation($delivery_charge, $merchant_id, $trip_calculation_method);

        $receipt_data = [
            'currency' => $currency,
            'quantity' => $total_cart_quantity,
            'tax' => round_number($total_tax_amount, 2),
            'total_amount' => $total_amount,
            'total_amount_formatted' => $merchant_helper->PriceFormat($total_amount, $merchant_id, $format_price, $trip_calculation_method),
            'discount_amount' => $merchant_helper->PriceFormat($discount_amount, $merchant_id, $format_price, $trip_calculation_method),
            'tax_amount' => $merchant_helper->PriceFormat($tax_amount, $merchant_id, $format_price, $trip_calculation_method),
            'delivery_charge' => $merchant_helper->PriceFormat($delivery_charge, $merchant_id, $format_price, $trip_calculation_method),
        ];

        if ($time_charges_enable) {
            $receipt_data['time_charges'] = (int)$time_charges;
        }
        // product discount and coupon discount
        $tip_amount = $request->tip_amount;
        $final_amount = ($total_cart_amount - $discount_amount) + $total_tax_amount + $delivery_charge + (int)$time_charges + (int)$tip_amount;
        $receipt_data['final_amount'] = $merchant_helper->TripCalculation($final_amount, $merchant_id, $trip_calculation_method);
        $receipt_data['final_amount_formatted'] = (string)$merchant_helper->PriceFormat($final_amount, $merchant_id, $format_price, $trip_calculation_method);
        $service_cart->time_charges_enable = $time_charges_enable;
        $service_cart->time_charges_placeholder = $time_charges_placeholder;
        $service_cart->receipt = $receipt_data;

        $cancel_minutes = 0;
        $cancel_charges = 0;
        $order_cancel_status = false;
        if (!empty($service_cart->price_card_id) && $service_cart->PriceCard->cancel_charges == 1) {
            $order_cancel_status = true;
            $cancel_minutes = $service_cart->PriceCard->cancel_time;
            $cancel_charges = $service_cart->PriceCard->cancel_amount;
        }
        $service_cart->cancel_text = [
            'cancel_order' => $order_cancel_status,
            'header' => trans("$string_file.cancel_text_header"),
            'body' => trans("$string_file.order_cancel_warning", ["time" => $cancel_minutes, "amount" => $cancel_charges])
        ];

        $outlet = LaundryOutlet::select('commission_method', 'commission', 'full_name', 'address')->find($laundry_outlet_id);
        $service_cart->service_details = $arr_cart_service;
        $paymentMethods = $service_cart->PriceCard->CountryArea->PaymentMethod;
        $bookingData = new BookingDataController();
        $options = $bookingData->PaymentOption($paymentMethods, $service_cart->user_id, null, $service_cart->PriceCard->minimum_wallet_amount);
        $service_cart->payment_method = $options;
        $service_cart->store_name = $outlet->full_name;
        $service_cart->store_address = $outlet->address;
        $service_cart->service_type = $service_type; // 1 home delivery, 6 self pickup
        $service_cart->promo_code_text = !empty($request->promo_code) ? trans("$string_file.apply_promo_applied") : trans("$string_file.apply_promo_code");
        $service_cart->active_promo_code = !empty($promocode);
        $service_cart->service_type = $service_type; // 1 home delivery, 6 self pickup
        $instant_order = $service_cart->Merchant->Configuration->instant_order;
        unset($service_cart->PriceCard);
        unset($service_cart->User);
        unset($service_cart->BusinessSegment);
        unset($service_cart->Merchant);
        unset($service_cart->created_at);
        unset($service_cart->updated_at);
        unset($service_cart->Segment);
        unset($service_cart->ServiceType);
        $request_data = (object)array(
            'driver_id' => NULL,
            'calling_from' => 'LAUNDRY_OUTLET',
            'merchant_id' => $service_cart->merchant_id,
            'segment_id' => $service_cart->segment_id,
            'area' => $area_id,
        );
        $slots =  ServiceTimeSlot::getServiceTimeSlot($request_data, $string_file);
        if (count($slots['time_slots']) == 0) {
            throw new \Exception(trans("$string_file.service_time_slots") . " " . trans("$string_file.not_exists"));
        }
        // $service_cart->estimate_process_days = $outletConfig->estimate_process_days;
        $service_cart->time_slot_details = $slots['time_slots'];
        $service_cart->instant_order = $instant_order == 1 ? true : false;
        return $service_cart;
    }

    public function deleteCart(Request $request): JsonResponse
    {
        $request_fields = [
            'delete_type' => 'required|in:CART,SERVICE',
            'cart_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $id = $request->cart_id; // product cart/checkout table
        $service_cart = LaundryServiceCart::where('id', $id)->first();
        $merchant_id = $service_cart->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $service_quantity = $request->service_quantity;
        $this->getAreaByLatLong($request, $string_file);
        if ($request->delete_type == 'CART') {
            $service_cart->delete();
            return $this->successResponse(trans("$string_file.cart_deleted"));
        } else {
            $laundry_service_id = $request->laundry_service_id;
            $service_details = $service_cart->service_details;
            $service_details = json_decode($service_details, true);
            $updated_services = [];
            foreach ($service_details as $service) {
                $quantity = $service['laundry_service_id'] == $laundry_service_id ? $service_quantity : $service['quantity'];
                if (!empty($quantity))
                    $updated_services[] = ['laundry_service_id' => $service['laundry_service_id'], 'quantity' => $quantity];
            }

            if (count($updated_services) == 0) {
                $service_cart->delete();
                return $this->successResponse(trans("$string_file.cart_deleted"));
            }
            $service_cart->service_details = json_encode($updated_services);
            $service_cart->save();
        }
        // return cart data
        $service_cart->area = $request->area;
        $return_cart = $this->getCartData($service_cart, false, "", "delete_cart", $request);
        return $this->successResponse(trans("$string_file.cart_product_deleted"), $return_cart);
    }

    public function getServiceCart(Request $request): JsonResponse
    {
        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL, $user->Merchant);
        $request_fields = [
            'cart_id' => ['required', 'integer', Rule::exists('laundry_service_carts', 'id')->where(function ($query) {}),],
            'latitude' => 'required',
            'longitude' => 'required',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $id = $request->cart_id;
        $return_cart = $this->getCartData($id, true, null, "", $request);
        return $this->successResponse(trans("$string_file.data_found"), $return_cart);
    }

    public function applyRemovePromoCode(Request $request)
    {
        $user = $request->user('api');
        $request_fields = [
            'cart_id' => ['required', 'integer', Rule::exists('laundry_service_carts', 'id')->where(function ($query) {}),],
            'segment_id' => ['required', 'integer', Rule::exists('segments', 'id')->where(function ($query) {}),],
            'latitude' => 'required',
            'longitude' => 'required',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $string_file = $this->getStringFile($user->merchant_id);
            $this->getAreaByLatLong($request, $string_file);


            $promocode = NULL;
            if (isset($request->promo_code) && !empty($request->promo_code)) {
                $cart = LaundryServiceCart::select('cart_amount', 'id')->find($request->cart_id);
                $request->merge(['order_amount' => $cart->cart_amount]);
                $common_controller = new CommonController();
                $check_promo_code = $common_controller->checkPromoCode($request);
                if (isset($check_promo_code['status']) && $check_promo_code['status']) {
                    $promocode = $check_promo_code['promo_code'];
                } else {
                    return $check_promo_code;
                }
            }
            $return_cart = $this->getCartData($request->cart_id, true, $promocode, "", $request, $string_file);
        } catch (Exception $e) {
            $message = $e->getMessage();
            // Rollback Transaction
            DB::rollback();
            return $this->failedResponse($message);
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.promo_code_applied"), $return_cart);
    }

    public function placeOrder(Request $request)
    {
        $user = $request->user('api');
        $request_fields = [
            'cart_id' => ['required', 'integer', Rule::exists('laundry_service_carts', 'id')->where(function ($query) {}),],
            'payment_method_id' => ['required', 'integer', Rule::exists('payment_methods', 'id')->where(function ($query) {}),],
            'segment_id' => ['required', 'integer', Rule::exists('segments', 'id')->where(function ($query) {}),],
            'address' => 'required',
            'service_time_slot_id' => 'required',
            'drop_date_time_slot' => 'required',
            'card_id' => 'required_if:payment_method_id,=,2',
            'service_type_id' => ['required', 'integer', Rule::exists('service_types', 'id')->where(function ($query) {}),],

        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        DB::beginTransaction();
        try {
            $string_file = $this->getStringFile($user->merchant_id);
            $this->getAreaByLatLong($request, $string_file);
            $promocode = $request->promo_code;
            $promo_code_id = NULL;
            if (!empty($request->promo_code)) {
                $common_controller = new CommonController();
                $check_promo_code = $common_controller->checkPromoCode($request);
                if (isset($check_promo_code['status']) && $check_promo_code['status'] == true) {
                    $promocode = $check_promo_code['promo_code'];
                    $promo_code_id = $promocode->id;
                } else {
                    return $check_promo_code;
                }
            }
            $return_cart = $this->getCartData($request->cart_id, true, $promocode, "", $request);
            $service_type_id = $request->service_type_id;
            if ($return_cart->ServiceType->type == 1) {
                // Check driver pricecard is exist or not
                $price_card_object = new PriceCardController();
                $check_for_driver = $price_card_object->checkFoodGroceryPriceCard("DRIVER", $request->merchant_id, $return_cart->segment_id, $request->area, $service_type_id); //can be used for laundry using segment id
                if (!$check_for_driver) {
                    throw new Exception(trans("$string_file.driver") . " " . trans("$string_file.price_card") . " " . trans("$string_file.data_not_found"));
                }
            }

            $order = new LaundryOutletOrder();
            $merchant_id = $request->merchant_id;
            $order->merchant_id = $merchant_id;
            $order->user_id = $user->id;
            $order->segment_id = $return_cart->segment_id;
            $order->order_status = 1; //order placed
            $order->country_area_id = $request->area;
            $order->payment_status = !empty($request->payment_status) ? $request->payment_status : 2;
            $order->service_type_id = $service_type_id;
            $cart_amount = $return_cart['receipt'];
            $order->promo_code_id = $promo_code_id;

            $final_amount = $cart_amount['final_amount'];
            if ($request->payment_method_id == 3) {
                $common_controller = new CommonController;
                $common_controller->checkUserWallet($user, $final_amount);
            }

            $laundry_image1 = !empty($request->laundry_image1) ? $this->uploadBase64Image('laundry_image1', 'laundry_order_items', $merchant_id) : "";
            $laundry_image2 = !empty($request->laundry_image2) ? $this->uploadBase64Image('laundry_image2', 'laundry_order_items', $merchant_id) : "";

            $order->order_item_images = json_encode([$laundry_image1, $laundry_image2]);
            $order->cart_amount = $cart_amount['total_amount'];
            $order->discount_amount = $cart_amount['discount_amount'];
            $order->tax = $cart_amount['tax'];
            $order->final_amount_paid = $final_amount;
            $order->delivery_amount = $cart_amount['delivery_charge'];
            $order->tip_amount = $request->tip_amount;
            $order->time_charges = isset($cart_amount['time_charges']) ? $cart_amount['time_charges'] : 0;

            $order->service_time_slot_id = $request->service_time_slot_id;
            $order->service_time_slot_detail_id = $request->service_time_slot_detail_id;

            $order->payment_method_id = $request->payment_method_id;
            $order->laundry_outlet_id = $return_cart->laundry_outlet_id;

            $order->price_card_id = $return_cart->price_card_id;

            $order->drop_latitude = $request->latitude;
            $order->drop_longitude = $request->longitude;
            $order->user_address_id = $request->user_address_id;
            $order->card_id = $request->card_id;

            $order->payment_option_id = $request->payment_option_id;
            $order->payment_status = !empty($request->payment_status) ? $request->payment_status : 2;


            // we should store drop location if get address id
            $drop_location = $request->address;
            if (empty($request->address)) {
                $drop_location = "";
                if (!empty($request->user_address_id)) {
                    $user_address = UserAddress::Find($request->user_address_id);
                    $drop_location = $user_address->house_name . ',' . $user_address->building . ',' . $user_address->address;
                }
            }

            $bs = $return_cart->LaundryOutlet;
            $order_date = !empty($request->order_date) ? Carbon::parse($request->order_date)->setTimeZone($bs->CountryArea->timezone)->format('Y-m-d') : Carbon::now()->setTimeZone($bs->CountryArea->timezone)->format('Y-m-d') ;
            // $order_date = !empty($request->order_date) ? Carbon::parse($request->order_date) : Carbon::now();

            $order->drop_location = $drop_location;
            $order->additional_notes = $request->additional_notes;
            $order->estimate_amount = 0;
            $order->order_timestamp = time();
            $order->order_date = $order_date;
            $order->quantity = $cart_amount['quantity'];
            $order->drop_date_time_slot = $request->drop_date_time_slot;

            $order->save();
            $this->saveLaundryOrderStatusHistory($request, $order);
            $arr_ordered_product = $return_cart->service_details;
            foreach ($arr_ordered_product as $service) {
                $service_obj = new LaundryOutletOrderDetail();
                $service_obj->laundry_outlet_order_id = $order->id;
                $service_obj->laundry_service_id = $service['laundry_service_id'];
                $service_obj->quantity = $service['quantity'];
                $service_obj->price = $service['service_price'];
                $service_obj->total_amount = $service['total_price'];
                $service_obj->save();
            }
            $message = trans("$string_file.order_placed");
            $service_cart = LaundryServiceCart::Find($request->cart_id);

            // send push notification to store and user
            $this->sendPushNotificationToLaundryOutlet($order);
            // $this->NotifyUser($order);

            $service_cart->delete();
        } catch (Exception $e) {
            // throw $e;
            return $this->failedResponse($e->getLine());
        }
        DB::commit();


        $drop_slot = json_decode($request->drop_date_time_slot);

        if ($drop_slot && isset($drop_slot->date, $drop_slot->from_time, $drop_slot->to_time)) {
            $drop_date_time = \Carbon\Carbon::parse($drop_slot->date)->format('d-m-Y') . ' ' .
                \Carbon\Carbon::parse($drop_slot->from_time)->format('h:i A') . ' - ' .
                \Carbon\Carbon::parse($drop_slot->to_time)->format('h:i A');
        } else {
            $drop_date_time = '';
        }


        $time_slot = ServiceTimeSlot::with(['ServiceTimeSlotDetail' => function ($query) use ($order) {
            $query->where('id', $order->service_time_slot_detail_id);
        }])->find($order->service_time_slot_id);

        if (count($time_slot->ServiceTimeSlotDetail) == 0) {
            $pickupDate = date('Y-m-d', strtotime("$order->order_date")) . ' ' . $time_slot->start_time;
            $dropDate = date('Y-m-d', strtotime("$pickupDate +1 days")) . ' ' . $time_slot->start_time;
            $data = ['laundry_outlet_order_id' => $order->id, 'order_status' => $order->order_status, "pickup_time" => $pickupDate, "drop_time" =>  $drop_date_time, "amount" => $order->final_amount_paid, "payment_method" => $order->PaymentMethod->payment_method];
            return $this->successResponse($message, $data);
        }

        $currentDayIndex = date('w', strtotime($order->order_date));
        if ($time_slot->day == $currentDayIndex) {
            $pickupDate = $order->order_date . ' ' . $time_slot->ServiceTimeSlotDetail[0]->slot_time_text;
            $dropDate = date('Y-m-d', strtotime("$order->order_date +2 days"));
        } else {
            $diff = ($time_slot->day - $currentDayIndex + 7) % 7;
            if ($diff == 0) {
                $diff = 7;
            }
            $pickup = date('Y-m-d', strtotime("$order->order_date +$diff days"));
            $pickupDate = $pickup . ' ' . $time_slot->ServiceTimeSlotDetail[0]->slot_time_text;
            $estimate_process_days = !empty($order->LaundryOutlet->LaundryOutletConfiguration) ? $order->LaundryOutlet->LaundryOutletConfiguration->estimate_process_days : 2;
            $dropDate = date('Y-m-d', strtotime("$pickup +$estimate_process_days days")) . ' ' . $time_slot->ServiceTimeSlotDetail[0]->slot_time_text;
        }
        $data = ['laundry_outlet_order_id' => $order->id, 'order_status' => $order->order_status, "pickup_time" => $pickupDate, "drop_time" => $drop_date_time, "amount" => $order->final_amount_paid, "payment_method" => $order->PaymentMethod->payment_method];

        return $this->successResponse($message, $data);
    }


    public function getOrders(Request $request): JsonResponse
    {
        $merchant_helper = new Merchant();
        $user = $request->user('api');
        $merchant_id = $user->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $req_param['merchant_id'] = $merchant_id;
        $config_status = $this->getLaundryOrderStatus($req_param);
        $currency = isset($user->Country) ? $user->Country->isoCode : "";
        $request_fields = [
            'type' => 'required', // 1 for schedule 2 ongoing 3 for past
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $user_id = $user->id;
            $order_status = [];
            if ($request->type == 2) {
                $order_status = [1, 6, 7, 9, 10, 13, 15, 16];
            } elseif ($request->type == 3) {
                $order_status = [2, 5, 8, 14, 17];
            }
            $order_status[] = 20;
            $orders = LaundryOutletOrder::select('laundry_outlet_id', 'country_area_id', 'merchant_id', 'id', 'merchant_order_id', 'payment_method_id', 'final_amount_paid', 'discount_amount', 'created_at', 'order_status', 'quantity', 'order_date', 'estimate_delivery_time', 'otp_for_pickup')
                ->with(['LaundryOutlet' => function ($q) {
                    $q->addSelect('id', 'full_name', 'business_logo', 'address');
                }])
                ->with(['PaymentMethod' => function ($q) {
                    $q->addSelect('id', 'payment_method');
                }])
                ->whereIn('order_status', $order_status)
                ->where([['user_id', '=', $user_id]])
                ->orderBy('created_at', 'DESC')
                ->get();

            $orders = $orders->map(function ($order, $key) use ($currency, $config_status, $string_file, $merchant_helper) {
                $merchant_id = $order->merchant_id;
                $date = new DateTime($order->created_at);
                $date->setTimezone(new DateTimeZone($order->CountryArea->timezone));

                return [
                    'laundry_outlet_order_id' => $order->id,
                    'outlet_name' => $order->LaundryOutlet->full_name,
                    'outlet_address' => $order->LaundryOutlet->address,
                    'outlet_logo' => get_image($order->LaundryOutlet->business_logo, 'business_logo', $merchant_id),
                    'order_date' => trans("$string_file.placed_at") . ' ' . $date->format('H:i D, d-m-Y'),
                    'deliver_on' => trans("$string_file.deliver_on") . ' ' . date('d-m-Y', strtotime($order->order_date)),
                    'total_items' => $order->quantity,
                    'currency' => "$currency",
                    'total_amount' => $merchant_helper->PriceFormat($order->final_amount_paid, $merchant_id),
                    'discount_amount' => (!empty($order->discount_amount)) ? $merchant_helper->PriceFormat($order->discount_amount, $merchant_id) : $order->discount_amount,
                    'order_status' => $config_status[$order->order_status],
                    'otp' => $order->otp_for_pickup,
                    'estimated_delivery_time' => carbon::parse($order->estimate_delivery_time)->format("Y-m-d H:i a"),
                ];
            });
            return $this->successResponse(trans("$string_file.data_found"), $orders);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
    }


    public function getOrderDetails(Request $request): JsonResponse
    {
        $merchant_helper = new Merchant();
        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL, $user->Merchant);
        $merchant_id = $user->merchant_id;
        $req_param['merchant_id'] = $merchant_id;
        $config_status = $this->getLaundryOrderStatus($req_param);
        $currency = isset($user->Country) ? $user->Country->isoCode : "";
        $request_fields = [
            'laundry_outlet_order_id' => ['required', 'integer', Rule::exists('laundry_outlet_orders', 'id')->where(function ($query) {})],
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $order = LaundryOutletOrder::select('laundry_outlet_id', 'country_area_id', 'price_card_id', 'merchant_id', 'id', 'merchant_order_id', 'drop_location', 'user_address_id', 'payment_method_id', 'promo_code_id', 'final_amount_paid', 'tax', 'discount_amount', 'cart_amount', 'delay_date_time', 'delivery_amount', 'created_at', 'order_status', 'quantity', 'order_status_history', 'otp_for_pickup', 'estimate_distance', 'service_type_id', 'otp_for_pickup', 'tip_amount', 'driver_id', 'drop_driver_id', 'drop_date_time_slot', 'estimate_delivery_time')
                ->with(['LaundryOutlet' => function ($q) {
                    $q->addSelect('id', 'full_name', 'address');
                }])
                ->with(['LaundryOutletOrderDetail' => function ($q) {
                    $q->addSelect('id', 'order_id', 'service_id', 'quantity', 'price', 'discount', 'total_amount');
                }])
                ->with(['LaundryOutletOrderDetail.Service' => function ($q) {}])
                ->with(['PaymentMethod' => function ($q) {
                    $q->addSelect('id', 'payment_method');
                }])
                ->where('id', $request->laundry_outlet_order_id)
                ->first();

            $time_charges_enable = false;
            $time_charges = "";
            $time_charges_placeholder = "";
            if (isset($user->Merchant->Configuration->user_time_charges) && $user->Merchant->Configuration->user_time_charges == 1) {
                $time_charges_enable = true;
            }
            if ($time_charges_enable && !empty($order->time_charges)) {
                $time_charges = $order->time_charges;
                $time_charges_enable = true;
                $time_charges_details = json_decode($order->PriceCard->time_charges_details, true);
                $time_charges_placeholder = $time_charges_details['charge_parameter'];
            } else {
                $time_charges_enable = false;
            }

            $order_cancel_status = false;
            $cancel_minutes = 0;
            $cancel_charges = 0;
            $order_eligible_for_cancel = [1];


            if (in_array($order->order_status, $order_eligible_for_cancel) && $order->order_status != 2) {
                if (isset($order->PriceCard) && $order->PriceCard->cancel_charges == 1 && $order->payment_method_id == 1) {
                    $order_cancel_status = true;
                    $cancel_minutes = $order->PriceCard->cancel_time;
                    $cancel_charges = $order->PriceCard->cancel_amount;
                }
            }

            $date = new DateTime($order->order_date);
            $date->setTimezone(new DateTimeZone($order->CountryArea->timezone));

            $merchant_id = $order->merchant_id;
            $service_data = $order->LaundryOutletOrderDetail->map(function ($service, $key) use ($merchant_id, $currency, $merchant_helper) {
                $req_param['merchant_id'] = $merchant_id;
                return [
                    'title' => $service->Service->Name($merchant_id),
                    'image' => get_image($service->service_cover_image, 'laundry_service_cover_image', $merchant_id),
                    'quantity' => $service->quantity,
                    'total_price' => $merchant_helper->PriceFormat(round_number($service->price), $merchant_id),
                    'service_price' => $merchant_helper->PriceFormat(round_number($service->price), $merchant_id),
                    'total_service_price' => $merchant_helper->PriceFormat(round_number($service->quantity * round_number($service->price)), $merchant_id),
                ];
            });

            $cart_amount = round_number($order->cart_amount);
            $receipt = [
                'cart_amount' => $merchant_helper->PriceFormat($cart_amount, $merchant_id),
                'tax' => !empty($order->tax) ? $merchant_helper->PriceFormat($order->tax, $merchant_id) : "",
                'time_charges' => "$time_charges",
                'tip_amount' => !empty($order->tip_amount) ? (string)$order->tip_amount : "",
                'delivery_amount' => !empty($order->delivery_amount) ? $merchant_helper->PriceFormat($order->delivery_amount, $merchant_id) : "",
                'discount_amount' => (!empty($order->discount_amount)) ? $merchant_helper->PriceFormat($order->discount_amount, $merchant_id) : "$order->discount_amount",
                'total_amount' => $merchant_helper->PriceFormat($order->final_amount_paid, $merchant_id),

            ];


            $drop_slot = json_decode($order->drop_date_time_slot);

            if ($order->delay_date_time) {
                $drop_date_time = \Carbon\Carbon::parse($order->delay_date_time)->format('d-m-Y h:i A');
            } else {
                if ($drop_slot && isset($drop_slot->date, $drop_slot->from_time, $drop_slot->to_time)) {
                    $drop_date_time = \Carbon\Carbon::parse($drop_slot->date)->format('d-m-Y') . ' ' .
                        \Carbon\Carbon::parse($drop_slot->from_time)->format('h:i A') . ' - ' .
                        \Carbon\Carbon::parse($drop_slot->to_time)->format('h:i A');
                } else {
                    $drop_date_time = '';
                }
            }


            $order_details = [
                'order_id' => $order->id,
                'order_no' => $order->merchant_order_id,
                'pickup' => $order->LaundryOutlet->address,
                'drop_off' => $order->drop_location,
                'order_date' => $date->format('H:i, d-m-Y'),
                'total_items' => $order->quantity,
                'currency' => "$currency",
                'order_status' => $config_status[$order->order_status],
                'service_data' => $service_data,
                'time_charges_enable' => $time_charges_enable,
                'time_charges_placeholder' => $time_charges_placeholder,
                'otp' => !empty($order->otp_for_pickup) ? $order->otp_for_pickup : "",

                'store_name' => $order->LaundryOutlet->full_name,
                'order_on' => Carbon::parse($order->order_timestamp)->format('jS M, Y'),
                'estimate_delivery_time' => $drop_date_time,
                'status_text' => $this->getLaundryOrderStatus($req_param)[$order->order_status],
                'payment_method' => $order->PaymentMethod->payment_method,
                'total_amount' => $order->final_amount_paid,
                'distance' => !empty($order->estimate_distance) ? $order->estimate_distance : "",

                'cancel_receipt' => HolderController::userOrderCancelHolder($order, $string_file),
                'arr_action' => [
                    'tracking' => in_array($order->order_status, [1]),
                    'cancel_order' => $order_cancel_status,
                    'cancel_text' => trans("$string_file.order_cancel_warning", ["time" => $cancel_minutes, "amount" => (isset($order->CountryArea->Country) ? $order->CountryArea->Country->isoCode : "") . " " . $cancel_charges])
                ],
                'additional_details' => $this->LaundryServiceStatus($order),
                'price_summary' => $this->LaundryPriceDetailHolderArray($receipt),
            ];


            return $this->successResponse(trans("$string_file.data_found"), $order_details);
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
    }


    public function userCancelOrder(Request $request)
    {
        $user = $request->user('api');
        $request_fields = [
            'laundry_outlet_order_id' => ['required', 'integer', Rule::exists('laundry_outlet_orders', 'id')->where(function ($query) {}),],
            'latitude' => 'required',
            'longitude' => 'required',
            'cancel_reason_id' => 'required'
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $string_file = $this->getStringFile($user->merchant_id);
        DB::beginTransaction();
        try {
            $order = LaundryOutletOrder::find($request->laundry_outlet_order_id);
            if ($order->payment_method_id == 1) {
                $price_card = $order->PriceCard;
                $cancel_charges_amount = 0;
                if ($price_card->cancel_charges == 1) {
                    $order_eligible_for_cancel = [1];
                    $cancel_charges_amount = $price_card->cancel_amount;
                    $status_history = json_decode($order->order_status_history, true);
                    if (in_array($order->order_status, $order_eligible_for_cancel) && $order->order_status != 2) {
                        $order_status_timestamp = "";
                        foreach ($status_history as $status_hst) {
                            if ($status_hst['order_status'] == 1) {
                                $order_status_timestamp = $status_hst['order_timestamp'];
                                break;
                            }
                        }
                        if (!empty($order_status_timestamp)) {
                            $till_cancel_time = date("Y-m-d H:i:s", $order_status_timestamp);
                            $till_cancel_time = new DateTime($till_cancel_time);
                            $till_cancel_time->add(new DateInterval('PT' . $order->PriceCard->cancel_time . 'M'));
                            $till_cancel_time = $till_cancel_time->format('Y-m-d H:i:s');
                            $till_cancel_time = convertTimeToUSERzone($till_cancel_time, $order->CountryArea->timezone, null, $order->Merchant, 1, 1);
                        } else {
                            $till_cancel_time = convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone, null, $order->Merchant, 1, 1);
                        }
                        $order->order_status = 2;
                        $order->cancel_reason_id = $request->cancel_reason_id;
                        $order->save();
                        $this->saveOrderStatusHistory($request, $order);

                        // Send notification to restro
                        $data = array('order_id' => $order->id, 'notification_type' => 'ORDER_CANCELLED', 'segment_type' => $order->Segment->slag);
                        $arr_param = array(
                            'laundry_outlet_id' => $order->laundry_outlet_id,
                            'data' => $data,
                            'message' => trans("$string_file.order_cancelled_message"),
                            'merchant_id' => $order->merchant_id,
                            'title' => trans("$string_file.order_cancelled")
                        );
                        $this->sendPushNotificationToLaundryOutlet($order, null, "CANCEL_ORDER");

                        if (!empty($order->driver_id)) {
                            $order->Driver->free_busy = 2;
                            $order->Driver->save();
                            // Send notification to driver
                            $request->request->add(['notification_type' => 'ORDER_CANCELLED']);
                            $arr_driver_id = [$order->driver_id];
                            $this->NotifyDriver($request, $arr_driver_id, $order);
                        }

                        $current_time = convertTimeToUSERzone(date("Y-m-d H:i:s"), $order->CountryArea->timezone, null, $order->Merchant, 1, 1);
                        $apply_cancel_charges = false;
                        $driver_received_amount = 0;
                        $outlet_received_amount = 0;
                        if ($current_time > $till_cancel_time) {
                            $apply_cancel_charges = true;
                            $outlert = LaundryOutlet::select('id', 'segment_id', 'merchant_id', 'latitude', 'longitude')->Find($order->laundry_outlet_id);
                            $paramArray = array(
                                'user_id' => $user->id,
                                'order_id' => $order->id,
                                'amount' => $cancel_charges_amount,
                                'narration' => 15,
                                'platform' => 2,
                                'payment_method' => 1,
                            );
                            WalletTransaction::UserWalletDebit($paramArray);
                            if (!empty($order->driver_id)) {
                                $paramArray = array(
                                    'driver_id' => $order->driver_id,
                                    'order_id' => $order->id,
                                    'amount' => $cancel_charges_amount,
                                    'narration' => 19
                                );
                                WalletTransaction::WalletCredit($paramArray);
                                $driver_received_amount = $cancel_charges_amount;
                            } else {
                                $paramArray = array(
                                    'laundry_outlet_id' => $order->laundry_outlet_id,
                                    'order_id' => $order->id,
                                    'amount' => $cancel_charges_amount,
                                    'narration' => 2,
                                );
                                WalletTransaction::LaundryOutletWalletCredit($paramArray);
                                $outlet_received_amount = $cancel_charges_amount;
                            }
                        }
                        $merchant = new Merchant();
                        BookingTransaction::updateOrCreate(
                            [
                                'laundry_outlet_order_id' => $order->id,
                            ],
                            [
                                'date_time_details' => date('Y-m-d H:i:s'),
                                'sub_total_before_discount' => $merchant->TripCalculation('0.0', $order->merchant_id),
                                'surge_amount' => $merchant->TripCalculation('0.0', $order->merchant_id),
                                'extra_charges' => $merchant->TripCalculation('0.0', $order->merchant_id),
                                'discount_amount' => $merchant->TripCalculation('0.0', $order->merchant_id),
                                'tax_amount' => $merchant->TripCalculation('0.0', $order->merchant_id),
                                'tip' => $merchant->TripCalculation('0.0', $order->merchant_id),
                                'insurance_amount' => $merchant->TripCalculation('0.0', $order->merchant_id),
                                'cancellation_charge_received' => $merchant->TripCalculation('0.0', $order->merchant_id),
                                'cancellation_charge_applied' => $merchant->TripCalculation(($apply_cancel_charges) ? $cancel_charges_amount : "0.0", $order->merchant_id),
                                'toll_amount' => $merchant->TripCalculation('0.0', $order->merchant_id),
                                'cash_payment' => $merchant->TripCalculation('0.0', $order->merchant_id),
                                'online_payment' => $merchant->TripCalculation(($apply_cancel_charges) ? $cancel_charges_amount : "0.0", $order->merchant_id),
                                'customer_paid_amount' => $merchant->TripCalculation(($apply_cancel_charges) ? $cancel_charges_amount : "0.0", $order->merchant_id),
                                'company_earning' => $merchant->TripCalculation('0.0', $order->merchant_id),
                                'driver_earning' => $merchant->TripCalculation(($apply_cancel_charges) ? $driver_received_amount : "0.0", $order->merchant_id),
                                'amount_deducted_from_driver_wallet' => $merchant->TripCalculation('0.0', $order->merchant_id),
                                'driver_total_payout_amount' => $merchant->TripCalculation(($apply_cancel_charges) ? $driver_received_amount : "0.0", $order->merchant_id),
                                'trip_outstanding_amount' => $merchant->TripCalculation('0.0', $order->merchant_id),
                                'laundry_outlet_earning' => $merchant->TripCalculation(($apply_cancel_charges) ? $outlet_received_amount : "0.0", $order->merchant_id),
                                'laundry_outlet_total_payout_amount' => $merchant->TripCalculation(($apply_cancel_charges) ? $outlet_received_amount : "0.0", $order->merchant_id),
                                'company_gross_total' => $merchant->TripCalculation('0.0', $order->merchant_id),
                                'merchant_id' => $order->merchant_id
                            ]
                        );
                    } else {
                        // Not able to cancel
                        throw new Exception(trans("$string_file.your_order_in_progress_can_not_cancel"));
                    }
                } else {
                    // Cancel order is disable
                    throw new Exception(trans("$string_file.your_order_in_progress_can_not_cancel"));
                }
            } else {
                // Other than cash payment method
                throw new Exception(trans("$string_file.for_selected_payment_method_cancellation_is_not_applicable"));
            }
        } catch (Exception $e) {
            DB::rollback();
            // p($e->getTraceAsString());
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.order") . " " . trans("$string_file.cancelled") . " " . trans("$string_file.successfully"));
    }


    public function dropSlots(Request $request)
    {
        $user = $request->user('api');
        $request_fields = [
            'laundry_outlet_id' => 'required|exists:laundry_outlets,id',
            'pickup_date' => 'required|date_format:Y-m-d|after_or_equal:today',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        try {

            $laundry_outlet = LaundryOutlet::find($request->laundry_outlet_id);
            if ($laundry_outlet) {
                $merchant_id = $laundry_outlet->merchant_id;

                $request_data = (object)array(
                    'driver_id' => NULL,
                    'calling_from' => 'LAUNDRY_OUTLET',
                    'merchant_id' => $merchant_id,
                    'segment_id' => $laundry_outlet->segment_id,
                    'area' =>  $laundry_outlet->country_area_id,
                );


                $string_file = $this->getStringFile($merchant_id);
                $estimate_process_days = LaundryOutletConfiguration::where("laundry_outlet_id", $request->laundry_outlet_id)->first()->estimate_process_days;
                $slots =  ServiceTimeSlot::getServiceTimeSlot($request_data, $string_file);
                if (count($slots['time_slots']) == 0) {
                    throw new \Exception(trans("$string_file.service_time_slots") . " " . trans("$string_file.not_exists"));
                }

                $data = collect($slots['time_slots'])->map(function ($slot) use ($estimate_process_days, $request) {
                    $pickupDate = Carbon::parse($request->pickup_date);
                    $startDate = $pickupDate->copy()->addDays($estimate_process_days);

                    $dayDifference = $slot['day'] - $startDate->dayOfWeek;

                    $slotDate = $startDate->copy()->addDays($dayDifference >= 0 ? $dayDifference : $dayDifference + 7)->format('Y-m-d');

                    $slot['date'] = $slotDate;

                    return $slot;
                })->sortBy('date')->values()->all();
                return $this->successResponse(trans("$string_file.data_fatched."), $data);
            }
        } catch (Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
    }
}
