<?php
/**
 * Created by PhpStorm.
 * User: apporio
 * Date: 16/8/23
 * Time: 10:45 PM
 */

namespace App\Http\Controllers\SpAdmin;


use App\Http\Controllers\Controller;
use App\Models\AccountType;
use App\Models\Admin;
use App\Models\ApplicationConfiguration;
use App\Models\ApplicationTheme;
use App\Models\BookingConfiguration;
use App\Models\CancelReason;
use App\Models\Category;
use App\Models\Configuration;
use App\Models\Country;
use App\Models\Document;
use App\Models\DriverConfiguration;
use App\Models\HandymanConfiguration;
use App\Models\LangAppNavDrawer;
use App\Models\LangName;
use App\Models\LanguageCancelReason;
use App\Models\LanguageCountry;
use App\Models\LanguageDocument;
use App\Models\LanguageVehicleMake;
use App\Models\LanguageVehicleModel;
use App\Models\LanguageVehicleType;
use App\Models\Merchant;
use App\Models\MerchantNavDrawer;
use App\Models\Onesignal;
use App\Models\ServiceType;
use App\Models\User;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use App\Models\VehicleMake;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Laravel\Passport\Client;
use DB;
use Config;
use Schema;
use App\Models\PricingParameter;
use App\Models\LanguagePricingParameter;
use App\Models\PricingParameterValue;
use App\Models\BusinessSegment\BusinessSegment;
use App\Models\BusinessSegment\Product;
use App\Models\BusinessSegment\LanguageProduct;
use App\Models\BusinessSegment\LanguageProductVariant;
use App\Models\BusinessSegment\ProductVariant;
use App;

class MerchantController extends Controller
{
    public function index(){
        $merchants = Merchant::where("parent_id", 0)->get()->pluck("BusinessName", "id")->toArray();
        return view("sp_admin.index", compact("merchants"));
    }

