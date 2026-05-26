<?php
/**
 * Created by PhpStorm.
 * User: apporio
 * Date: 16/8/23
 * Time: 10:45 PM
 */

namespace App\Http\Controllers\Developer;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\SmsController;
use App\Models\DefaultOnesignal;
use App\Models\Driver;
use App\Models\HolderSegment;
use App\Models\HomeScreenHolder;
use App\Models\Merchant;
use App\Models\MerchantHomeScreenHolder;
use App\Models\Onesignal;
use App\Models\Segment;
use App\Models\SmsConfiguration;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserGuide;
use App\Models\DriverDetail;
use App\Traits\DriverTrait;
use App\Traits\ImageTrait;
use App\Traits\MerchantTrait;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Redis;


class DeveloperController extends Controller
{
    use DriverTrait,ImageTrait, MerchantTrait;

    public $merchant = "";

    public function __construct()
    {
        $value = \Session::get('developer');
        $pin = base64_decode($value);
        $this->merchant = Merchant::where("access_pin", $pin)->first();
    }

    public function index(){
        $merchant = $this->merchant;
        $places_api_count = count($merchant->SearchablePlace);
        return view("developer.home", compact("merchant", "places_api_count"));
    }

    public function out()
    {
        \Session::forget('developer');
        return redirect()->route("merchant.dashboard")->withSuccess("Logged out successfully");
    }

    public function smsGatewayTesting(){
        $merchant = $this->merchant;
        $sms_config = SmsConfiguration::where("merchant_id", $merchant->id)->get();
        return view("developer.sms_gateway_testing", compact("merchant", "sms_config"));
    }

