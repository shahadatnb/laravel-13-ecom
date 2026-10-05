<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Resources\CustomerResource;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function __construct(private readonly FileUploadService $fileUpload) {}

    /**
     * Update the authenticated customer's profile (name, email, phone, avatar).
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $customer = $request->user();
        $data = $request->validated();
        $oldAvatar = null;

        if ($request->hasFile('avatar')) {
            $oldAvatar = $customer->avatar;
            $data['avatar'] = $this->fileUpload->upload($request->file('avatar'), 'avatars');
        }

        if (isset($data['email']) && $data['email'] !== $customer->email) {
            $data['email_verified_at'] = null;
        }

        $customer->update($data);

        if ($oldAvatar) {
            $this->fileUpload->delete($oldAvatar);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => new CustomerResource($customer->fresh()),
        ]);
    }
}
