<?php

namespace App\Http\Controllers\Api;

use App\DTO\CreateCustomerProfileDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerFormRequest;
use App\Http\Resources\CustomerProfileResource;
use App\Models\CustomerProfile;
use App\Services\CustomerServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerServiceInterface $customerService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $customers = $this->customerService->listCustomers(
            $request->get('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'message' => 'Customers retrieved successfully',
            'data' => CustomerProfileResource::collection($customers),
        ]);
    }

    public function show(CustomerProfile $customer): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Customer retrieved successfully',
            'data' => new CustomerProfileResource($customer),
        ]);
    }

    public function store(CustomerFormRequest $request): JsonResponse
    {
        $dto = CreateCustomerProfileDTO::fromArray([
            'customer_id' => $request->user('customer')->id,
            'gender' => $request->input('gender'),
            'date_of_birth' => $request->input('date_of_birth'),
            'marketing_opt_in' => $request->boolean('marketing_opt_in'),
            'status' => $request->input('status', 'active'),
            'notes' => $request->input('notes'),
        ]);

        $customer = $this->customerService->createCustomerProfile($dto);

        return response()->json([
            'success' => true,
            'message' => 'Customer profile created successfully',
            'data' => new CustomerProfileResource($customer),
        ], 201);
    }

    public function update(CustomerFormRequest $request, CustomerProfile $customer): JsonResponse
    {
        $data = $request->validated();
        $customer = $this->customerService->updateCustomerProfile($customer, $data);

        return response()->json([
            'success' => true,
            'message' => 'Customer profile updated successfully',
            'data' => new CustomerProfileResource($customer),
        ]);
    }

    public function destroy(CustomerProfile $customer): JsonResponse
    {
        $this->customerService->deleteCustomerProfile($customer);

        return response()->json([
            'success' => true,
            'message' => 'Customer profile deleted successfully',
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $customers = $this->customerService->searchCustomers($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Customers searched successfully',
            'data' => CustomerProfileResource::collection($customers),
        ]);
    }

    public function statistics(): JsonResponse
    {
        $statistics = $this->customerService->getCustomerStatistics();

        return response()->json([
            'success' => true,
            'message' => 'Statistics retrieved successfully',
            'data' => $statistics,
        ]);
    }
}