    public function getSmsGatewayDetails(Request $request){
        $validator = Validator::make($request->all(), [
            'sms_gateway_config_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }

        $configuration = SmsConfiguration::with('Merchant','SmsGateways')->where('id','=',$request->sms_gateway_config_id)->first();

        $params = json_decode($configuration->SmsGateways->params);

        $html = "";
        foreach ($params as $key => $value) {
            $paramvalue=$configuration->$key;
            $html .= "<label><b>$value</b></label> : <label>$paramvalue</label>";
        }

        return $html;
    }

    public function submitSmsGatewayTesting(Request $request){
        $validator = Validator::make($request->all(), [
            'sms_gateway_config_id' => 'required',
            'phone' => 'required',
            'sms' =>'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }
        try{
            $merchant_id = get_merchant_id();
            $sms = new SmsController();
            $response = $sms->SendSms($merchant_id, $request->phone, $request->sms, null, null, true, $request->sms_gateway_config_id);
            return $response;
        }catch (\Exception $exception){
            return $exception->getMessage();
            exit();
        }
    }

    public function userToken(){
        $merchant_id = get_merchant_id();
        $users = User::where([['merchant_id', '=', $merchant_id], ['taxi_company_id', '=', NULL], ['user_delete', '=', NULL], ['signup_status', '!=', 1]])->get();
        $users_arr[] = "--Select--";
        foreach($users as $user){
            $users_arr[$user->id] = $user->first_name." ".$user->last_name." (".$user->UserPhone.")";
        }
        return view("developer.user_token_generate", compact( "users_arr"));
    }

    public function segmentGroupIcon(Request $request){
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile($merchant_id);
        $merchant_segement_group_icons = Merchant::select("handyman_segement_group_icon")->where("id", $merchant_id)->first();
        return view("developer.segment_group_icon",compact('merchant_segement_group_icons','merchant','merchant_id','string_file'));
    }

    public function saveSegemtGroupIcon(Request $request)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile($merchant_id);
        $request->validate([
            'handyman_grouping_icon' => 'required'
        ]);
        DB::beginTransaction();
        try {
            if ($request->hasFile("handyman_grouping_icon")) {
                $handyman_grouping_icon = $this->uploadImage('handyman_grouping_icon', 'segment_group_icons', $merchant_id);
                $merchant = Merchant::find($merchant_id);
                $merchant->handyman_segement_group_icon = $handyman_grouping_icon;
                $merchant->handyman_segement_group_name = $request->handyman_grouping_name;
                $merchant->save();
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            return redirect()->route('developer.segment-group-icon')->withErrors($message);
            DB::rollback();
        }
        DB::commit();
        return redirect()->route('developer.segment-group-icon')->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function clientCreate(Request $request){
        return view("developer.third_party_tokens");
    }

    public function thirdPartyTokenGeneration(Request $request){
        try{
            $merchant = get_merchant_id(false);
            $client = Client::where([['user_id', '=', $merchant->id], ['password_client', '=', 1]])->first();
            $request->request->add([
                'grant_type' => 'client_credentials',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'scope' => '*',
            ]);
            $token_generation_after_login = Request::create(
                'oauth/token',
                'POST'
            );
            $collect_response = \Route::dispatch($token_generation_after_login)->getContent();

            $collectArray = json_decode($collect_response);
            if (isset($collectArray->error)) {
                return "Invalid";
            }
            return "Bearer $collectArray->access_token";
        }catch (\Exception $exception){
            return $exception->getMessage();
            exit();
        }
    }

    public function generateUserToken(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }
        try{
            $merchant = get_merchant_id(false);
            $user = User::where("merchant_id", $merchant->id)->where("id", $request->user_id)->first();
            if(!empty($user)){
                $client = Client::where([['user_id', '=', $merchant->id], ['password_client', '=', 1]])->first();
                Config::set('auth.guards.api.provider', 'userOtp');
                $request->request->add(["publicKey" => $merchant->merchantPublicKey, "secretKey" => $merchant->merchantSecretKey]);

                $request->request->add([
                    'merchant_id' => $merchant->id,
                    'grant_type' => 'password',
                    'client_id' => $client->id,
                    'client_secret' => $client->secret,
                    'username' => "$user->id",
                    'password' => '',
                    'scope' => '',
                ]);
                $token_generation_after_login = Request::create(
                    'oauth/token',
                    'POST'
                );
                $collect_response = \Route::dispatch($token_generation_after_login)->getContent();

                $collectArray = json_decode($collect_response);
                if (isset($collectArray->error)) {
                    return "Invalid";
                }
                return "Bearer $collectArray->access_token";
            }else{
                return "User Not Found!!";
            }
        }catch (\Exception $exception){
            return $exception->getMessage();
            exit();
        }
    }

    public function driverToken(Request $request){
        $merchant_id = get_merchant_id();
        $request->merge(['merchant_id' => $merchant_id]);
        $drivers = $this->getAllDriver(true, $request);

        $drivers_arr[] = "--Select--";
        foreach($drivers as $driver){
            $drivers_arr[$driver->id] = $driver->first_name." ".$driver->last_name." (".$driver->phoneNumber.")";
        }
        return view("developer.driver_token_generate", compact( "drivers_arr"));
    }

    public function generateDriverToken(Request $request){
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }
        try{
            $merchant = get_merchant_id(false);
            $driver = Driver::where("merchant_id", $merchant->id)->where("id", $request->driver_id)->first();
            if(!empty($driver)){
                $client = Client::where([['user_id', '=', $merchant->id], ['password_client', '=', 1]])->first();
                Config::set('auth.guards.api.provider', 'driverOtp');
                $request->request->add(["publicKey" => $merchant->merchantPublicKey, "secretKey" => $merchant->merchantSecretKey]);

                $request->request->add([
                    'merchant_id' => $merchant->id,
                    'grant_type' => 'password',
                    'client_id' => $client->id,
                    'client_secret' => $client->secret,
                    'username' => "$driver->id",
                    'password' => '',
                    'scope' => '',
                ]);
                $token_generation_after_login = Request::create(
                    'oauth/token',
                    'POST'
                );
                $collect_response = \Route::dispatch($token_generation_after_login)->getContent();

                $collectArray = json_decode($collect_response);
                if (isset($collectArray->error)) {
                    return "Invalid";
                }
                return "Bearer $collectArray->access_token";
            }else{
                return "Driver Not Found!!";
            }
        }catch (\Exception $exception){
            return $exception->getMessage();
            exit();
        }
    }



    public function devSettings(Request $request){
        $merchant_id = get_merchant_id();
        $request->merge(['merchant_id' => $merchant_id, 'request_from' => 'dev_settings']);;
        $drivers = $this->getAllDriver(false, $request);

        $drivers_arr[] = "--Select--";
        foreach($drivers as $driver){
            $drivers_arr[$driver->id] = $driver->first_name." ".$driver->last_name." (".$driver->phoneNumber.")";
        }

        $enabled_location_logs = Driver::where('merchant_id', $merchant_id)
            ->whereHas('DriverDetail', function ($q) {
                $q->where('location_logs_enable', 1);
            })
            ->with(['DriverDetail' => function ($q) {
                $q->where('location_logs_enable', 1);
            }])
            ->select('id', 'merchant_id', 'first_name', 'last_name', 'phoneNumber')
            ->get();

        foreach($enabled_location_logs as $driver){
            $today = now('Asia/Kolkata')->format('Y-m-d');
            $pattern = "location_api_request_log:{$driver->merchant_id}:{$driver->id}:{$today}";
            $logs = Redis::lrange($pattern, 0, -1);
            $logs = array_map(function($log) {
                return json_decode($log, true);
            }, $logs);
            $driver->location_logs = $logs;
        }
        return view("developer.dev_settings", compact( "drivers_arr", "enabled_location_logs"));
    }


    public function SavedevSettings(Request $request){
        try{
            DB::beginTransaction();
            $merchant_id = get_merchant_id();
            $driver_ids = $request->driver_id;
            $log_enable_status = $request->status;
            foreach($driver_ids as $driver_id){
                DriverDetail::updateOrCreate(['driver_id' => $driver_id],['location_logs_enable' => $log_enable_status, 'driver_jwt_token'=> NULL]);

                if($log_enable_status == 2){
                    $pattern = "location_api_request_log:{$merchant_id}:{$driver_id}:*";
                    $keys = Redis::keys($pattern);
                    if (!empty($keys)) {
                        Redis::del($keys);
                    }
                }
            }
        }
        catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->withError("Something went wrong! : ".$e->getMessage());
        }
        DB::commit();
        return redirect()->back()->withSuccess("Saved Successfully !");
    }

    /**
     * Notification Developer Tool
     */

    public function userNotification(){
        $merchant_id = get_merchant_id();
        $users = User::where([['merchant_id', '=', $merchant_id], ['taxi_company_id', '=', NULL], ['user_delete', '=', NULL], ['signup_status', '!=', 1]])->get();
        $users_arr[] = "--Select--";
        foreach($users as $user){
            $users_arr[$user->id] = $user->first_name." ".$user->last_name." (".$user->UserPhone.")";
        }
        $notification_content = array(
            "message" => "This is Test Message",
            "data" => array(
                "notification_type" => "PROMOTION_NOTIFICATION",
                "segment_type" => "NOTIFICATION",
                "segment_data" => array("id" => 1)
            ),
            "merchant_id" => $merchant_id,
            "title" => "Developer Tool",
            "large_icon" => ""
        );
        $notification_content = json_encode($notification_content);
        return view("developer.user_notification", compact( "users_arr", "notification_content"));
    }

    public function getUserPlayerids(Request $request){
        try{
            $merchant_id = get_merchant_id();
            $onesignal_player_id = UserDevice::select('player_id', 'device')->where(function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })->whereHas('User', function ($q) use ($merchant_id) {
                $q->where([['merchant_id', '=', $merchant_id], ['user_delete', '=', NULL]]);
            })->where('player_id', '!=', '')->get();
            $data = "";
            $result = 1;
            if(count($onesignal_player_id) > 0){
                foreach($onesignal_player_id as $player){
                    $device = $player->device == 1 ? "Android" : "iOS";
                    $data .= "<input type='checkbox' id='user_$player->player_id' name='player_id_values[]' class='player_id_check' value='$player->player_id'> - <label for='user_$player->player_id'>".$player->player_id.", <b>".$device."</b></label><br>";
                }
            }else{
                $data = "No player ids found.";
                $result = 0;
            }
            return array("data" => $data, "result" => $result);
        }catch (\Exception $exception){
            return array("data" => $exception->getMessage(), "result" => $result);
        }
    }

    public function generateUserNotification(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }
        try{
            $merchant = get_merchant_id(false);
            $user = User::where("merchant_id", $merchant->id)->where("id", $request->user_id)->first();
            if(!empty($user)){
                $arr_param = json_decode($request->notification_content, true);
                $arr_param['user_id'] = $request->user_id;
                if(!empty($request->player_ids)){
                    $player_ids = explode("%%",$request->player_ids);
                    array_pop($player_ids);
                    $arr_param['player_ids'] = $player_ids;
                }
                $arr_param['merchant_id'] = $merchant->id;
                $data = Onesignal::UserPushMessage($arr_param);
                return json_encode($data);
            }else{
                return "User Not Found!!";
            }
        }catch (\Exception $exception){
            return $exception->getMessage();
            exit();
        }
    }

