<?php

/**@ayush
 * Laundry Module*/

namespace App\Http\Controllers\LaundryOutlet;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InfoSetting;
use App\Models\LaundryOutlet\LanguageLaundryServices;
use App\Models\LaundryOutlet\LaundryService;
use App\Traits\ImageTrait;
use App\Traits\LaundryServiceTrait;
use App\Traits\MerchantTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;

//use Illuminate\Validation\Validator;

class LaundryServiceController extends Controller
{
    use MerchantTrait, LaundryServiceTrait, ImageTrait;

    //
    public function index(Request $request)
    {
        $category = $request->category;
        $outlet = get_laundry_outlet(false);
        $string_file = $this->getStringFile(NULL, $outlet->Merchant);
        $merchant_id = $outlet->merchant_id;
        $query = LaundryService::with(['Category' => function ($q) use ($merchant_id, $category) {
            $q->where([['delete', '=', NULL]]);
            if (!empty($category)) {
                $q->with(['LangCategorySingle' => function ($q) use ($category, $merchant_id) {
                    $q->where('name', $category)->where('merchant_id', $merchant_id);
                }]);
            }
        }])
            ->whereHas('Category', function ($q) use ($merchant_id, $category) {
                $q->where([['delete', '=', NULL]]);
                if (!empty($category)) {
                    $q->whereHas('LangCategorySingle', function ($q) use ($category, $merchant_id) {
                        $q->where('name', $category)->where('merchant_id', $merchant_id);
                    });
                }
            })
            ->where([['delete', '=', NULL], ['laundry_outlet_id', '=', $outlet->id]]);

        if (!empty($request->name)) {
            $query->with(['LanguageLaundryServices' => function ($q) use ($request, $merchant_id) {
                $q->where('name', 'like', "%" . $request->name . "%")->where('merchant_id', $merchant_id);
            }])
                ->whereHas('LanguageLaundryServices', function ($q) use ($request, $merchant_id) {
                    $q->where('name', 'like', "%" . $request->name . "%")->where('merchant_id', $merchant_id);
                });
        }
        if (!empty($request->sid)) {
            $query->where('sid', $request->sid);
        }
        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        $info_setting = InfoSetting::where('slug', 'PRODUCT')->first();
        $service['data'] = $query->latest()->paginate(10);
        $service['service_status'] = get_product_status("web", $string_file);
        $service['arr_search'] = $request->all();
        $service['search_view'] = $this->searchView($request);
        $service['info_setting'] = $info_setting;
        $service['arr_category_search'] = [
            'category' => $request->category,
            'merchant_id' => $merchant_id,
        ];
        return view('laundry-outlet.service.index')->with($service);
    }

    public function searchView($request)
    {
        $data['arr_search'] = $request->all();
        $data['arr_search']['search_route'] = route('laundry-outlet.services.index');
        $search = View::make('laundry-outlet.service.search')->with($data)->render();
        return $search;
    }

    public function add(Request $request, $id = NULL)
    {
        $service = null;
        $outlet = get_laundry_outlet(false);
        $string_file = $this->getStringFile(NULL, $outlet->Merchant);
        $merchant_id = $outlet->merchant_id;
        $arr_category = $this->getCategory($merchant_id, $outlet->segment_id);
        $sub_category = [];
        $is_demo = false;
        if (!empty($id)) {
            $service = LaundryService::Find($id);
            if (empty($service->id)) {
                return redirect()->back()->withErrors(trans("$string_file.data_not_found"));
            }

            $category = Category::select('id', 'category_parent_id')->find($service->category_id);
            if ($category->category_parent_id != 0) {
                $parent_category = Category::select('id', 'category_parent_id')->find($category->category_parent_id);
                $service->category_id = $parent_category->id;
                $service->sub_category_id = $category->id;
                $sub_category = $this->getCategory($merchant_id, $outlet->segment_id, 'child', $parent_category->id);
            } else {
                $sub_category = $this->getCategory($merchant_id, $outlet->segment_id, 'child', $category->id);
                $service->category_id = $category->id;
                $service->sub_category_id = null;
            }
        }
        if (empty($id)) {
            $maxSID = get_max_laundry_outlet_sid($merchant_id, $outlet->id);
            if (!empty($maxSID)) {
                $exists = 0;
                $i = 1;
                do {
                    $sid = sprintf("%'03d", (int)$maxSID->sid + $i);
                    $exists = LaundryService::where(['merchant_id' => $merchant_id, 'laundry_outlet_id' => $outlet->id, 'sid' => $sid])->exists();
                    $i++;

                } while ($exists);
            } else {
                $sid = sprintf("%'03d", 1);

            }
        }
        $string_file = $this->getStringFile($merchant_id);

        $data['data'] = [
            'save_url' => route('laundry-outlet.service.save', $id),
            'service' => $service,
            'arr_category' => $arr_category,
            'arr_status' => get_active_status("web", $string_file),
            'service_status' => get_product_status("web", $string_file),
            'outlet' => $outlet,
            'sub_category' => $sub_category,
            'arr_size' => Config('custom.laundry_service_image_size'),
            'sid' => isset($sid) ? $sid : 1
        ];
        $data['is_demo'] = $is_demo;
        return view('laundry-outlet.service.form')->with($data);
    }

