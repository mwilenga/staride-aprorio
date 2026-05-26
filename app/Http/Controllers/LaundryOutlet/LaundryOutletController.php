<?php

namespace App\Http\Controllers\LaundryOutlet;

use App\Http\Controllers\Controller;
use App\Models\BookingTransaction;
use App\Models\LaundryOutlet\LaundryOutletOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\View;

class LaundryOutletController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth:laundry_outlet');
    }

    public function dashboard()
    {
        $new_orders = 0;
        $assigned_orders = 0;
        $total_booking = 0;
        $ongoing_booking = 0;
        $expired_booking = 0;
        $complete_booking = 0;
        $cancelled = 0;
        $started = 0;
        return view('laundry-outlet.dashboard', compact('new_orders', 'total_booking', 'complete_booking', 'ongoing_booking', 'expired_booking', 'cancelled', "assigned_orders", 'started'));
    }


    public function SetLangauge(Request $request, $locle)
    {
        $request->session()->put('locale', $locle);
        return redirect()->back();
    }


    public function earningSummary(Request $request){
        $data = [];
        $order = new LaundryOutletOrder;
        $outlet = get_laundry_outlet(false);
        $id = $outlet->id;
        $merchant_id = $outlet->merchant_id;
        $data['business_summary'] = [];
        $segment_id = $outlet->segment_id;
        $request->request->add(['status'=>'COMPLETED','merchant_id'=>$merchant_id,'segment_id'=>$segment_id,'id'=>$id]);  // DELIVERED
        $all_orders = $order->getOrders($request,true);
        $query = BookingTransaction::select(DB::raw('SUM(customer_paid_amount) as order_amount'),DB::raw('SUM(company_earning) as merchant_earning'),DB::raw('SUM(driver_earning) as driver_earning'),DB::raw('SUM(laundry_outlet_earning) as store_earning'))
            ->with(['LaundryOutletOrder'=>function($q) use($request,$id){
                $q->where([['order_status','=',11],['laundry_outlet_id','=',$id]]);
                if (!empty($request->booking_id) && $request->booking_id) {
                    $q->where('merchant_booking_id', $request->booking_id);
                }
                if (!empty($request->segment_id)) {
                    $q->where('segment_id', $request->segment_id);
                }
                if ($request->start) {
                    $start_date = date('Y-m-d',strtotime($request->start));
                    $end_date = date('Y-m-d ',strtotime($request->end));
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                }
            }])
            ->whereHas('LaundryOutletOrder',function($q) use($request,$id){
                $q->where([['order_status','=',10],['laundry_outlet_id','=',$id]]);
                if (!empty($request->order_id) && $request->order_id) {
                    $q->where('merchant_order_id', $request->order_id);
                }
                if (!empty($request->segment_id)) {
                    $q->where('segment_id', $request->segment_id);
                }
                if ($request->start) {
                    $start_date = date('Y-m-d',strtotime($request->start));
                    $end_date = date('Y-m-d ',strtotime($request->end));
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                }
            });
        $business_income = $query->first();
        $data['business_summary'] = [
            'orders'=> $all_orders->total(),
            'income'=> $business_income,
            'commission'=> $outlet->commission,
        ];
        $currency = $outlet->Country->isoCode;
        $data['currency'] = $currency;
        $data['arr_orders'] = $all_orders;
        $req_param['merchant_id'] = $outlet->merchant_id;
        $data['title'] =  !empty($business_seg) ? $outlet->full_name : '---';
        $data['merchant_name'] =  !empty($outlet) ? $outlet->Merchant->BusinessName : '---';
        $request->request->add(['search_route'=>route('business-segment.earning')]);
        $order_con = new LaundryOrderController();
        $request->request->add(['calling_view'=>'earning']);
        $data['search_view'] = $order_con->orderSearchView($request);
        $data['arr_search'] = $request->all();
        return view('laundry-outlet.wallet.earning')->with($data);
    }


}
