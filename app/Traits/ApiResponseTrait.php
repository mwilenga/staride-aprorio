<?php

namespace App\Traits;
use App\Models\CountryArea;
use DB;
use App\Models\BannerManagement;
use App\Models\ServiceType;
use App;

trait ApiResponseTrait{

    protected function failedResponse($message, $data = [])
    {
        $api_version = "1.5";
        $string_version = "0";
        if(isset(request()->merchant_id)){
            $merchant_id = request()->merchant_id;
            $version_management = App\Models\VersionManagement::where('merchant_id',$merchant_id)->first();
            $api_version = !empty($version_management->id) ? "$version_management->api_version" : $api_version;
            $string_version_management = App\Models\Merchant::findOrFail($merchant_id);
            $string_version=!empty($string_version_management->id) ? "$string_version_management->app_string_versions" : $string_version;
        }
        return response()->json(['version' => $api_version,'string_version' => $string_version,"result" => "0", 'message' => $message]);
    }
    protected function successResponse($message, $data = [])
    {
        $api_version = "1.5";
        $string_version="0";
        if(isset(request()->merchant_id)){
            $merchant_id = request()->merchant_id;
            $version_management = App\Models\VersionManagement::where('merchant_id',$merchant_id)->first();
            $api_version = !empty($version_management->id) ? "$version_management->api_version" : $api_version;
            $string_version_management = App\Models\Merchant::findOrFail($merchant_id);
            $string_version=!empty($string_version_management->id) ? "$string_version_management->app_string_versions" : $string_version;
        }
        return response()->json(['version' => $api_version,'string_version' => $string_version,"result" => "1", 'message' => $message, 'data' => $data]);
    }
    protected function successfullResponse($message, $data = [], $additional_data =[])
    {
        $api_version = "1.5";
        $string_version="0";
        if(isset(request()->merchant_id)){
            $merchant_id = request()->merchant_id;
            $version_management = App\Models\VersionManagement::where('merchant_id',$merchant_id)->first();
            $api_version = !empty($version_management->id) ? "$version_management->api_version" : $api_version;
            $string_version_management = App\Models\Merchant::findOrFail($merchant_id);
            $string_version=!empty($string_version_management->id) ? "$string_version_management->app_string_versions" : $string_version;
        }
        return response()->json(['version' => $api_version,'string_version' => $string_version,"result" => "1", 'message' => $message, 'data' => $data, 'additional_data'=>$additional_data]);
    }

    protected function pendingResponse($message)
    {
        $api_version = "1.5";
        $string_version="0";
        if(isset(request()->merchant_id)){
            $merchant_id = request()->merchant_id;
            $version_management = App\Models\VersionManagement::where('merchant_id',$merchant_id)->first();
            $api_version = !empty($version_management->id) ? "$version_management->api_version" : $api_version;
            $string_version_management = App\Models\Merchant::findOrFail($merchant_id);
            $string_version=!empty($string_version_management->id) ? "$string_version_management->app_string_versions" : $string_version;
        }
        return response()->json(['version' => $api_version,'string_version' => $string_version,"result" => "2", 'message' => $message]);
    }
    protected function failedResponseWithData($message, $data = [])
    {
        $api_version = "1.5";
        $string_version="0";
        if(isset(request()->merchant_id)){
            $merchant_id = request()->merchant_id;
            $version_management = App\Models\VersionManagement::where('merchant_id',$merchant_id)->first();
            $api_version = !empty($version_management->id) ? "$version_management->api_version" : $api_version;
            $string_version_management = App\Models\Merchant::findOrFail($merchant_id);
            $string_version=!empty($string_version_management->id) ? "$string_version_management->app_string_versions" : $string_version;
        }
        return response()->json(['version' => $api_version,'string_version' => $string_version,"result" => "0", 'message' => $message, 'data'=>$data]);
    }
     protected function compressedSuccessResponse($data = [])
    {
        $api_version = "1.5";
        $string_version="0";
        if(isset(request()->merchant_id)){
            $merchant_id = request()->merchant_id;
            $version_management = App\Models\VersionManagement::where('merchant_id',$merchant_id)->first();
            $api_version = !empty($version_management->id) ? "$version_management->api_version" : $api_version;
            $string_version_management = App\Models\Merchant::findOrFail($merchant_id);
            $string_version=!empty($string_version_management->id) ? "$string_version_management->app_string_versions" : $string_version;
        }
         $json  = json_encode([
            "version"=> $api_version,
            'string_version' => $string_version,
            "result" =>  "1",
            'message' => isset($data['message']) ? $data['message'] : [],
            'data' => isset($data['data']) ? $data['data'] : [],
            'additional_data'=>isset($data['additional_data'])?$data['additional_data']: []
        ]);
        $compressed = gzencode($json, 6);
        return response($compressed, 200)
            ->header('Content-Type',     'application/json')
            ->header('Content-Encoding', 'gzip')
            ->header('Content-Length',   strlen($compressed));
    }
}
