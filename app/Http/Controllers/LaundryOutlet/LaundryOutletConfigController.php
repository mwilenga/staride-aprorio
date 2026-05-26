<?php

namespace App\Http\Controllers\LaundryOutlet;

use App\Http\Controllers\Controller;
use App\Models\LaundryOutlet\LaundryOutletConfiguration;
use App\Traits\MerchantTrait;
use Illuminate\Http\Request;
use DB;

class LaundryOutletConfigController extends Controller
{
    //
    use MerchantTrait;
    public function index()
    {
        $outlet = get_laundry_outlet(false);
        $merchant_id = $outlet->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $data['config'] = LaundryOutletConfiguration::where('laundry_outlet_id',  $outlet->id)->first();
        $data['is_open'] = get_status(true,$string_file);
        return view('laundry-outlet.configurations')->with($data);
    }

    public function save(Request $request){
        $outlet = get_laundry_outlet(false);
        $string_file = $this->getStringFile(NULL,$outlet->Merchant);
        DB::beginTransaction();

        try {
            $config=LaundryOutletConfiguration::where('laundry_outlet_id',  $outlet->id)->first();
            if(empty($config)){
                $config = new LaundryOutletConfiguration();
                $config->laundry_outlet_id=$outlet->id;
            }
            $config->order_expire_time = $request->order_expire_time;
            $config->is_open=$request->is_open;
            $config->estimate_process_days = $request->estimate_process_days;
            $config->save();
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            DB::rollback();
            return redirect()->route('laundry-outlet.configurations')->withErrors($message);
        }
        DB::commit();
        return redirect()->route('laundry-outlet.configurations')->with('success', trans("$string_file.added_successfully"));
    }
}
