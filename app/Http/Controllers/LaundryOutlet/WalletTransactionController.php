<?php

namespace App\Http\Controllers\LaundryOutlet;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\LaundryOutlet\LaundryOutletCashout;
use App\Models\LaundryOutlet\LaundryOutletWalletTransaction;
use App\Traits\LaundryServiceTrait;
use App\Traits\MerchantTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class WalletTransactionController extends Controller
{
    //
    use MerchantTrait, LaundryServiceTrait;
    public function walletSearchView($request)
    {
        $data['arr_search'] = $request->all();
        $wallet_search = View::make('laundry-outlet.wallet.wallet-search')->with($data)->render();
        return $wallet_search;
    }

    public function index(Request $request){
        $data = [];
        $merchant_id = get_merchant_id();

        $request->request->add(['search_route'=>route('laundry-outlet.wallet')]);
        $order_con = new WalletTransactionController;

        $request->request->add(['calling_view'=>"wallet-transaction-list"]);
        $search_view = $order_con->walletSearchView($request);
        $data['arr_search'] = $request->all();
        $outlet = get_laundry_outlet(false);
        $wallet_transactions = LaundryOutletWalletTransaction::where('laundry_outlet_id',$outlet->id)
            ->where(function ($query) use ($request) {
                if($request->start) {
                    $start_date = date('Y-m-d',strtotime($request->start));
                    $end_date = date('Y-m-d ',strtotime($request->end));
                    $query->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                }
            })
            ->latest()->paginate(25);
        return view('laundry-outlet.wallet.index',compact('outlet','wallet_transactions','search_view'));
    }

    public function cashouts(Request $request){
        $data = [];
        $merchant_id = get_merchant_id();

        $request->request->add(['search_route'=>route('business-segment.cashouts')]);
        $order_con = new WalletTransactionController;

        $request->request->add(['calling_view'=>"cashout-list"]);
        $search_view = $order_con->walletSearchView($request);
        $data['arr_search'] = $request->all();
        $outlet = get_laundry_outlet(false);
        $cashout_requests = LaundryOutletCashout::where('laundry_outlet_id',$outlet->id)
            ->where(function ($query) use ($request) {
                if($request->start) {
                    $start_date = date('Y-m-d',strtotime($request->start));
                    $end_date = date('Y-m-d ',strtotime($request->end));
                    $query->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                }
            })
            ->latest()->paginate(25);
        return view('laundry-outlet.wallet.cashout',compact('outlet','cashout_requests','search_view'));
    }

    public function cashoutRequest(Request $request){
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try{
            $outlet = get_laundry_outlet(false);
            $merchant_id = $outlet->merchant_id;
            $string_file = $this->getStringFile($merchant_id);
            if($outlet->wallet_amount < $request->amount){
                return redirect()->back()->withErrors(trans('admin.wallet_balance_low'));
            }
            $paramArray = array(
                'laundry_outlet_id' => $outlet->id,
                'booking_id' => null,
                'amount' => $request->amount,
                'narration' => 4,
            );
            WalletTransaction::LaundryOutletWalletDebit($paramArray);
            $cashout_req = new LaundryOutletCashout();
            $cashout_req->laundry_outlet_id = $outlet->id;
            $cashout_req->merchant_id = $outlet->merchant_id;
            $cashout_req->amount = $request->amount;
            $cashout_req->save();

            DB::commit();
            $this->sendPushNotificationToLaundryOutlet(null,null, $cashout_req);
        }catch (\Exception $e){
            throw $e;
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
        }
        return redirect()->back()->with('success',trans("$string_file.cashout_request_registered_successfully"));
    }
}
