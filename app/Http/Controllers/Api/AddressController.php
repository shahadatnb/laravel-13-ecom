<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddressFormRequest;
use App\Http\Resources\UserAddressResource;
use App\Models\UserAddress;
use App\Services\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(private readonly AddressService $service) {}

    /**
     * List the authenticated customer's addresses.
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = $this->service->listAddresses($request->user());

        return response()->json([
            'success' => true,
            'data' => UserAddressResource::collection($addresses),
        ]);
    }

    /**
     * Create a new address for the authenticated customer.
     */
    public function store(AddressFormRequest $request): JsonResponse
    {
        $address = $this->service->createAddress($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully.',
            'data' => new UserAddressResource($address),
        ], 201);
    }

    /**
     * Update one of the customer's addresses.
     */
    public function update(AddressFormRequest $request, UserAddress $userAddress): JsonResponse
    {
        $this->authorize('update', $userAddress);

        $address = $this->service->updateAddress($request->user(), $userAddress, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully.',
            'data' => new UserAddressResource($address),
        ]);
    }

    /**
     * Soft delete one of the customer's addresses.
     */
    public function destroy(UserAddress $userAddress): JsonResponse
    {
        $this->authorize('delete', $userAddress);

        $this->service->deleteAddress($userAddress);

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully.',
        ]);
    }
}