    public function driverNotification(Request $request){
        $merchant_id = get_merchant_id();
        $request->merge(['merchant_id' => $merchant_id]);
        $drivers = $this->getAllDriver(true, $request);

        $drivers_arr[] = "--Select--";
        foreach($drivers as $driver){
            $drivers_arr[$driver->id] = $driver->first_name." ".$driver->last_name." (".$driver->phoneNumber.")";
        }

        $notification_content = array(
            "message" => "This is Test Message",
            "data" => array(
                "notification_type" => "NOTIFICATION",
                "segment_type" => "NOTIFICATION",
                "segment_data" => array("id" => 1)
            ),
            "merchant_id" => $merchant_id,
            "title" => "Developer Tool",
            "large_icon" => ""
        );
        $notification_content = json_encode($notification_content);
        return view("developer.driver_notification", compact( "drivers_arr", "notification_content"));
    }

    public function getDriverPlayerids(Request $request){
        try{
            $merchant_id = get_merchant_id();

            $player_ids = Driver::select('player_id', 'device')->where(function ($q) use ($request) {
                $q->where('id', $request->driver_id);
            })->where([['merchant_id', '=', $merchant_id], ['driver_delete', '=', NULL], ['player_id', '!=',NULL]])->get();

            $data = "";
            $result = 1;
            if(count($player_ids) > 0){
                foreach($player_ids as $player){
                    $device = $player->device == 1 ? "Android" : "iOS";
                    $data .= "<input type='checkbox' id='driver_$player->player_id' name='player_id_values[]' class='player_id_check' value='$player->player_id'> - <label for='driver_$player->player_id'>".$player->player_id.", <b>".$device."</b></label><br>";
                }
            }else{
                $data = "No player ids found.";
                $result = 0;
            }
            return array("data" => $data, "result" => $result);
        }catch (\Exception $exception){
            return array("data" => $exception->getMessage(), "result" => $result);
        }
    }

