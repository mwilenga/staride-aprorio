<?php

namespace App\Http\Controllers\LaundryOutlet\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\GoogleController;
use App\Models\Category;
use App\Models\LaundryOutlet\LaundryOutletConfiguration;
use App\Models\LaundryOutlet\LaundryService;
use App\Traits\ApiResponseTrait;
use App\Traits\BannerTrait;
use App\Traits\LaundryServiceTrait;
use App\Traits\MerchantTrait;
use App\Traits\AreaTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LaundryOutletController extends Controller
{
    //
    use MerchantTrait, AreaTrait, ApiResponseTrait, BannerTrait, LaundryServiceTrait;
    public function homeScreen(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'segment_id' => ['required', 'integer', Rule::exists('segments', 'id')->where(function ($query) {
            }),],
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            // call area trait to get id of area
            $user = $request->user('api');
            $string_file = $this->getStringFile(NULL, $user->Merchant);
            $this->getAreaByLatLong($request, $string_file);
            $request->merge(['calling_from' => "home_screen"]);
            $data = $this->homeCategoryScreen($request);
            $return_data['response_data'] = $data['data'];
            $multi_store = $data['status'] == "multi_store" ? true : false;
            $return_data['multi_store'] = $multi_store;
            $return_data['is_favourite'] = false;
            $return_data['laundry_outlet_id'] = $data['laundry_outlet_id'];
            $return_data['next_page_url'] = $data['next_page_url'];
            $return_data['total_pages'] = $data['total_pages'];
            $return_data['current_page'] = $data['current_page'];
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }

        return $this->successResponse(trans("$string_file.data_found"), $return_data);
    }


    public function getCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'segment_id' => ['required', 'integer', Rule::exists('segments', 'id')->where(function ($query) {
            }),],
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL, $user->Merchant);
        if (!empty($request->latitude) && !empty($request->longitude)) {
            $this->getAreaByLatLong($request, $string_file);
        }
        $request->merge(['calling_from' => "category_screen"]);
        $data = $this->homeCategoryScreen($request);
        $return_data['response_data'] = $data['data'];
