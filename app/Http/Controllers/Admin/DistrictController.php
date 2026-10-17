<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateDistrictRequest;
use App\Models\District;
use App\Models\SiteSetting;
use App\Services\DeliveryZoneService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DistrictController extends Controller
{
    public function __construct(private DeliveryZoneService $deliveryZoneService) {}

    /**
     * List all districts with search, edit and status toggle actions.
     */
    public function index(Request $request): View
    {
        $districts = $this->deliveryZoneService->listDistricts($request->query('q'));
        $defaultCharge = SiteSetting::getValue('shipping_rate');

        return view('admin.district.index', compact('districts', 'defaultCharge'));
    }

    /**
     * Edit form for a district: name, zone, charge override and status.
     */
    public function edit(District $district): View
    {
        $district->load('zone');
        $zones = $this->deliveryZoneService->list();

        return view('admin.district.edit', compact('district', 'zones'));
    }

    public function update(UpdateDistrictRequest $request, District $district): RedirectResponse
    {
        $this->deliveryZoneService->updateDistrict($district, $request->validated());

        return redirect()->route('admin.districts.index')
            ->with('success', 'District updated successfully.');
    }

    /**
     * Toggle a district between active and inactive.
     * Inactive districts are hidden from the frontend checkout.
     */
    public function toggleStatus(District $district): RedirectResponse
    {
        $district = $this->deliveryZoneService->toggleDistrictStatus($district);

        $statusLabel = $district->status === District::STATUS_ACTIVE ? 'activated' : 'deactivated';

        return redirect()->route('admin.districts.index')
            ->with('success', "District {$statusLabel} successfully.");
    }
}