    public function generateDriverNotification(Request $request){
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }
        try{
            $merchant = get_merchant_id(false);
            $driver = Driver::where("merchant_id", $merchant->id)->where("id", $request->driver_id)->first();
            if(!empty($driver)){
                $arr_param = json_decode($request->notification_content, true);
                $arr_param['driver_id'] = $driver->id;
                $data = Onesignal::DriverPushMessage($arr_param);
                return json_encode($data);
            }else{
                return "User Not Found!!";
            }
        }catch (\Exception $exception){
            return $exception->getMessage();
            exit();
        }
    }

    /**
     * END - Notification Developer Tool
     */


    /**
     * API Testing - Developer Tool
     */

    public function apiTesting(){

        $routeCollection = \Illuminate\Support\Facades\Route::getRoutes();
//        echo "<table style='width:100%'>";
//        echo "<tr>";
//        echo "<td width='10%'><h4>HTTP Method</h4></td>";
//        echo "<td width='10%'><h4>Route</h4></td>";
//        echo "<td width='10%'><h4>Name</h4></td>";
//        echo "<td width='70%'><h4>Corresponding Action</h4></td>";
//        echo "</tr>";
        $api_list = [];
        foreach ($routeCollection as $value) {
            $uri_arr = explode("/", $value->uri());
            // $value->methods()[0] == "POST" &&
            if($uri_arr[0] == "api"){
//                echo "<tr>";
//                echo "<td>" . $value->methods()[0] . "</td>";
//                echo "<td>" . $value->uri() . "</td>";
//                echo "<td>" . $value->getName() . "</td>";
//                echo "<td>" . $value->getActionName() . "</td>";
//                echo "</tr>";
                array_push($api_list, array(
                    "uri" => $value->uri(),
                    "method" => $value->methods()[0],
                    "name" => $value->getName(),
                    "calling_function" => $value->getActionName()
                ));
            }
        }
//        echo "</table>";
//        p("d");
        $merchant = get_merchant_id(false);
        return view("developer.api_testing", compact( "api_list", "merchant"));
    }

    public function apiTestingSubmit(Request $request){
        try{
            $url = $request->base_url."/".$request->api;
            $locale = isset($request->locale_type) && !empty($request->locale_type) ? $request->locale_type : "en";
            $header = array("locale:$locale","Content-Type: application/json");
            if(isset($request->token) && !empty($request->token)){
                array_push($header, "Authorization:Bearer ".$request->token);
            }elseif(isset($request->public_key) && !empty($request->public_key) && isset($request->secret_key) && !empty($request->secret_key)){
                array_push($header, "public_key:".$request->token);
                array_push($header, "secret_key:".$request->token);
            }
            $postFields = [];
            if(isset($request->request_data) && !empty($request->request_data)){
                $postFields = $request->request_data;
            }
            $response = "";
            if($request->method_type == "POST"){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                $response = curl_exec($ch);
                curl_close($ch);
            }elseif($request->method_type == "GET"){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                $response = curl_exec($ch);
                curl_close($ch);
            }
            return $response;
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    /**
     * END - API Testing Developer Tool
     */

    /**
     * Preview App Config - - Developer Tool
     */

    public function previewConfig(){
        $merchant = get_merchant_id(false);
        $onesignal = DefaultOnesignal::first();
        return view("developer.preview_app_config", compact("merchant", "onesignal"));
    }

    public function previewConfigSubmit(Request $request){
        try{
            $onesignal = DefaultOnesignal::first();
            if(empty($onesignal)){
                $onesignal = new DefaultOnesignal();
            }

            $onesignal->user_application_key = $request->user_application_key;
            $onesignal->user_rest_key = $request->user_rest_key;
            $onesignal->user_channel_id = $request->user_channel_id;
            $onesignal->driver_application_key = $request->driver_application_key;
            $onesignal->driver_rest_key = $request->driver_rest_key;
            $onesignal->driver_channel_id = $request->driver_channel_id;
            $onesignal->web_application_key = $request->web_application_key;
            $onesignal->web_rest_key = $request->web_rest_key;

            $onesignal->business_segment_application_key = $request->business_segment_application_key;
            $onesignal->business_segment_rest_key = $request->business_segment_rest_key;
            $onesignal->business_segment_channel_id = $request->business_segment_channel_id;
            $onesignal->save();

            $merchant_id = get_merchant_id();

            Merchant::updateOrCreate(
                ['id' => $merchant_id],
                ['send_notification_to_preview' => isset($request->send_notification_to_preview) ? $request->send_notification_to_preview : null]
            );
            return redirect()->back()->withSuccess("Saved Successfully");
        }catch (\Exception $exception){
            p($exception->getMessage());
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }

    /**
     * END - Preview App Config - Developer Tool
     */

    /*
     * Home Screen Holder Config
     */

    public function homeScreenConfig(){
        $merchant = get_merchant_id(false);
        $home_screen_holders = HomeScreenHolder::where('status',1)->get();
        $merchant_holders = $merchant->HomeScreenHolder;
        return view("developer.home_screen_holder_config", compact("merchant", "home_screen_holders","merchant_holders"));
    }

    public function homeScreenConfigSubmit(Request $request){
        DB::beginTransaction();
        try{
            $merchant = get_merchant_id(false);
            if (!empty($request->home_screen_holder)){
                $home_screen_holder_arr = [];
                foreach ($request->home_screen_holder as $key => $value){
                    $home_screen_holder_arr[$value] = ['sequence' => $request->holder_position[$key]];
                }
                $merchant->HomeScreenHolder()->sync($home_screen_holder_arr);
            }
            DB::commit();
            return redirect()->back()->withSuccess("Saved Successfully");
        }catch (\Exception $exception){
            DB::rollback();
            // p($exception->getMessage());
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }

    public function resetDefaultHomeScreen(){
        DB::beginTransaction();
        try{
            $merchant = get_merchant_id(false);
            $merchant->HomeScreenHolder()->detach();
            DB::commit();
            return redirect()->back()->withSuccess("Config Reset Successfully");
        }catch (\Exception $exception){
            DB::rollback();
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }

    /*
     * End Home Screen Holder Config
     */

    /*
     * User Guide Files
     */

    public function userGuide(){
        $guides = UserGuide::get();
        $user_guides = [];
        foreach ($guides as $guide){
            $user_guides[$guide->slug] = $guide->file;
        }
        $slugs = array("TAXI","DELIVERY","FOOD","GROCERY","HANDYMAN","CARPOOLING","BUS_BOOKING");
        return view("developer.user_guide", compact("user_guides", "slugs"));
    }

    public function submitUserGuide(Request $request){
        DB::beginTransaction();
        try{
            if(isset($request->file) && !empty($request->file)){
                foreach($request->file as $item){
                    if(isset($item['file']) && !empty($item['file'])){
                        UserGuide::updateOrCreate(["slug" => $item['slug']],["file" => $this->uploadFile($item['file'], 'user_guide')]);
                    }
                }
            }
            DB::commit();
            return redirect()->back()->withSuccess("Saved Successfully");
        }catch (\Exception $exception){
            DB::rollback();
            p($exception->getMessage());
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }

    function uploadFile($file, $dir = 'images')
    {
        // dd($file,$dir);
        $name = "";
        if ($file) {
            $upload_path = \Config::get('custom.' . $dir);
            $alias = $upload_path['path'];
            $name = time() . "_" . uniqid() . '_' . $dir . '.' . $file->getClientOriginalExtension();
            $filePath = $alias . $name;
            \Illuminate\Support\Facades\Storage::disk('s3')->put($filePath, file_get_contents($file));
        }
        return $name;
    }

    /*
     * End User Guide Files
     */

    /*
     * Image Gallery
     */
    public function imageGallery(){
        $gallery_images = get_config_image("image_gallery");
        $images_arr = [];
        foreach($gallery_images as $image){
            array_push($images_arr, view_config_image($image));
        }
        $images_arr = json_encode($images_arr);
        return view("developer.image_gallery", compact("gallery_images","images_arr"));
    }

    public function imageGallerySubmitTest(Request $request){
        p($request->all());
    }

    public function imageGallerySubmit(Request $request){
        try{
            foreach($request->image as $item){
                $this->uploadFile($item, 'image_gallery');
            }
            return redirect()->back()->withSuccess("Uploaded Successfully");
        }catch (\Exception $exception){
            p($exception->getMessage());
        }
    }


    /**
     * @ayush Create and Edit for Dynamic Holders
     */
    public function addDynamicHomeScreenHolder($id = null){
        $merchant = $this->merchant;
        $merchant_segment = $merchant->Segment;
        $dynamic_holders =  DB::table("home_screen_holders")
            ->select(
                "home_screen_holders.id",
                "home_screen_holders.merchant_id",
                "home_screen_holders.name as holder_name",
                "home_screen_holders.holder_image",
                DB::raw("GROUP_CONCAT(segments.name SEPARATOR ', ') as segment_names")
            )
            ->join("holder_segments", "home_screen_holders.id", "=", "holder_segments.home_screen_holder_id")
            ->join("segments", "segments.id", "=", "holder_segments.segment_id")
            ->where("home_screen_holders.merchant_id", $merchant->id)
            ->where("slug", "DYNAMIC_HOLDER")
            ->groupBy(
                "home_screen_holders.id",
                "home_screen_holders.merchant_id",
                "home_screen_holders.name",
                "home_screen_holders.holder_image"
            )
            ->get();
        $data = [];
        if(!empty($id)){
            $data["name"] = HomeScreenHolder::select("name")->where("id", $id)->first()->name;
            $data['selected_segments'] = HolderSegment::where("home_screen_holder_id", $id)->pluck("segment_id")->toArray();
        }
        return view("developer.dynamic-home-screen-holder", compact('merchant_segment', 'dynamic_holders', 'id', 'data'));
    }

    /**
     * @ayush (Dynamic Holders are created using choosen segment , then added to Home Screen Configs)
     */
    public function saveDynamicHomeScreenHolder(Request $request, $id = null){
        $rules = [
            "holder_name" => 'required',
            "segments" => 'required'
        ];
        if (empty($id)) {
            $rules["holder_image"] = 'required';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withErrors($errors[0]);
        }
        $merchant = $this->merchant;
        DB::beginTransaction();
        try{

            if(!empty($id)) {
                $home_screen_holder = HomeScreenHolder::find($id);
            }
            else{
                $home_screen_holder = new HomeScreenHolder();
                $home_screen_holder->created_at = Date("Y-m-d H:i:s");
            }
            $home_screen_holder->name = $request->holder_name;
            $home_screen_holder->merchant_id = $merchant->id;
            $home_screen_holder->slug = "DYNAMIC_HOLDER";
            if(empty($id) || $request->hasFile('holder_image')) {
                $image = $this->uploadImage('holder_image','merchant', $merchant->id);
                $home_screen_holder->holder_image = $image;
            }
            $home_screen_holder->status = 1;
            $home_screen_holder->updated_at=Date("Y-m-d H:i:s");
            $home_screen_holder->save();

            if(!empty($id)){
                HolderSegment::where("home_screen_holder_id", $id)->delete();
            }

            if(!empty($home_screen_holder)){
                foreach($request->segments as $segment){
                    $holder_segments = new HolderSegment();
                    $holder_segments->home_screen_holder_id= $home_screen_holder->id;
                    $holder_segments->segment_id = $segment;
                    $holder_segments->created_at = Date("Y-m-d H:i:s");
                    $holder_segments->updated_at=Date("Y-m-d H:i:s");
                    $holder_segments->save();
                }
            }
        }
        catch (\Exception $e){
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
        DB::commit();
        return redirect()->route("dynamic.home-screen-holder")->withSuccess("Dynamic Holder Added Successfully !");
    }


    public function coordinatesTesting(Request $request)
    {
        $merchant = $this->merchant;
        $key = $merchant->BookingConfiguration->google_key_admin;
        return view("developer.map_and_distance", compact('key'));
    }

    public function fetchBookingCoordinates(Request $request)
    {
        $booking_id = $request->booking_id;
        $merchant = $this->merchant;
        $booking = \App\Models\Booking::where("id", $booking_id)->where("merchant_id", $merchant->id)->first();
        $booking_distance_log = isset($booking->BookingDetail)? json_decode($booking->BookingDetail->distance_log) : null;
        $coordinate = !empty($booking)? $booking->BookingCoordinate: null;
        return response()->json(["success"=>!empty($coordinate), "coordinate"=>$coordinate, "booking_distance_log"=>$booking_distance_log]);
    }
}
