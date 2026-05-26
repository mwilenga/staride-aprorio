<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UtilityBannersOffer;
use App\Traits\MerchantTrait;
use App\Traits\ImageTrait;

class UtilityBannersOfferController extends Controller
{
    use MerchantTrait, ImageTrait;

    /**
     * Display listing
     */
    public function index()
    {
        $transactions = UtilityBannersOffer::where('merchant_id', get_merchant_id())
            ->latest()
            ->get();

        return view('merchant.utilitybannersoffers.index', compact('transactions'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('merchant.utilitybannersoffers.add');
    }

    /**
     * Store data
     */
    public function store(Request $request)
    {
        $merchant_id = get_merchant_id();
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'hyperlink' => 'nullable|url|max:500',
            'type' => 'required|in:BANNER,OFFER',
        ]);

        // Update Image
        if ($request->hasFile('image')) {

            $url = $this->uploadImage(
                'image',
                'banners_image',
                $merchant_id
            );
        }
        if (isset($url)) {
            $validated['image'] = $url;
        }

        $validated['merchant_id'] = get_merchant_id();

        UtilityBannersOffer::create($validated);

        return redirect()
            ->route('merchant.banners_offers.index')
            ->with('success', 'Banner/Offer added successfully.');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $data = $this->findMerchantRecord($id);

        return view('merchant.utilitybannersoffers.edit', compact('data'));
    }

    /**
     * Update data
     */
    public function update(Request $request, $id)
    {
        $data = $this->findMerchantRecord($id);
        $merchant_id = get_merchant_id();
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'hyperlink' => 'nullable|url|max:500',
            'type' => 'required|in:BANNER,OFFER',
        ]);

        // Update Image
        if ($request->hasFile('image')) {

            $url = $this->uploadImage(
                'image',
                'banners_image',
                $merchant_id
            );
        }
        if (isset($url)) {
            $validated['image'] = $url;
        }
        $data->update($validated);

        return redirect()
            ->route('merchant.banners_offers.index')
            ->with('success', 'Banner/Offer updated successfully.');
    }

    /**
     * Delete record
     */
    public function destroy($id)
    {
        $data = $this->findMerchantRecord($id);

        $data->delete();

        return back()->with('success', 'Deleted successfully.');
    }

    /**
     * Reusable method to get merchant record safely
     */
    private function findMerchantRecord($id)
    {
        return UtilityBannersOffer::where('merchant_id', get_merchant_id())
            ->findOrFail($id);
    }
}