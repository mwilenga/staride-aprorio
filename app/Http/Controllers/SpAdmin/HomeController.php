<?php
/**
 * Created by PhpStorm.
 * User: apporio
 * Date: 16/8/23
 * Time: 10:44 PM
 */

namespace App\Http\Controllers\SpAdmin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function index(){
        return view("sp_admin.login");
    }

    public function entry(Request $request){
        $request->validate([
            'password' => 'required',
        ]);
        try{
            $admin = Admin::first();
            if(Hash::check($request->password, $admin->password)){
                \Session::put('sp-token', base64_encode($request->password));
                return redirect()->route("sp-admin.home")->withSuccess("Logged in successfully");
            }else{
                return redirect()->back()->withErrors("Invalid Credentials");
            }
        }catch (\Exception $exception){
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }
}
