<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDeliveryZoneRequest;
use App\Http\Requests\Admin\UpdateDeliveryZoneRequest;
use App\Models\DeliveryZone;
use App\Services\DeliveryZoneService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeliveryZoneController extends Controller
{
    public function __construct(private DeliveryZoneService $deliveryZoneService) {}

    public function index(): View
    {
        $zones = $this->deliveryZoneService->list();

        return view('admin.delivery-zone.index', compact('zones'));
    }

    public function create(): View
    {
        return view('admin.delivery-zone.createOrEdit');
    }

    public function store(StoreDeliveryZoneRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->deliveryZoneService->create($data);

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'Delivery zone created successfully.');
    }

    public function edit(DeliveryZone $deliveryZone): View
    {
        $deliveryZone->load('districts');

        return view('admin.delivery-zone.createOrEdit', compact('deliveryZone'));
    }

    public function update(UpdateDeliveryZoneRequest $request, DeliveryZone $deliveryZone): RedirectResponse
    {
        $data = $request->validated();
        $this->deliveryZoneService->update($deliveryZone, $data);

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'Delivery zone updated successfully.');
    }

    public function destroy(DeliveryZone $deliveryZone): RedirectResponse
    {
        $this->deliveryZoneService->delete($deliveryZone);

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'Delivery zone deleted successfully.');
    }
}