    /*Save or Update*/
    public function save(Request $request, $id = NULL)
    {
        $arr_size = Config('custom.laundry_service_image_size');
        $width = $arr_size['service']['width'];
        $height = $arr_size['service']['height'];

        $p_width = $arr_size['service_image']['width'];
        $p_height = $arr_size['service_image']['height'];

        $outlet = get_laundry_outlet(false);
        $string_file = $this->getStringFile(NULL, $outlet->Merchant);
        $outlet_id = $outlet->id;
        $merchant_id = $outlet->merchant_id;
        $locale = App::getLocale();
        $validator = Validator::make($request->all(), [
            'sid' => [
                'required',
                Rule::unique('laundry_services', 'sid')->where(function ($query) use ($merchant_id, $outlet_id) {
                    return $query->where([['laundry_outlet_id', '=', $outlet_id], ['merchant_id', '=', $merchant_id], ['delete', '=', NULL]]);
                })->ignore($id)
            ],

            'status' => 'required',
            'price' => 'required',
            'service_description' => 'required',
            'service_cover_image' => 'mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=' . $width . ',min_height=' . $height,
            'service_image.*' => 'mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=' . $p_width . ',min_height=' . $p_height,
            'category_id' => 'required',
        ], ['service_image.dimensions' => 'Please upload correct dimensions image']);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput()->withErrors($errors);
        }
        // product name
        $service_name = DB::table('language_laundry_services')->where(function ($query) use ($merchant_id, $locale, $id, $request) {
            return $query->where([['language_laundry_services.merchant_id', '=', $merchant_id], ['language_laundry_services.locale', '=', $locale], ['language_laundry_services.name', '=', $request->service_name]])
                ->where('language_laundry_services.laundry_service_id', '!=', $id);
        })->join("laundry_services", "language_laundry_services.laundry_service_id", "=", "laundry_services.id")
            ->where('laundry_services.id', '!=', $id)
            ->where('laundry_services.merchant_id', '=', $merchant_id)
            ->where('laundry_services.laundry_outlet_id', '=', $outlet_id)
            ->where('laundry_services.delete', NULL)->first();

        if (!empty($service_name->id)) {
            $string_file = $this->getStringFile($merchant_id);
            return redirect()->back()->withErrors(trans("$string_file.service_name_already_exist"));
        }
        DB::beginTransaction();
        try {
            $outlet = get_laundry_outlet(false);
            if (!empty($id)) {
                $service = LaundryService::Find($id);
            } else {
                $service = new LaundryService();
                $service->laundry_outlet_id = $outlet->id;
                $service->merchant_id = $outlet->merchant_id;
            }

            $service->sid = $request->sid;
            $service->sequence = $request->sequence;
            $service->status = $request->status;
            $service->category_id = !empty($request->sub_category_id) ? $request->sub_category_id : $request->category_id;
            $service->display_type = $request->display_type;
            $service->price = $request->price;

            if (!empty($request->hasFile('service_cover_image'))) {
                $additional_req = ['compress' => true, 'custom_key' => 'product'];
                $service->service_cover_image = $this->uploadImage('service_cover_image', 'laundry_service_cover_image', $merchant_id, 'single', $additional_req);
            }
            if (!empty($request->hasFile('service_image'))) {
                $service->service_image = $this->uploadImage('service_image', 'laundry_service_image', $merchant_id, 'single');
            }
            $service->save();
            $this->saveLanguageData($request, $service->merchant_id, $service);

        } catch (Exception $e) {
            $message = $e->getMessage();
            DB::rollback();
            return redirect()->route("laundry-outlet.services.index")->with('error', $message);
        }
        DB::commit();
        return redirect()->route("laundry-outlet.services.index")->with('success', trans("$string_file.added_successfully"));
    }

    public function saveLanguageData($request, $merchant_id, $service)
    {
        LanguageLaundryServices::updateOrCreate(
            [
                'merchant_id' => $merchant_id,
                'locale' => App::getLocale(),
                'laundry_service_id' => $service->id
            ],
            [
                'laundry_outlet_id' => $service->laundry_outlet_id,
                'name' => $request->service_name,
                'description' => $request->service_description,
            ]
        );
    }


    public function getSubCategory(Request $request)
    {
        $result = [];
        $outlet = get_laundry_outlet(false);
        $string_file = $this->getStringFile(NULL, $outlet->Merchant);
        if (isset($request->id) && $request->id != '') {
            $merchant_id = $outlet->merchant_id;
            $sub_category = $this->getCategory($merchant_id, $outlet->segment_id, 'child', $request->id);
            if (!empty($sub_category)) {
                $result = $sub_category;
            } else {
                $result = array('' => trans("$string_file.select"));
            }
        } else {
            $result = array('' => trans("$string_file.select"));
        }
        return json_encode($result, true);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $delete = LaundryService::Find($id);
        $delete->delete = 1;
        $delete->save();
    }

}
