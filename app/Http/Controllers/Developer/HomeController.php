<?php
/**
 * Created by PhpStorm.
 * User: apporio
 * Date: 16/8/23
 * Time: 10:44 PM
 */

namespace App\Http\Controllers\Developer;


use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function index(){
        return view("developer.login");
    }

    public function entry(Request $request){
        $request->validate([
            'pin' => 'required',
        ]);
        try{
            $merchant = Merchant::where("access_pin", $request->pin)->first();
            if(!empty($merchant)){
                \Session::put('developer', base64_encode($request->pin));
                return redirect()->route("developer.home")->withSuccess("Logged in successfully");
            }else{
                return redirect()->back()->withErrors("Invalid Credentials");
            }
        }catch (\Exception $exception){
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }
}