    public function merchantCopy(Request $request){
        $request->merge(['alias_name' => str_slug($request->business_name)]);
        $request->validate([
            'business_name' => 'required|string|max:255',
            'alias_name' => 'required|max:255|unique:merchants,alias_name',
            'owner_first_name' => 'required',
            'owner_last_name' => 'required',
            'email' => 'required|email|unique:merchants,email',
            'phone' => 'required',
            'merchant_id' => 'required|exists:merchants,id',
            'business_logo' => 'required|file',
            'password' => 'required',
            'confirm_password' => 'required_with:password|same:password',
        ]);
        DB::beginTransaction();
        try{
//            p($request->all(), 0);
            $source_merchant = Merchant::find($request->merchant_id);
//            $ext = str_replace("-","_",$source_merchant->alias_name);
//            $ext = str_replace($ext, "", $source_merchant->string_file);
            $string_group = json_decode($source_merchant->string_group, true);
            $langString = "all_in_one";
            if(count($string_group) > 0){
                $langString = $string_group[0];
            }
            $ext = "_".$langString;
            $merchant_arr = $source_merchant->toArray();
            unset($merchant_arr['id']);
            unset($merchant_arr['created_at']);
            unset($merchant_arr['updated_at']);
            unset($merchant_arr['file_system_config']);
            $merchant_arr['BusinessName'] = $request->business_name;
            $merchant_arr['email'] = $request->email;
            $merchant_arr['alias_name'] = $request->alias_name;
            $merchant_arr['merchantFirstName'] = $request->owner_first_name;
            $merchant_arr['merchantLastName'] = $request->owner_last_name;
            $merchant_arr['merchantPhone'] = $request->phone;
            $merchant_arr['merchantAddress'] = $request->address;
            $merchant_arr['password'] = Hash::make($request->password);
            $merchant_arr['merchantPublicKey'] = $this->publickey();
            $merchant_arr['merchantSecretKey'] = $this->secretkey();
            $merchant_arr['string_file'] = str_replace("-", "_", str_slug($request->business_name)) . $ext;
            $merchant_arr['access_pin'] = $this->accessPin();

            $merchant = Merchant::create($merchant_arr);

            $merchant->BusinessLogo = $this->uploadImage('business_logo', $dir = 'business_logo', $merchant->id, 'single', true, $merchant->alias_name);
            $merchant->save();

            $source_driver_config = DriverConfiguration::where("merchant_id", $source_merchant->id)->first();
            if(!empty($source_driver_config)){
                $driver_config_arr = $source_driver_config->toArray();
                unset($driver_config_arr['id']);
                unset($driver_config_arr['created_at']);
                unset($driver_config_arr['updated_at']);
                $driver_config_arr['merchant_id'] = $merchant->id;
                $driver_config = DriverConfiguration::create($driver_config_arr);
            }

            $source_config = Configuration::where("merchant_id", $source_merchant->id)->first();
            if(!empty($source_config)){
                $config_arr = $source_config->toArray();
                unset($config_arr['id']);
                unset($config_arr['created_at']);
                unset($config_arr['updated_at']);
                $config_arr['merchant_id'] = $merchant->id;
                $config = Configuration::create($config_arr);
            }

            $source_booking_config = BookingConfiguration::where("merchant_id", $source_merchant->id)->first();
            if(!empty($source_booking_config)){
                $booking_config_arr = $source_booking_config->toArray();
                unset($booking_config_arr['id']);
                unset($booking_config_arr['created_at']);
                unset($booking_config_arr['updated_at']);
                $booking_config_arr['merchant_id'] = $merchant->id;
                $booking_config = BookingConfiguration::create($booking_config_arr);
            }

            $source_application_config = ApplicationConfiguration::where("merchant_id", $source_merchant->id)->first();
            if(!empty($source_application_config)){
                $application_config_arr = $source_application_config->toArray();
                unset($application_config_arr['id']);
                unset($application_config_arr['created_at']);
                unset($application_config_arr['updated_at']);
                $application_config_arr['merchant_id'] = $merchant->id;
                $application_config = ApplicationConfiguration::create($application_config_arr);
            }

            $source_onesignal = Onesignal::where("merchant_id", $source_merchant->id)->first();
            if(!empty($source_onesignal)){
                $onesignal_arr = $source_onesignal->toArray();
                unset($onesignal_arr['id']);
                unset($onesignal_arr['created_at']);
                unset($onesignal_arr['updated_at']);
                $onesignal_arr['merchant_id'] = $merchant->id;
                $onesignal = Onesignal::create($onesignal_arr);
            }

            $source_handyman = HandymanConfiguration::where("merchant_id", $source_merchant->id)->first();
            if(!empty($source_handyman)){
                $handyman_arr = $source_handyman->toArray();
                unset($handyman_arr['id']);
                unset($handyman_arr['created_at']);
                unset($handyman_arr['updated_at']);
                $handyman_arr['merchant_id'] = $merchant->id;
                $handyman = HandymanConfiguration::create($handyman_arr);
            }

            $source_categories = Category::where('merchant_id',$source_merchant->id)->get();
            if (!empty($source_categories)){
                foreach ($source_categories as $source_category){
                    $category_arr = $source_category->toArray();
                    unset($category_arr['id']);
                    unset($category_arr['created_at']);
                    unset($category_arr['updated_at']);
                    $category_arr['merchant_id'] = $merchant->id;
                    $category = Category::create($category_arr);
                }
            }

            $source_cancel_reasons = CancelReason::where('merchant_id',$source_merchant->id)->get();
            $source_cancel_reasons = $source_cancel_reasons->map(function ($key){
                $key->reason_name = $key->ReasonName;
                return $key;
            });
            if (!empty($source_cancel_reasons)){
                foreach ($source_cancel_reasons as $cancel_reason){
                    $cancel_reason_arr = $cancel_reason->toArray();
                    unset($cancel_reason_arr['id']);
                    unset($cancel_reason_arr['reason_name']);
                    unset($cancel_reason_arr['created_at']);
                    unset($cancel_reason_arr['updated_at']);
                    $cancel_reason_arr['merchant_id'] = $merchant->id;
                    $reason = CancelReason::create($cancel_reason_arr);
                    LanguageCancelReason::updateOrCreate([
                        'merchant_id' => $merchant->id, 'locale' => \App::getLocale(), 'cancel_reason_id' => $reason->id
                    ], [
                        'reason' => $cancel_reason->reason_name,
                    ]);
                }
            }

            // Store merchant as client for giving roles and permissions
            $client = (new Client)->forceFill([
                'user_id' => $merchant->id,
                'name' => $merchant->alias_name,
                'secret' => $this->ClientSecretKey(),
                'redirect' => 'http://localhost',
                'personal_access_client' => false,
                'password_client' => true,
                'revoked' => false,
            ]);
            $client->save();

            // Create role for merchant as super admin
            $role = Role::updateOrCreate(['merchant_id' => $merchant->id], ['name' => "Super Admin" . $merchant->id,'display_name' => "Super Admin",'description' => "Super Admin",'guard_name' => 'merchant']);
            // get all permissions
            $permissions = Permission::all();
            //Give permissions to merchant
            $role->givePermissionTo($permissions);
            // Assign role to merchant
            $merchant->assignRole($role->id);

            $source_application_theme = ApplicationTheme::where("merchant_id", $source_merchant->id)->first();
            if(!empty($source_application_theme)){
                $application_theme_arr = $source_application_theme->toArray();
                unset($application_theme_arr['id']);
                unset($application_theme_arr['created_at']);
                unset($application_theme_arr['updated_at']);
                $application_theme_arr['merchant_id'] = $merchant->id;
                $application_theme = ApplicationTheme::create($application_theme_arr);
            }

            // detach only super admin based services
            DB::table('merchant_service_type')->where('merchant_id', $merchant->id)
                ->join('service_types', 'service_types.id', '=', 'merchant_service_type.service_type_id')
                ->where('owner', 1)
                ->where('owner_id', NULL)
                ->delete();

            $arr_segment = [];
            $source_services = $source_merchant->ServiceType;
            foreach ($source_services as $service) {
                $seg_service = ServiceType::select('segment_id')->where('id', $service->id)->first();
                $arr_segment[] = $seg_service->segment_id;
                $merchant->ServiceType()->attach($service->id, ['segment_id' => $seg_service->segment_id]);
            }
            $merchant->Segment()->sync(array_unique($arr_segment));

            // Sync languages
            $languages = $source_merchant->Language->pluck("id")->toArray();
            if(!empty($languages)){
                $merchant->Language()->sync($languages);
            }
            // Sync payment methods
            $payment_methods = $source_merchant->PaymentMethod->pluck("id")->toArray();
            if(!empty($languages)){
                $merchant->PaymentMethod()->sync($payment_methods);
            }
            // Sync Rate cards
            $rate_cards = $source_merchant->RateCard->pluck("id")->toArray();
            if(!empty($rate_cards)){
                $merchant->RateCard()->sync($rate_cards);
            }
            // Sync payment options
            $payment_options = $source_merchant->PaymentOption->pluck("id")->toArray();
            if(!empty($payment_options)){
                $merchant->PaymentOption()->sync($payment_options);
            }
            // Sync Application navigation drawer
            $nav_drawer = $source_merchant->AppNavigationDrawer->pluck("id")->toArray();
            if(!empty($nav_drawer)){
                $sync_data = $merchant->AppNavigationDrawer()->sync($nav_drawer);
                if (!empty($sync_data['attached'])):
                    foreach ($sync_data['attached'] as $key => $value):
                        $created_new = MerchantNavDrawer::with('AppNavigationDrawer')->where([['merchant_id', $merchant->id], ['app_navigation_drawer_id', $value]])->select(['id', 'app_navigation_drawer_id'])->first();
                        $language_nav = new LangAppNavDrawer([
                            'merchant_id' => $merchant->id,
                            'merchant_nav_drawer_id' => $created_new->id,
                            'locale' => 'en',
                            'name' => $created_new->AppNavigationDrawer->name
                        ]);
                        $created_new->LanguageAppNavigationDrawers()->save($language_nav);
                    endforeach;
                endif;
            }

            //updating data from source to target
            $source_merchant_id = $source_merchant->id;
            $source_countries = NULL;
            if($request->countries == "on"){
                $source_countries = $this->getSourceCountries($source_merchant_id);
            }

            $source_documents = NULL;
            if($request->documents == "on"){
                $source_documents = $this->getSourceDocuments($source_merchant_id);
            }

            $source_vehicle_types = NULL;
            if($request->vehicle_types == "on"){
                $source_vehicle_types = $this->getSourceVehicleTypes($source_merchant_id);
            }

            $source_vehicle_makes = NULL;
            if($request->vehicle_make == "on"){
                $source_vehicle_makes = $this->getSourceVehicleMakes($source_merchant_id);
            }

            $source_vehicle_models = NULL;
            if($request->vehicle_models == "on"){
                $source_vehicle_models = $this->getSourceVehicleModels($source_merchant_id);
            }

            $source_account_types = NULL;
            if($request->account_types == "on"){
                $source_account_types = $this->getSourceAccountTypes($source_merchant_id);
            }
            
            $source_pricing_parameter = NULL;
            if($request->pricing_parameter == "on"){
                $source_pricing_parameter = $this->getSourcePricingParameter($source_merchant_id);
            }

            //inserting Countries data
            if (!empty($source_countries)){
                $mapped_country_array = $this->uploadCountries($source_countries, $merchant->id);
            }

            //inserting Documents data
            if (!empty($source_documents)){
                $mapped_document_array = $this->uploadDocuments($source_documents, $merchant->id);
            }

            //inserting Vehicle Types data
            if (!empty($source_vehicle_types)){
                $mapped_vehicle_type_array = $this->uploadVehicleTypes($source_vehicle_types, $merchant->id);
            }

            //inserting Vehicle Makes data
            if (!empty($source_vehicle_makes)){
                $mapped_vehicle_make_array = $this->uploadVehicleMakes($source_vehicle_makes, $merchant->id);
            }

            //inserting Vehicle Models data
            if (!empty($source_vehicle_models) && !empty($mapped_vehicle_type_array) && !empty($mapped_vehicle_make_array)){
                $mapped_vehicle_model_array = $this->uploadVehicleModels($source_vehicle_models, $merchant->id, $mapped_vehicle_type_array, $mapped_vehicle_make_array);
            }

            //inserting Account Types data
            if (!empty($source_account_types)){
                $mapped_account_type_array = $this->uploadAccountTypes($source_account_types, $merchant->id);
            }
            
            if (!empty($source_pricing_parameter)){
                $mapped_pricing_parameter_array = $this->uploadPricingParameter($source_pricing_parameter, $merchant->id);
            }

            DB::table('version_managements')->updateOrInsert(['merchant_id' => $merchant->id], ['api_version' => 0.1]);

//            p($merchant);
            DB::commit();
            return redirect()->route("sp-admin.home")->withSuccess("Merchant created successfully");
        }catch (\Exception $exception){
            DB::rollback();
//            p($exception->getMessage());
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }

    /**
     * Helper Functions
     */
    protected function secretkey($length = 30)
    {
        $secret_generate = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789efghijklmnopqrstuvwxyZ"), 0, $length);
        if (Merchant::where('merchantSecretKey', '=', $secret_generate)->exists()) :
            $this->secretkey();
        endif;
        return $secret_generate;
    }

    protected function publickey($length = 30)
    {
        $public_generate = substr(str_shuffle("ABCDEFGHIJKLMNOPQRST0123456789ijklmnopqrstuvwxyZ"), 0, $length);
        if (Merchant::where('merchantPublicKey', '=', $public_generate)->exists()) :
            $this->publickey();
        endif;
        return $public_generate;
    }

    protected function accessPin()
    {
        $pin_generate = rand(10000001, 99999999);
        if (Merchant::where('access_pin', '=', $pin_generate)->exists()) :
            $this->accessPin();
        endif;
        return $pin_generate;
    }

    protected function uploadImage($image, $dir = 'images', $merchant_id = null, $image_type = 'single', $merchant = false, $alias_name = '')
    {
        $name = "";
        if ($image_type == 'multiple') {
            $file = $image; // its name of image
        } else {
            if (request()->hasFile($image)) {
                $file = request()->file($image); // its name of image's field
            }
        }
        if ($file) {
            $upload_path = \Config::get('custom.' . $dir);
            if ($merchant) {
                if (!empty($alias_name)) {
                    // its case when merchant is creating and pass alias name directly
                    $alias = $alias_name;
                } else {
                    $id = $merchant_id ? $merchant_id : get_merchant_id();
                    $merchant = Merchant::Find($id);
                    $alias = $merchant->alias_name;
                }
                $alias = $alias . $upload_path['path'];
            } else {
                $alias = $upload_path['path'];
            }
            $name = time() . "_" . uniqid() . '_' . $dir . '.' . $file->getClientOriginalExtension();
            $filePath = $alias . $name;
            \Illuminate\Support\Facades\Storage::disk('s3')->put($filePath, file_get_contents($file));
        }
        return $name;
    }

    protected function ClientSecretKey()
    {
        $secret_key_generate = str_random(40);
        if (Client::where([['secret', '=', $secret_key_generate], ['password_client', '=', 1]])->exists()) :
            $this->ClientSecretKey();
        endif;
        return $secret_key_generate;
    }

    /*
     * End - Helper Functions
     */

    public function out()
    {
        \Session::forget('sp-token');
        return redirect()->route("sp-admin")->withSuccess("Logged out successfully");
    }

    public function CopyOtherDBData(){
        $merchants = Merchant::where([['merchantStatus','=',1],['parent_id','=',0]])->get()->pluck("BusinessName", "id")->toArray();
        return view("sp_admin.copy-other-db", compact("merchants"));
    }

    public function connectSourceDB(Request $request){
        Config::set('database.connections.mysql', array(
            'driver' => 'mysql',
            'host' => $request->ip,
            'port' => 3306,
            'database' => $request->db_name,
            'username' => $request->username,
            'password' => $request->password,
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ));
        DB::purge('mysql');
        DB::reconnect('mysql');

        if($request->calling_from != "FUNCTION"){
            $merchants = Merchant::select("BusinessName","id")->where([['merchantStatus','=',1],['parent_id','=',0]])->get();
            $merchantData = "<option value=''>Select Merchant</option>";
            foreach($merchants as $merchant){
                $merchantData .= "<option value='" . $merchant['id'] . "'>" . $merchant['BusinessName'] . "</option>";
            }
            return array('result' => 1, 'message' => "Success!", 'merchants' => $merchantData);
        }
    }

    public function connectTargetDB(){
        Config::set('database.connections.mysql', array(
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ));
        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    public function uploadSourceDB(Request $request){
        $request->validate([
            'source_merchant_id' => 'required',
            'target_merchant_id' => 'required',
            'ip_address' => 'required',
            'db_name' => 'required',
            'username' => 'required',
            'password' => 'required',
        ]);

        $request->merge(['calling_from' => 'FUNCTION', 'ip' => $request->ip_address]);
        $this->connectSourceDB($request);
        // dd(DB::connection()->getDatabaseName());
        $source_merchant_id = $request->source_merchant_id;
        $source_countries = NULL;
        if($request->countries == "on"){
            $source_countries = $this->getSourceCountries($source_merchant_id);
        }

        $source_documents = NULL;
        if($request->documents == "on"){
            $source_documents = $this->getSourceDocuments($source_merchant_id);
        }

        $source_vehicle_types = NULL;
        if($request->vehicle_types == "on"){
            $source_vehicle_types = $this->getSourceVehicleTypes($source_merchant_id);
        }

        $source_vehicle_makes = NULL;
        if($request->vehicle_make == "on"){
            $source_vehicle_makes = $this->getSourceVehicleMakes($source_merchant_id);
        }

        $source_vehicle_models = NULL;
        if($request->vehicle_models == "on"){
            $source_vehicle_models = $this->getSourceVehicleModels($source_merchant_id);
        }

        $source_account_types = NULL;
        if($request->account_types == "on"){
            $source_account_types = $this->getSourceAccountTypes($source_merchant_id);
        }
        
        $source_users = NULL;
        if ($request->users == "on"){
            $source_users = $this->getSourceUsers($source_merchant_id);
        }
        
        $source_pricing_parameter = NULL;
        if($request->pricing_parameter == "on"){
                $source_pricing_parameter = $this->getSourcePricingParameter($source_merchant_id);
        }

        $source_drivers = NULL;
        if($request->drivers == "on"){
                $source_drivers = $this->uploadDrivers();
        }
        
        $source_store = NULL;
        if($request->copy_store_product == "on"){
            $source_store = $this->getSourceStoreProduct($source_merchant_id);
        }


        DB::beginTransaction();
        try{
            $this->connectTargetDB();
            //inserting Countries data
            if (!empty($source_countries)){
                $mapped_country_array = $this->uploadCountries($source_countries, $request->target_merchant_id);
            }

            //inserting Documents data
            if (!empty($source_documents)){
                $mapped_document_array = $this->uploadDocuments($source_documents, $request->target_merchant_id);
            }

            //inserting Vehicle Types data
            if (!empty($source_vehicle_types)){
                $mapped_vehicle_type_array = $this->uploadVehicleTypes($source_vehicle_types, $request->target_merchant_id);
            }

            //inserting Vehicle Makes data
            if (!empty($source_vehicle_makes)){
                $mapped_vehicle_make_array = $this->uploadVehicleMakes($source_vehicle_makes, $request->target_merchant_id);
            }

            //inserting Vehicle Models data
            if (!empty($source_vehicle_models) && !empty($mapped_vehicle_type_array) && !empty($mapped_vehicle_make_array)){
                $mapped_vehicle_model_array = $this->uploadVehicleModels($source_vehicle_models, $request->target_merchant_id, $mapped_vehicle_type_array, $mapped_vehicle_make_array);
            }

            //inserting Account Types data
            if (!empty($source_account_types)){
                $mapped_account_type_array = $this->uploadAccountTypes($source_account_types, $request->target_merchant_id);
            }
            
            //inserting Users data
            if (!empty($source_users)){
                $mapped_account_type_array = $this->uploadUsers($source_users, $request->target_merchant_id, $mapped_country_array);
            }
            
            if (!empty($source_pricing_parameter)){
                $mapped_pricing_parameter_array = $this->uploadPricingParameter($source_pricing_parameter, $merchant->id);
            }
            
            if (!empty($source_store)){
                $mapped_store_product_array = $this->uploadStoreAndProduct($source_store, $request->target_merchant_id,$request);
            }

            DB::commit();
            return redirect()->route("sp-admin.home")->withSuccess("Merchant Data Copied successfully");
        }catch (\Exception $exception){
            DB::rollback();
            throw $exception;
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }

    public function getSourceCountries($source_merchant_id){
        $source_countries = Country::where([['country_status','=',1],['merchant_id','=', $source_merchant_id]])->get();
        $source_countries = $source_countries->map(function ($key){
            $key->country_name = $key->CountryName;
            return $key;
        });
        return $source_countries;
    }

    public function getSourceDocuments($source_merchant_id){
        $source_documents = Document::where([['documentStatus','=',1],['merchant_id','=', $source_merchant_id]])->get();
        $source_documents = $source_documents->map(function ($key){
            $key->document_name = $key->DocumentName;
            return $key;
        });
        return $source_documents;
    }

    public function getSourceVehicleTypes($source_merchant_id){
        //for multi service database
        // $source_vehicle_types = VehicleType::where([['admin_delete','=',NULL],['merchant_id','=', $source_merchant_id]])->get(); 
        //for socket database
        $source_vehicle_types = VehicleType::where([['vehicleTypeStatus','=',1],['merchant_id','=', $source_merchant_id]])->get();
        $source_vehicle_types = $source_vehicle_types->map(function ($key){
            $key->vehicle_type_name = $key->VehicleTypeName;
            $key->vehicle_type_description = $key->VehicleTypeDescription;
            return $key;
        });
        return $source_vehicle_types;
    }

    public function getSourceVehicleMakes($source_merchant_id){
        //for multi service database
        // $source_vehicle_makes = VehicleMake::where([['admin_delete','=',NULL],['merchant_id','=', $source_merchant_id]])->get();
        //for socket database
        $source_vehicle_makes = VehicleMake::where([['vehicleMakeStatus','=',1],['merchant_id','=', $source_merchant_id]])->get();
        $source_vehicle_makes = $source_vehicle_makes->map(function ($key){
            $key->vehicle_make_name = $key->VehicleMakeName;
            $key->vehicle_make_description = $key->VehicleMakeDescription;
            return $key;
        });
        return $source_vehicle_makes;
    }

    public function getSourceVehicleModels($source_merchant_id){
        //for multi service database
        // $source_vehicle_models = VehicleModel::where([['admin_delete','=',NULL],['merchant_id','=', $source_merchant_id]])->get();
        //for socket database
        $source_vehicle_models = VehicleModel::where([['vehicleModelStatus','=',1],['merchant_id','=', $source_merchant_id]])->get();
        $source_vehicle_models = $source_vehicle_models->map(function ($key){
            $key->vehicle_model_name = $key->VehicleModelName;
            $key->vehicle_model_description = $key->VehicleModelDescription;
            return $key;
        });
        return $source_vehicle_models;
    }

    public function getSourceAccountTypes($source_merchant_id){
        $source_account_types = AccountType::where([['admin_delete','=',0],['merchant_id','=', $source_merchant_id]])->get();
        $source_account_types = $source_account_types->map(function ($key){
            $key->account_type_name = $key->Name;
            return $key;
        });
        return $source_account_types;
    }
    
    public function getSourceUsers($source_merchant_id){
        $source_users = User::where([['merchant_id', '=', $source_merchant_id],['user_delete', '=', NULL]])->get();
        return $source_users;
    }
    
    public function getSourcePricingParameter($source_merchant_id){
        $source_pricing_parameter = PricingParameter::where([['merchant_id','=', $source_merchant_id]])->get();
        $source_pricing_parameter = $source_pricing_parameter->map(function ($key){
            $key->parameter_name = $key->ParameterName;
            $key->parameter_name_application = $key->ParameterApplication;
            return $key;
        });
        return $source_pricing_parameter;
    }
    
    public function getSourceStoreProduct($source_merchant_id){
        $source_store_and_product = BusinessSegment::with('Product')->where(['merchant_id' => $source_merchant_id])->get();
        return $source_store_and_product;
    }

    public function uploadCountries($source_countries, $target_merchant_id){
        $mapped_country_array = [];
        foreach ($source_countries as $source_country){
            $country = Country::create([
                'merchant_id' => $target_merchant_id,
                'country_code' => $source_country->country_code,
                'isoCode' => $source_country->isoCode,
                'phonecode' => $source_country->phonecode,
                'distance_unit' => $source_country->distance_unit,
                'default_language' => $source_country->default_language,
                'maxNumPhone' => $source_country->maxNumPhone,
                'minNumPhone' => $source_country->minNumPhone,
            ]);

            LanguageCountry::updateOrCreate([
                'merchant_id' => $target_merchant_id, 'locale' => 'en', 'country_id' => $country->id
            ], [
                'name' => $source_country->country_name,
            ]);

            $mapped_country_array[] = ['source_id' => $source_country->id, 'target_id' => $country->id];
        }
        return $mapped_country_array;
    }

    public function uploadDocuments($source_documents, $target_merchant_id){
        $mapped_document_array = [];
        foreach ($source_documents as $source_document){
            $document = Document::create([
                'merchant_id' => $target_merchant_id,
                'expire_date' => $source_document->expire_date,
                'documentStatus' => $source_document->documentStatus,
                'documentNeed' => $source_document->documentNeed,
                'document_number_required' => $source_document->document_number_required,
            ]);

            LanguageDocument::updateOrCreate([
                'merchant_id' => $target_merchant_id, 'locale' => 'en', 'document_id' => $document->id
            ], [
                'documentname' => $source_document->document_name,
            ]);

            $mapped_document_array[] = ['source_id' => $source_document->id, 'target_id' => $document->id];
        }
        return $mapped_document_array;
    }

    public function uploadVehicleTypes($source_vehicle_types, $target_merchant_id){
        $mapped_vehicle_type_array = [];
        foreach ($source_vehicle_types as $source_vehicle_type){
            $vehicle_type = VehicleType::create([
                'merchant_id' => $target_merchant_id,
                'vehicleTypeImage' => $source_vehicle_type->vehicleTypeImage,
                'vehicleTypeDeselectImage' => $source_vehicle_type->vehicleTypeDeselectImage,
                'vehicleTypeMapImage' => $source_vehicle_type->vehicleTypeMapImage,
                'vehicleTypeRank' => $source_vehicle_type->vehicleTypeRank,
                'vehicleTypeStatus' => $source_vehicle_type->vehicleTypeStatus,
                'pool_enable' => $source_vehicle_type->pool_enable,
                'sequence' => $source_vehicle_type->sequence,
                'rating' => $source_vehicle_type->rating,
                'ride_now' => $source_vehicle_type->ride_now,
                'ride_later' => $source_vehicle_type->ride_later,
                'model_expire_year' => $source_vehicle_type->model_expire_year,
            ]);

            LanguageVehicleType::updateOrCreate([
                'merchant_id' => $target_merchant_id, 'locale' => 'en', 'vehicle_type_id' => $vehicle_type->id
            ], [
                'vehicleTypeName' => $source_vehicle_type->vehicle_type_name,
                'vehicleTypeDescription' => $source_vehicle_type->vehicle_type_description,
            ]);

            $mapped_vehicle_type_array[] = ['source_id' => $source_vehicle_type->id, 'target_id' => $vehicle_type->id];
        }
        return $mapped_vehicle_type_array;
    }

    public function uploadVehicleMakes($source_vehicle_makes, $target_merchant_id){
        $mapped_vehicle_make_array = [];
        foreach ($source_vehicle_makes as $source_vehicle_make){
            $vehicle_make = VehicleMake::create([
                'merchant_id' => $target_merchant_id,
                'vehicleMakeLogo' => $source_vehicle_make->vehicleMakeLogo,
                'vehicleMakeStatus' => $source_vehicle_make->vehicleMakeStatus
            ]);

            LanguageVehicleMake::updateOrCreate([
                'merchant_id' => $target_merchant_id, 'locale' => 'en', 'vehicle_make_id' => $vehicle_make->id
            ], [
                'vehicleMakeName' => $source_vehicle_make->vehicle_make_name,
                'vehicleMakeDescription' => $source_vehicle_make->vehicle_make_description,
            ]);

            $mapped_vehicle_make_array[] = ['source_id' => $source_vehicle_make->id, 'target_id' => $vehicle_make->id];
        }
        return $mapped_vehicle_make_array;
    }

    public function uploadVehicleModels($source_vehicle_models, $target_merchant_id, $mapped_vehicle_type_array, $mapped_vehicle_make_array){
        $mapped_vehicle_model_array = [];
        foreach ($source_vehicle_models as $source_vehicle_model){
            $vehicle_model = VehicleModel::create([
                'merchant_id' => $target_merchant_id,
                'vehicle_type_id' => $this->getTargetId($source_vehicle_model->vehicle_type_id,$mapped_vehicle_type_array),
                'vehicle_make_id' => $this->getTargetId($source_vehicle_model->vehicle_make_id,$mapped_vehicle_make_array),
                'vehicle_seat' => $source_vehicle_model->vehicle_seat,
                'vehicleModelStatus' => $source_vehicle_model->vehicleModelStatus
            ]);

            LanguageVehicleModel::updateOrCreate([
                'merchant_id' => $target_merchant_id, 'locale' => 'en', 'vehicle_model_id' => $vehicle_model->id
            ], [
                'vehicleModelName' => $source_vehicle_model->vehicle_model_name,
                'vehicleModelDescription' => $source_vehicle_model->vehicle_model_description,
            ]);

            $mapped_vehicle_model_array[] = ['source_id' => $source_vehicle_model->id, 'target_id' => $vehicle_model->id];
        }
        return $mapped_vehicle_model_array;
    }

    public function uploadAccountTypes($source_account_types, $target_merchant_id){
        $mapped_account_type_array = [];
        foreach ($source_account_types as  $source_account_type){
            $account_type = AccountType::create([
                'merchant_id' => $target_merchant_id,
                'status' => $source_account_type->status
            ]);

            $this->saveAccountTypeLang($target_merchant_id, $source_account_type->account_type_name, $account_type);
            $mapped_account_type_array[] = ['source_id' => $source_account_type->id, 'target_id' => $account_type->id];
        }
        return $mapped_account_type_array;
    }

    public function saveAccountTypeLang($target_merchant_id, $name, AccountType $model_data){
        $language_data = new LangName([
            'merchant_id' => $target_merchant_id,
            'locale' => 'en',
            'name' => $name,
        ]);
        $model_data->LangAccountTypes()->save($language_data);
    }
    
    public function uploadUsers($source_users, $target_merchant_id, $mapped_country_array){
        $mapped_user_array = [];
        foreach ($source_users as $source_user){
            
            $existing_user = User::where("UserPhone", $source_user->UserPhone)->first();
            if(empty($existing_user)){
                
                $user = User::create([
                    'merchant_id' => $target_merchant_id,
                    'country_id' => $this->getTargetId($source_user->country_id,$mapped_country_array),
                    'first_name' => $source_user->first_name,
                    'last_name' => $source_user->last_name,
                    'UserPhone' => $source_user->UserPhone,
                    'email' => $source_user->email,
                    'password' => $source_user->password,
                    'ReferralCode' => $source_user->ReferralCode,
                ]);
    
                $mapped_user_array[] = ['source_id' => $source_user->id, 'target_id' => $user->id];
            }
        }
        return $mapped_user_array;
    }

    public function getTargetId($id, $mapped_array){
        $key = array_search($id,array_column($mapped_array,'source_id'));
        $target_id = $mapped_array[$key]['target_id'];
        return $target_id;
    }
    
    public function uploadPricingParameter($source_pricing_parameter, $target_merchant_id){
        $mapped_pricing_parameter_array = [];
        foreach ($source_pricing_parameter as $source_price){
            // dd($source_price->PriceCardValue,$source_price);
            $pricing_param = new PricingParameter();
            $pricing_param->parameterType = $source_price->parameterType;
            $pricing_param->merchant_id = $target_merchant_id;
            $pricing_param->sequence_number = $source_price->sequence_number;
            $pricing_param->applicable = $source_price->applicable;
            $pricing_param->parameterStatus = $source_price->parameterStatus;
            $pricing_param->save();
            $pricing_param->fresh();
            
            $segmentIds = [];
            foreach($source_price->Segment as $segment){
                $segmentIds[] = $segment['id'];
            }
            
            $pricing_param->Segment()->sync($segmentIds);
            
            if(isset($source_price->PricingType) && count($source_price->PricingType) > 0 ){
                foreach ($source_price->PricingType as $price_type) {
                    PricingParameterValue::create(['price_type' => $price_type->price_type, 'pricing_parameter_id' => $pricing_param->id]);
                }
            }
            
            LanguagePricingParameter::updateOrCreate([
                'merchant_id' => $target_merchant_id, 'locale' => 'en', 'pricing_parameter_id' => $pricing_param->id
            ], [
                'parameterNameApplication' => $source_price->parameter_name_application,
                'parameterName' => $source_price->parameter_name,
            ]);

            $mapped_pricing_parameter_array[] = ['source_id' => $source_price->id, 'target_id' => $pricing_param->id];
        }
        return $mapped_pricing_parameter_array;
    }
    
    public function uploadStoreAndProduct($source_store,$target_merchant_id,$request){
        $merchant = Merchant::where('id', $target_merchant_id)->first();
        foreach ($source_store as $target_store){
            $bs = new BusinessSegment();
            $bs->merchant_id = $target_merchant_id;
            // $bs->country_area_id = 2156;
            // $bs->country_id = 2258;
            // $bs->country_area_id = 1464;
            // $bs->country_id = 1306;
            $bs->segment_id = $target_store->segment_id;
            $bs->parent_id = $target_store->parent_id;
            $bs->full_name = $target_store->full_name;
            $bs->alias_name = $target_store->alias_name;
            $bs->email = $target_store->email;
            $bs->password = $target_store->password;
            $bs->business_logo = $target_store->business_logo;
            $bs->phone_number = $target_store->phone_number;
            $bs->open_time = $target_store->open_time;
            $bs->close_time = $target_store->close_time;
            $bs->pin_code = $target_store->pin_code;
            $bs->state = $target_store->state;
            $bs->city = $target_store->city;
            $bs->address = $target_store->address;
            $bs->latitude = $target_store->latitude;
            $bs->longitude = $target_store->longitude;
            $bs->landmark = $target_store->landmark;
            $bs->commission_type = $target_store->commission_type;
            $bs->commission_method = $target_store->commission_method;
            $bs->commission = $target_store->commission;
            $bs->order_request_receiver = $target_store->order_request_receiver;
            $bs->status = $target_store->status;
            $bs->is_popular = $target_store->is_popular;
            $bs->delivery_time = $target_store->delivery_time;
            $bs->minimum_amount = $target_store->minimum_amount;
            $bs->minimum_amount_for = $target_store->minimum_amount_for;
            $bs->wallet_amount = $target_store->wallet_amount;
            $bs->login = $target_store->login;
            $bs->player_id = $target_store->player_id;
            $bs->unique_number = $target_store->unique_number;
            $bs->access_token_id = $target_store->access_token_id;
            $bs->device = $target_store->device;
            $bs->remember_token = $target_store->remember_token;
            $bs->rating = $target_store->rating;
            $bs->login_background_image = $target_store->login_background_image;
            $bs->bank_details = $target_store->bank_details;
            $bs->delivery_service = $target_store->delivery_service;
            $bs->created_at = date("Y-m-d H:i:s");
            $bs->updated_at = date("Y-m-d H:i:s");
            $bs->business_profile_image = $target_store->business_profile_image;
            $bs->subscription_expired = 2;
            $bs->save();
            $bs->fresh();

            foreach($target_store->Product as $prod){
                $this->connectSourceDB($request);
                
                $segId = $prod->segment_id;
                $skuId = $prod->sku_id;
                $covImage = $prod->product_cover_image;
                $tax = $prod->tax;
                $seq = $prod->sequence;
                $foodType = $prod->food_type;
                $del = $prod->delete;
                $dis = $prod->display_type;
                $manInv = $prod->manage_inventory;
                $prodName = $prod->langData(459)->name;
                $locale = $prod->langData(459)->locale;
                $desc = $prod->langData(459)->description;
                $ing = $prod->langData(459)->ingredients;
                
                $this->connectTargetDB();
                
                $targetProduct = new Product();
                $targetProduct->merchant_id = $target_merchant_id;
                $targetProduct->business_segment_id = $bs->id;
                // $targetProduct->category_id = 2886;
                $targetProduct->category_id = 625;
                $targetProduct->segment_id = $prod->segment_id;
                $targetProduct->sku_id = $prod->sku_id;
                $targetProduct->product_cover_image = $prod->product_cover_image;
                $targetProduct->product_preparation_time = null;
                $targetProduct->tax = $prod->tax;
                $targetProduct->sequence = $prod->sequence;
                $targetProduct->status = $prod->status;
                $targetProduct->food_type = $prod->food_type;
                $targetProduct->delete = $prod->delete;
                $targetProduct->display_type = $prod->display_type;
                $targetProduct->manage_inventory = $prod->manage_inventory;
                $targetProduct->created_at = date("Y-m-d H:i:s");
                $targetProduct->updated_at = date("Y-m-d H:i:s");
                $targetProduct->save();
                $targetProduct->fresh();

                LanguageProduct::updateOrCreate([
                    'merchant_id' => $target_merchant_id, 'locale' => $locale, 'product_id' => $targetProduct->id
                ], [
                    'business_segment_id' => $targetProduct->business_segment_id,
                    'name' => $prodName,
                    'description' => $desc,
                    'ingredients' => $ing,
                ]);

                // dd($targetProduct,$bs);
                
                $this->connectSourceDB($request);
                foreach($prod->ProductVariant as $prodVar){
                    $this->connectSourceDB($request);
                    // dd($prodVar,$targetProduct,$bs);
                    $sku = $prodVar->sku_id;
                    $prodTitle = $prodVar->product_title;
                    $prodPrice = $prodVar->product_price;
                    $dis = $prodVar->discount;
                    $weight = $prodVar->weight;
                    $title = $prodVar->is_title_show;
                    $status = $prodVar->status;
                    $deleted = $prodVar->deleted_at;
                    $prodVarName = $prodVar->Name(459);
                    
                    $this->connectTargetDB();
                    
                    $productVariant = new ProductVariant();
                    $productVariant->product_id = $targetProduct->id;
                    $productVariant->sku_id = $sku;
                    $productVariant->product_title = $prodTitle;
                    $productVariant->product_price = $prodPrice;
                    $productVariant->discount = $dis;
                    $productVariant->weight_unit_id = null;
                    $productVariant->weight = $weight;
                    $productVariant->is_title_show = $title;
                    $productVariant->status = $status;
                    $productVariant->deleted_at = $deleted;
                    $productVariant->created_at = date("Y-m-d H:i:s");
                    $productVariant->updated_at = date("Y-m-d H:i:s");
                    $productVariant->save();
                    $productVariant->fresh();
                    
                    
                    
                    LanguageProductVariant::updateOrCreate([
                        'merchant_id' => $target_merchant_id, 'locale' => $locale, 'product_variant_id' => $productVariant->id
                    ], [
                        'business_segment_id' => $targetProduct->business_segment_id,
                        'name' => $prodVarName,
                    ]);
                    
                    
                }
                
                $mapped_store_product_array[] = ['source_id' => $target_store->id, 'target_id' => $bs->id];
            }

            
        }

        return $mapped_store_product_array;
        
        
    }


    public function uploadDrivers()
    {
        ini_set('max_execution_time', '90000');
        $mapped_vehicle_type_array = [
            // ['source_id' => 276, 'target_id' => 1207],
            // ['source_id' => 307, 'target_id' => 1208],
            // ['source_id' => 379, 'target_id' => 1209],
            // ['source_id' => 380, 'target_id' => 1210],
            // ['source_id' => 387, 'target_id' => 1211],
            ['source_id' => 855, 'target_id' => 1481],
            ['source_id' => 856, 'target_id' => 1481],
            ['source_id' => 857, 'target_id' => 1481],
            ['source_id' => 888, 'target_id' => 1481],
            ['source_id' => 906, 'target_id' => 1481],
            ['source_id' => 908, 'target_id' => 1482],
            ['source_id' => 909, 'target_id' => 1481],
            ['source_id' => 910, 'target_id' => 1481],
            ['source_id' => 920, 'target_id' => 1481],
            ['source_id' => 942, 'target_id' => 1482],
            ['source_id' => 956, 'target_id' => 1481],

        ];

        $mapped_vehicle_make_array = [
            // ['source_id' => 475, 'target_id' => 2923],
            // ['source_id' => 500, 'target_id' => 2924],
            // ['source_id' => 668, 'target_id' => 2925],
            // ['source_id' => 669, 'target_id' => 2926],
            // ['source_id' => 670, 'target_id' => 2927],
            // ['source_id' => 671, 'target_id' => 2928],
            // ['source_id' => 672, 'target_id' => 2929],
            // ['source_id' => 673, 'target_id' => 2930],
            ['source_id' => 1965, 'target_id' => 3731],
            ['source_id' => 2033, 'target_id' => 3732],
            ['source_id' => 2040, 'target_id' => 3734],
            ['source_id' => 2044, 'target_id' => 3736],
            ['source_id' => 2065, 'target_id' => 3742],
            ['source_id' => 2151, 'target_id' => 3743],
        ];

        $mapped_vehicle_model_array = [
            // ['source_id' => 2077, 'target_id' => 9525],
            // ['source_id' => 2104, 'target_id' => 9526],
            // ['source_id' => 2638, 'target_id' => 9527],
            // ['source_id' => 2639, 'target_id' => 9528],
            // ['source_id' => 2678, 'target_id' => 9529],
            // ['source_id' => 2881, 'target_id' => 9530],
            // ['source_id' => 2924, 'target_id' => 9531],
            // ['source_id' => 6552, 'target_id' => 9532],
            // ['source_id' => 6553, 'target_id' => 9533],
            // ['source_id' => 6561, 'target_id' => 9534],
            // ['source_id' => 6562, 'target_id' => 9535],
            // ['source_id' => 6563, 'target_id' => 9536],
            // ['source_id' => 6564, 'target_id' => 9537],
            ['source_id' => 6443, 'target_id' => 12579],
            ['source_id' => 6444, 'target_id' => 12580],
            ['source_id' => 6445, 'target_id' => 12581],
            ['source_id' => 6622, 'target_id' => 12582],
            ['source_id' => 6661, 'target_id' => 12582],
            ['source_id' => 6710, 'target_id' => 12582],
            ['source_id' => 6710, 'target_id' => 12582],
            ['source_id' => 6714, 'target_id' => 12582],
            ['source_id' => 6798, 'target_id' => 12582],
            ['source_id' => 6990, 'target_id' => 12582],
        ];

        $mapped_document_array = [
            // ['source_id' => 232, 'target_id' => 1311],
            // ['source_id' => 233, 'target_id' => 1310],
            // ['source_id' => 244, 'target_id' => 1309],
            // ['source_id' => 293, 'target_id' => 1308],
            // ['source_id' => 294, 'target_id' => 1307],
            // ['source_id' => 295, 'target_id' => 1306],
            // ['source_id' => 296, 'target_id' => 1304],
            // ['source_id' => 324, 'target_id' => 1303],
            // ['source_id' => 325, 'target_id' => 1305],
            ['source_id' => 763, 'target_id' => 1624],
            ['source_id' => 764, 'target_id' => 1625],
            ['source_id' => 792, 'target_id' => 1626],
            ['source_id' => 812, 'target_id' => 1627],
            ['source_id' => 813, 'target_id' => 1628],
            ['source_id' => 824, 'target_id' => 1629],
        ];

        // DB::beginTransaction();
        try {
            $drivers = \App\Models\Driver::where('driver_delete', NULL)->where("merchant_id", 424)->get();
            foreach ($drivers as $driver) {
                $driver_arr = $driver->toArray();
                unset($driver_arr['id']);
                unset($driver_arr['created_at']);
                unset($driver_arr['updated_at']);
                $driver_arr['merchant_id'] = 756; //taget merchant
                $driver_arr['country_id'] = 1763;
                $driver_arr['country_area_id'] = 1898;
                $driver_arr['account_type_id'] = NULL;
                $driver_arr['taxi_company_id'] = NULL;
                $driver_arr['created_at'] = date('y-m-d H:i:s');
                $driver_new = DB::connection('mysql3')->table('drivers')->insertGetId($driver_arr);

                if (!empty($driver->DriverDocument->toArray())) {
                    foreach ($driver->DriverDocument as $doc) {
                        $doc_arr = $doc->toArray();
                        unset($doc_arr['id']);
                        unset($doc_arr['created_at']);
                        unset($doc_arr['updated_at']);
                        $doc_arr['driver_id'] = $driver_new;
                        $doc_arr['document_id'] = $this->getTargetId($doc->document_id, $mapped_document_array);
                        DB::connection('mysql3')->table('driver_documents')->insert($doc_arr);
                    }
                    // p('Driver Document Inserted.', 0);
                }

                if (!empty($driver->Segment->toArray())) {
                    foreach ($driver->Segment as $segment) {
                        DB::connection('mysql3')->table('driver_segment')->insert([
                            'driver_id' => $driver_new,
                            'segment_id' => $segment->id
                        ]);
                    }
                    // p('Driver Segment Inserted.', 0);
                }

                if (!empty($driver->DriverSegmentDocument->toArray())) {
                    foreach ($driver->DriverSegmentDocument as $driver_seg_doc) {
                        $segment_doc_arr = $driver_seg_doc->toArray();
                        unset($segment_doc_arr['id']);
                        unset($segment_doc_arr['created_at']);
                        unset($segment_doc_arr['updated_at']);
                        $segment_doc_arr['driver_id'] = $driver_new;
                        $segment_doc_arr['document_id'] = $this->getTargetId($driver_seg_doc->document_id, $mapped_document_array);
                        DB::connection('mysql3')->table('driver_segment_documents')->insert($segment_doc_arr);
                    }
                    // p('Driver Segment Document Inserted.', 0);
                }

                if (!empty($driver->ServiceType->toArray())) {
                    foreach ($driver->ServiceType as $service_type) {
                        $driver_service_type = $this->fetchTargetServiceType($service_type->pivot->service_type_id);
                        if (!empty($driver_service_type)) {
                            DB::connection('mysql3')->table('driver_service_type')->insert([
                                'driver_id' => $driver_new,
                                'segment_id' => $service_type->pivot->segment_id,
                                'service_type_id' => $service_type->pivot->service_type_id,
                            ]);
                        }
                    }
                    // p('Driver ServiceType Inserted.', 0);
                }

                if (!empty($driver->DriverVehicles->toArray())) {
                    foreach ($driver->DriverVehicles as $vehicle) {
                        $driver_vehicle_arr = $vehicle->toArray();
                        unset($driver_vehicle_arr['id']);
                        unset($driver_vehicle_arr['created_at']);
                        unset($driver_vehicle_arr['updated_at']);
                        $driver_vehicle_arr['merchant_id'] = 756;
                        $driver_vehicle_arr['driver_id'] = $driver_new;
                        $driver_vehicle_arr['vehicle_type_id'] = $this->getTargetId($vehicle->vehicle_type_id, $mapped_vehicle_type_array);
                        $driver_vehicle_arr['vehicle_make_id'] = $this->getTargetId($vehicle->vehicle_make_id, $mapped_vehicle_make_array);
                        $driver_vehicle_arr['vehicle_model_id'] = $this->getTargetId($vehicle->vehicle_model_id, $mapped_vehicle_model_array);
                        $driver_vehicle_id = DB::connection('mysql3')->table('driver_vehicles')->insertGetId($driver_vehicle_arr);

                        if (!empty($vehicle->ServiceTypes->toArray())) {
                            foreach ($vehicle->ServiceTypes as $serviceType) {
                                $driver_veh_service_type = $this->fetchTargetServiceType($serviceType->pivot->service_type_id);
                                if (!empty($driver_veh_service_type)) {
                                    DB::connection('mysql3')->table('driver_vehicle_service_type')->insert([
                                        'driver_vehicle_id' => $driver_vehicle_id,
                                        'service_type_id' => $serviceType->pivot->service_type_id,
                                        'segment_id' => $serviceType->pivot->segment_id,
                                    ]);
                                }
                            }
                            // p('Driver Vehicle ServiceType Inserted.', 0);
                        }

                        if (!empty($vehicle->Drivers->toArray())) {
                            DB::connection('mysql3')->table('driver_driver_vehicle')->insert([
                                'driver_id' => $driver_new,
                                'driver_vehicle_id' => $driver_vehicle_id,
                                'vehicle_active_status' => $vehicle->Drivers[0]->pivot->vehicle_active_status
                            ]);
                        }

                        if (!empty($vehicle->DriverVehicleDocument->toArray())) {
                            foreach ($vehicle->DriverVehicleDocument as $vehicle_doc) {
                                $vehicle_doc_arr = $vehicle_doc->toArray();
                                unset($vehicle_doc_arr['id']);
                                unset($vehicle_doc_arr['created_at']);
                                unset($vehicle_doc_arr['updated_at']);
                                $vehicle_doc_arr['driver_vehicle_id'] = $driver_vehicle_id;
                                $vehicle_doc_arr['document_id'] = $this->getTargetId($vehicle_doc->document_id, $mapped_document_array);
                                DB::connection('mysql3')->table('driver_vehicle_documents')->insert($vehicle_doc_arr);
                            }
                            // p('Driver Vehicle Document Inserted.', 0);
                        }
                    }
                    // p('Driver Vehicle Inserted.', 0);
                }
                \Log::channel('driver_copy_data')->emergency(['driver_id' => $driver_new, 'message' => "Copied!"]);
            }
            die('copied done');
            // p('Driver Inserted successfully', 0);
            // DB::commit();
            // return redirect()->back()->withSuccess("Driver & Vehicle Copied successfully");
        } catch (\Exception $exception) {
            // DB::rollback();
            dd($exception);
            // return redirect()->back()->withErrors($exception->getMessage());
        }
    }

    public function fetchTargetServiceType($source_service_id)
    {
        $this->connectTargetDB();
        $service_type = ServiceType::find($source_service_id);
        Config::set('database.connections.mysql', array(
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ));
        DB::purge('mysql');
        DB::reconnect('mysql');
        return $service_type;
    }
}