//        $fav = FavouriteBusinessSegment::select('id')->where([['user_id', '=', $user->id], ['segment_id', '=', $request->segment_id], ['business_segment_id', '=', $request->business_segment_id]])->first();
//        $return_data['if_fav'] = !empty($fav->id) ? true : false;

        return $this->successResponse(trans("$string_file.data_found"), $return_data);

    }


    public function categoryServices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'segment_id' => ['required', 'integer', Rule::exists('segments', 'id')->where(function ($query) {
            }),],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->where(function ($query) {
            }),],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $request->merge(['return_type' => 'modified_array', 'pagination' => false]);
        $products = $this->getLaundryServices($request);
        $string_file = $this->getStringFile($request->merchant_id);
        return $this->successResponse(trans("$string_file.data_found"), $products);
    }

    function homeCategoryScreen($request)
    {
        $user = $request->user('api');
        $is_search = isset($request->is_search) ? $request->is_search : NULL;
        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $segment_id = $request->segment_id;
        $calling_from = $request->calling_from;
        $response = "";
        $laundry_outlet_id = NULL;
        $outlets = 0;
        $arr_popular_outlets = [];

        $config = $user->Merchant->Configuration;

//        if ((isset($config->category_type_view) && $config->category_type_view == 1)) {
//            $category_type_view_config = true;
//        }
        if (empty($request->laundry_outlet_id)) {
            $distance = $user->Merchant->BookingConfiguration->store_radius_from_user;
            $request->merge(['distance' => $distance]);

            $arr_laundry_outlets = $this->getMerchantLaundryOutlets($request);

            // GET POPULAR STORES
            $request->merge(['is_popular' => "YES"]);
            $arr_popular_outlets = $this->getMerchantLaundryOutlets($request);

            $request->merge(['laundry_outlet_id' => array_pluck($arr_laundry_outlets, 'id')]);
            $outlets = $arr_laundry_outlets->count();
        }

        if ($calling_from == "home_screen") {
            if ($outlets > 1 || $is_search == 1 || ($request->page > 1)) {
                // Multi-store response
                $holder_item = ["BANNER", "STORE", "POPULAR_STORE"];
                $response = "multi_store";
            } else {
                 $user = $request->user('api');
            $string_file = $this->getStringFile(NULL, $user->Merchant);
            $unit = isset($user->Country) ? $user->Country->distance_unit : "";
           $google = new GoogleController;
            $user_lat = $request->latitude;
            $user_long = $request->longitude;
            $user = $request->user('api');
            $string_file = $this->getStringFile(NULL, $user->Merchant);
            $unit = isset($user->Country) ? $user->Country->distance_unit : "";
            $demo = $user->Merchant->demo;
            $google_key = $user->Merchant->BookingConfiguration->google_key;
                // Single-store response
                $holder_item = ["BANNER", "CATEGORY", "SERVICE"];
                $response = "single_store";


                date_default_timezone_set($arr_laundry_outlets[0]->CountryArea['timezone']);
                $current_time = date('H:i');
                $is_laundry_outlet_open = false;
                $current_day = date('w');
                $arr_open_time = json_decode($arr_laundry_outlets[0]->open_time, true);
                $arr_close_time = json_decode($arr_laundry_outlets[0]->close_time, true);
                $open_time = isset($arr_open_time[$current_day]) ? $arr_open_time[$current_day] : NULL;
                $close_time = isset($arr_close_time[$current_day]) ? $arr_close_time[$current_day] : NULL;
                
              
                
                 //  Changes for midnight store time
                if ($open_time > $close_time) {
                    $close_time_n = date('Y-m-d H:i:s', strtotime($close_time . ' +1 day'));
                } else {
                    $close_time_n = date('Y-m-d H:i:s', strtotime($close_time));
                }
                $open_time_n = date('Y-m-d H:i:s', strtotime($open_time));
                $current_time_n = date("Y-m-d H:i:s");

                if ($open_time_n < $current_time_n && $close_time_n > $current_time_n) {
                    $is_laundry_outlet_open = true;
                }

                $is_open_from_admin = false;
                $admin_text = trans("$string_file.close");
                $outletConfig = LaundryOutletConfiguration::where('laundry_outlet_id',  $arr_laundry_outlets[0]->id)->first();
                if(!empty($outletConfig)){
                    $is_open_from_admin = $outletConfig->is_open == 1 ? true : false;
                    $admin_text = $outletConfig->is_open == 1 ?? trans("$string_file.open");
                }

                $outlet_lat = $arr_laundry_outlets[0]->latitude;
                $outlet_long = $arr_laundry_outlets[0]->longitude;
                if ($demo ==  1) {
                    $distance_from_user = $google->arialDistance($user_lat, $user_long, $outlet_lat, $outlet_long, $unit, $string_file);
                } else {
                    $user_drop_location[0] = [
                        'drop_latitude' => $user_lat,
                        'drop_longitude' => $user_long,
                        'drop_location' => ""
                    ];
                    $distance_from_user = GoogleController::GoogleStaticImageAndDistance($outlet_lat, $outlet_long, $user_drop_location, $google_key, "", $string_file);
                    $distance_from_user = $distance_from_user['total_distance_text'] ?? "";
                }

                // Prepare categories with subcategories
                $arr_categories = $this->getLaundryOutletCategories($request);
                $category_res['cell_title'] = 'CATEGORY_CELL';
               $category_res['cell_contents'] = $arr_categories->map(function ($category) use (
    $request, $arr_laundry_outlets, $open_time, $close_time, $merchant_id,
    $is_laundry_outlet_open, $distance_from_user, $is_open_from_admin, $admin_text, $string_file
) {
    $sub_cats = Category::where('category_parent_id', $category['id'])
        ->where('status', 1)
        ->whereNull('delete')
        ->get()
        ->map(function ($sub_cat) use ($request) {
            return [
                'id' => $sub_cat->id,
                'category_parent_id' => $sub_cat->category_parent_id,
                'category_name' => $sub_cat->Name($request->merchant_id),
            ];
        });

    return [
        "id" => $category['id'],
        "laundry_outlet_id" => $arr_laundry_outlets[0]->id,
        "segment_id" => $arr_laundry_outlets[0]->segment_id,
        "title" => $arr_laundry_outlets[0]->full_name,
        "description" => '',
        "rating" => $arr_laundry_outlets[0]->rating,
        "open_time" => $open_time,
        "close_time" => $close_time,
        "image" => get_image($arr_laundry_outlets[0]->business_logo, 'laundry_outlet_logo', $merchant_id),
        "is_outlet_open" => $is_laundry_outlet_open,
        "is_admin_outlet_open" => $is_open_from_admin,
        "admin_store_text_from_admin" => $admin_text,
        "distance" => trans("$string_file.distance") . ' ' . $distance_from_user,
        "promo_code" => $arr_laundry_outlets[0]->ActivePromoCode ?: (object)[],
        "sub_categories" => $sub_cats,
    ];
});

              

                // Prepare the final response
                $return_data = [
                    [
                        "cell_title" => "BANNER_CELL",
                        "cell_contents" => [],
                    ],
                    [
                        "cell_title" => "TITLE",
                        "cell_contents" => [
                            ["title" => trans("$string_file.all_categories")],
                        ],
                    ],
                    $category_res,
                    [
                        "cell_title" => "PRODUCT_CELL",
                        "cell_contents" => [],
                    ],
                ];

                $return = [
                    "next_page_url" => $next_page_url ?? "",
                    "total_pages" => $total_pages ?? 1,
                    "current_page" => $current_page ?? 1,
                    "data" => $return_data,
                    "status" => $response,
                    "laundry_outlet_id" => $laundry_outlet_id,
                    
                    
                    
                ];
                
                
                return $return;
            }
        } elseif ($calling_from == "category_screen") {
            // if store is multiple then we have to open next screen as category of store
            $holder_item = ["BANNER", "CATEGORY_WITH_SUBCATEGORY"]; // category_screen
            $response = "category_screen";
        }

        $banner_res = [];
        if ($user->Merchant->advertisement_module == 1 && in_array("BANNER", $holder_item) && $is_search != 1) {
            $request->merge(['merchant_id' => $merchant_id, 'home_screen' => NULL, 'segment_id' => $segment_id, 'banner_for' => 1]);
            // made this changes because in db banner does not have business_segment_id value
            $arr_outlets = [];
            if (is_array($request->laundry_outlet_id)) {
                $arr_outlets = $request->laundry_outlet_id;
                $request->merge(['laundry_outlet_id' => NULL]);
            }
            $arr_banner = $this->getMerchantBanner($request); //all banners related to laundry segment
            if (!empty($arr_outlets)) {
                $request->merge(['laundry_outlet_id' => $arr_outlets]);
            }
            if (isset($request->laundry_outlet_id) && !empty($request->laundry_outlet_id)) {
                $outlet_arr_banner = $arr_banner->whereIn('laundry_outlet_id', $request->laundry_outlet_id);
                if ($outlet_arr_banner->count() > 0) {
                    $arr_banner = $outlet_arr_banner;
                } else {
                    $arr_banner = $arr_banner->where('laundry_outlet_id', '=', NULL);
                }
                $arr_banner = $arr_banner->values();
            }
            //p($arr_banner);
            $banner_res['cell_title'] = 'BANNER_CELL';
            $banner_res['cell_contents'] = $arr_banner->map(function ($item, $key) use ($merchant_id, $string_file) {
                $redirect_sub_category = false;
                if (isset($item->action_type) && $item->action_type == "CATEGORY" && !empty($item->category_id)) {
                    $redirect_sub_category = Category::where("category_parent_id", $item->category_id)->get()->count();
                    $redirect_sub_category = $redirect_sub_category > 0 ? true : false;
                }

                $image = get_image($item->banner_images, 'banners', $merchant_id);


                $is_open_from_admin = false;
                $admin_text = trans("$string_file.close");
                $outletConfig = LaundryOutletConfiguration::where('laundry_outlet_id',  $item->laundry_outlet_id)->first();
                if(!empty($outletConfig)){
                    $is_open_from_admin = $outletConfig->is_open == 1 ? true : false;
                    $admin_text = $outletConfig->is_open == 1 ?? trans("$string_file.open");
                }

                $outlet = $item->LaundryOutlet;
                $current_day = date('w');
                $arr_open_time = json_decode($outlet->open_time, true);
                $arr_close_time = json_decode($outlet->close_time, true);
                $open_time = isset($arr_open_time[$current_day]) ? $arr_open_time[$current_day] : NULL;
                $close_time = isset($arr_close_time[$current_day]) ? $arr_close_time[$current_day] : NULL;

                //  Changes for midnight store time
                if ($open_time > $close_time) {
                    $close_time_n = date('Y-m-d H:i:s', strtotime($close_time . ' +1 day'));
                } else {
                    $close_time_n = date('Y-m-d H:i:s', strtotime($close_time));
                }
                $open_time_n = date('Y-m-d H:i:s', strtotime($open_time));
                $current_time_n = date("Y-m-d H:i:s");

                $is_laundry_outlet_open = false;
                if ($open_time_n < $current_time_n && $close_time_n > $current_time_n) {
                    $is_laundry_outlet_open = true;
                }

                $return = array(
                    'id' => $item->id,
                    'segment_id'=> $item->segment_id,
                    'laundry_outlet_id' => $item->laundry_outlet_id,
                    'title' => $item->banner_name,
                    'image' => $image,
                    'image_width'=> isset($item->image_width) ? $item->image_width : 1000,
                    'image_height'=>isset($item->image_height) ?  $item->image_height : 500,
                    'action_type' => !empty($item->action_type) ? $item->action_type : "",
                    'redirect_url' => !empty($item->redirect_url) ? $item->redirect_url : "",
                    'redirect_product_id' => !empty($item->product_id) ? $item->product_id : null,
                    'redirect_category_id' => !empty($item->category_id) ? $item->category_id : null,
                    'redirect_sub_category' => $redirect_sub_category,
                    'is_outlet_open' => $is_laundry_outlet_open,
                    'is_admin_outlet_open'=>$is_open_from_admin,
                    'admin_store_text_from_admin'=>$admin_text,
                );
                if (!empty($item->LaundryOutlet)) {
                    $return['name']  = $item->LaundryOutlet->full_name;
                }
                return $return;
            });
        }

        //  get store list for holder
        if (in_array("STORE", $holder_item)) {
            $arr_outlets = $arr_laundry_outlets;
//            dd($arr_outlets);
            $restaurant_res = $arr_outlets->toArray();
            $next_page_url = $restaurant_res['next_page_url'];
            $total_pages = $restaurant_res['last_page'];
            $current_page = $restaurant_res['current_page'];
            //$this->getMerchantBusinessSegment($request);
            $outlet_heading['cell_title'] = 'TITLE';
            $outlet_heading['cell_contents'][0] = ['title' => trans("$string_file.all_outlets")];

            $outlet_res['cell_title'] = 'STORE_CELL';
            $google = new GoogleController;
            $user_lat = $request->latitude;
            $user_long = $request->longitude;
            $user = $request->user('api');
            $string_file = $this->getStringFile(NULL, $user->Merchant);
            $unit = isset($user->Country) ? $user->Country->distance_unit : "";
            $demo = $user->Merchant->demo;
            $google_key = $user->Merchant->BookingConfiguration->google_key;
            $outlet_res['cell_contents'] = $arr_outlets->map(function ($item, $key) use (
                $merchant_id,
                $google,
                $user_lat,
                $user_long,
                $unit,
                $string_file,
                $google_key,
                $demo
            ) {
                // only setting timezone
                date_default_timezone_set($item->CountryArea['timezone']);
                $current_time = date('H:i');
                $is_laundry_outlet_open = false;
                $current_day = date('w');
                $arr_open_time = json_decode($item->open_time, true);
                $arr_close_time = json_decode($item->close_time, true);
                $open_time = isset($arr_open_time[$current_day]) ? $arr_open_time[$current_day] : NULL;
                $close_time = isset($arr_close_time[$current_day]) ? $arr_close_time[$current_day] : NULL;

                //  Changes for midnight store time
                if ($open_time > $close_time) {
                    $close_time_n = date('Y-m-d H:i:s', strtotime($close_time . ' +1 day'));
                } else {
                    $close_time_n = date('Y-m-d H:i:s', strtotime($close_time));
                }
                $open_time_n = date('Y-m-d H:i:s', strtotime($open_time));
                $current_time_n = date("Y-m-d H:i:s");

                if ($open_time_n < $current_time_n && $close_time_n > $current_time_n) {
                    $is_laundry_outlet_open = true;
                }

                $is_open_from_admin = false;
                $admin_text = trans("$string_file.close");
                $outletConfig = LaundryOutletConfiguration::where('laundry_outlet_id',  $item->id)->first();
                if(!empty($outletConfig)){
                    $is_open_from_admin = $outletConfig->is_open == 1 ? true : false;
                    $admin_text = $outletConfig->is_open == 1 ?? trans("$string_file.open");
                }

                $outlet_lat = $item->latitude;
                $outlet_long = $item->longitude;
                if ($demo ==  1) {
                    $distance_from_user = $google->arialDistance($user_lat, $user_long, $outlet_lat, $outlet_long, $unit, $string_file);
                } else {
                    $user_drop_location[0] = [
                        'drop_latitude' => $user_lat,
                        'drop_longitude' => $user_long,
                        'drop_location' => ""
                    ];
                    $distance_from_user = GoogleController::GoogleStaticImageAndDistance($outlet_lat, $outlet_long, $user_drop_location, $google_key, "", $string_file);
                    $distance_from_user = $distance_from_user['total_distance_text'] ?? "";
                }
                return array(
                    'laundry_outlet_id' => $item->id,
                    'segment_id'=> $item->segment_id,
                    'title' => $item->full_name,
                    'description' => "Static Text Here",
                    'rating' => !empty($item->rating) ? $item->rating : "2.5",
                    'open_time' => $open_time,
                    'close_time' => $close_time,
                    'image' => get_image($item->business_logo, 'laundry_outlet_logo', $merchant_id),
                    'is_outlet_open' => $is_laundry_outlet_open,
                    'is_admin_outlet_open'=>$is_open_from_admin,
                    'admin_store_text_from_admin'=>$admin_text,
                    'distance' => trans("$string_file.distance") . ' ' . $distance_from_user,
                    'promo_code' => $item->ActivePromoCode ? $item->ActivePromoCode : (object)[],
                    //                    'is_favourite' => !empty($item->FavouriteBusinessSegment->id) ? true : false,
                );
            });
        }
//        dd($holder_item);
        //Todo: get popular store list for holder
        if (in_array("POPULAR_STORE", $holder_item)) {
            $arr_store = $arr_popular_outlets;
            $restaurant_res = $arr_popular_outlets->toArray();
            $next_page_url = $restaurant_res['next_page_url'];
            $total_pages = $restaurant_res['last_page'];
            $current_page = $restaurant_res['current_page'];
            //$this->getMerchantBusinessSegment($request);
            $popular_store_heading['cell_title'] = 'TITLE';
            $popular_store_heading['cell_contents'][0] = ['title' => trans("$string_file.popular_stores")];

            $popular_store_res['cell_title'] = 'POPULAR_CELL';
            $google = new GoogleController;
            $user_lat = $request->latitude;
            $user_long = $request->longitude;
            $user = $request->user('api');
            $string_file = $this->getStringFile(NULL, $user->Merchant);
            $unit = isset($user->Country) ? $user->Country->distance_unit : "";
            $google_key = $user->Merchant->BookingConfiguration->google_key;
            $popular_store_res['cell_contents'] = $arr_store->map(function ($item, $key) use (
                $merchant_id,
                $google,
                $user_lat,
                $user_long,
                $unit,
                $string_file,
                $google_key
            ) {
                // only setting timezone
                date_default_timezone_set($item->CountryArea['timezone']);
                $current_time = date('H:i');
                $is_laundry_outlet_open = false;
                $current_day = date('w');
                $arr_open_time = json_decode($item->open_time, true);
                $arr_close_time = json_decode($item->close_time, true);
                $open_time = isset($arr_open_time[$current_day]) ? $arr_open_time[$current_day] : NULL;
                $close_time = isset($arr_close_time[$current_day]) ? $arr_close_time[$current_day] : NULL;

                //  Changes for midnight store time
                if ($open_time > $close_time) {
                    $close_time_n = date('Y-m-d H:i:s', strtotime($close_time . ' +1 day'));
                } else {
                    $close_time_n = date('Y-m-d H:i:s', strtotime($close_time));
                }
                $open_time_n = date('Y-m-d H:i:s', strtotime($open_time));
                $current_time_n = date("Y-m-d H:i:s");
                if ($open_time_n < $current_time_n && $close_time_n > $current_time_n) {
                    $is_laundry_outlet_open = true;
                }
                
                $is_open_from_admin = false;
                $admin_text = trans("$string_file.close");
                $outletConfig = LaundryOutletConfiguration::where('laundry_outlet_id',  $item->id)->first();
                if(!empty($outletConfig)){
                    $is_open_from_admin = $outletConfig->is_open == 1 ? true : false;
                    $admin_text = $outletConfig->is_open == 1 ?? trans("$string_file.open");
                }


                $outlet_lat = $item->latitude;
                $outlet_long = $item->longitude;

                $user_drop_location[0] = [
                    'drop_latitude' => $user_lat,
                    'drop_longitude' => $user_long,
                    'drop_location' => ""
                ];
                $distance_from_user = GoogleController::GoogleStaticImageAndDistance($outlet_lat, $outlet_long, $user_drop_location, $google_key, "", $string_file);
                $distance_from_user = isset($distance_from_user['total_distance_text']) ? $distance_from_user['total_distance_text'] : "";

                return array(
                    'laundry_outlet_id' => $item->id,
                    'segment_id'=> $item->segment_id,
                    'title' => $item->full_name,
                    'rating' => !empty($item->rating) ? $item->rating : "2.5",
                    'open_time' => $open_time,
                    'close_time' => $close_time,
                    'image' => get_image($item->business_logo, 'laundry_outlet_logo', $merchant_id),
                    'is_laundry_outlet_open' => $is_laundry_outlet_open,
                    'distance' => trans("$string_file.distance") . ' ' . $distance_from_user,
                    //                    'is_favourite' => !empty($item->FavouriteBusinessSegment->id) ? true : false,
                    'is_outlet_open' => $is_laundry_outlet_open,
                    'is_admin_outlet_open'=>$is_open_from_admin,
                    'admin_store_text_from_admin'=>$admin_text,
                );
            });
        }

        //  get store list for holder
        if (in_array("CATEGORY", $holder_item)) {
            // home categories
            $category_heading['cell_title'] = 'TITLE';
            $category_heading['cell_contents'][0] = ['title' => trans("$string_file.all_categories")];
            $arr_categories = $outlets > 0 || !empty($request->laundry_outlet_id) ? $this->getLaundryOutletCategories($request) : [];
            $category_res['cell_title'] = 'CATEGORY_CELL';
            $category_res['cell_contents'] = $arr_categories;
            $laundry_outlet_id = $request->laundry_outlet_id;
        }

        if (in_array("CATEGORY_WITH_SUBCATEGORY", $holder_item)) {
            // home categories
            $category_heading['cell_title'] = 'TITLE';
            $category_heading['cell_contents'][0] = ['title' => trans("$string_file.all_categories")];
            $arr_categories = $outlets > 0 || !empty($request->laundry_outlet_id) ? $this->getLaundryOutletCategories($request) : [];
            $category_res['cell_title'] = 'CATEGORY_CELL';
            $category_res['cell_contents'] = $arr_categories;
            $laundry_outlet_id = $request->laundry_outlet_id;
        }
        $popular_store['cell_title'] = 'PRODUCT_CELL';
        $popular_store['cell_contents'] = [];

        $return_data = [];
        if ($is_search != 1 && !empty($banner_res)) {
            array_push($return_data, $banner_res);
        }
        if ($response == "multi_store") {
            array_push($return_data, $outlet_heading, $outlet_res, $popular_store_res);
        } elseif ($response == "single_store") {
            array_push($return_data, $category_heading, $category_res);
        } elseif ($response == "category_screen") {
            array_push($return_data, $category_heading, $category_res);
        }
        array_push($return_data, $popular_store);

        $return = [];
        $return['next_page_url'] = $next_page_url ?? "";
        $return['total_pages'] = $total_pages ?? 1;
        $return['current_page'] = $current_page ?? 1;
        $return['data'] = $return_data;
        $return['status'] = $response;
        $return['laundry_outlet_id'] = $laundry_outlet_id;
        return $return;
    }


    public function getLaundryOutletCategories($request)
    {
        $segment_id = $request->segment_id;
        $user = $request->user('api');
        $merchant_id = $user->merchant_id;
        $area_id = $request->area;
        $service = LaundryService::select('id', 'category_id')
            ->whereHas('LaundryOutlet', function ($q) use ($segment_id, $merchant_id, $area_id) {
                $q->where([['segment_id', '=', $segment_id], ['merchant_id', '=', $merchant_id]]);
                if (!empty($area_id)) {
                    $q->where('country_area_id', $area_id);
                }
            })
            ->where(function ($q) use ($request) {
                if (!empty($request->laundry_outlet_id)) {
                    $q->where('laundry_outlet_id', $request->laundry_outlet_id);
                }
            })
            ->where([['status', '=', 1], ['delete', '=', NULL]])
            ->get();
            $arr_categories_id  =  $service->map(function ($item, $key) use ($merchant_id) {
            if ($item->Category->delete != 1) {
                return ['id' => $item->Category->category_parent_id == 0 ? $item->Category->id : $item->Category->category_parent_id];
            }
        });

        $arr_categories_id = array_unique(array_pluck($arr_categories_id, 'id'));
        $category_query = Category::whereHas('Segment', function ($q) use ($segment_id, $merchant_id) {
            $q->where([['segment_id', '=', $segment_id], ['merchant_id', '=', $merchant_id]]);
        })
            ->whereIn('id', $arr_categories_id)
            ->where('category_parent_id', 0)
            ->where('merchant_id', $merchant_id)
            ->where('status', 1)
            ->where('delete', NULL);

        if (!empty($request->category_type)) {
            if ($request->category_type == "CAT") {
                $category_query->where("category_type", "CAT")->orderBy('sequence', "ASC");
            } elseif ($request->category_type == "EVENT") {
                $category_query->where("category_type", "EVENT")->orderBy('sequence', "ASC");
            } elseif ($request->category_type == "EVENT_TWO") {
                $category_query->where("category_type", "EVENT_TWO")->orderBy('sequence', "ASC");
            }
        } else {
            $category_query->orderBy('sequence');
        }

        $categories = $category_query->get();
        $sub_categories = [];
        return $categories->map(function ($item, $key) use ($request, $merchant_id, $segment_id) {
            $sub_cat_count = Category::where('category_parent_id', $item->id)->count();
            $return = array(
                'id' => $item->id,
                'title' => $item->Name($merchant_id),
                'image' => get_image($item->category_image, 'category', $merchant_id),
                'sub_category' => $sub_cat_count > 0,
                'cel_slug' => $request->category_type ?? ""
            );
            if (isset($request->calling_from) && $request->calling_from == 'category_screen'){
                $sub_cats = Category::select('id', 'category_parent_id')->whereHas('Segment', function ($q) use ($segment_id, $merchant_id) {
                    $q->where([['segment_id', '=', $segment_id], ['merchant_id', '=', $merchant_id]]);
                })
                    ->where('category_parent_id', $item->id)
                    ->where('merchant_id', $merchant_id)
                    ->where('status', 1)
                    ->orderBy('sequence')
                    ->where('delete', NULL)
                    ->get();
                $sub_cats = $sub_cats->map(function ($sub_cat) use ($merchant_id) {
                    return [
                        'id' => $sub_cat->id,
                        'category_parent_id' => $sub_cat->category_parent_id,
                        'category_name' => $sub_cat->Name($merchant_id),
                    ];
                });
                $return['sub_categories'] = array_values($sub_cats->toArray());
            }
            return $return;
        });
    }

}