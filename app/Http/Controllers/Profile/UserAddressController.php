<?php

namespace App\Http\Controllers\Profile;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditProfileRequest;
use App\Models\UserAddress;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserAddressController extends Controller {
    /**
     * Add new user address entry
     *
     * @param EditProfileRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function AddAddress(EditProfileRequest $request): JsonResponse {
        try {
            $address = UserAddress::create([
                'user_id'  => Auth::id(),
                'country'  => $request->get('country'),
                'city'     => $request->get('city'),
                'state'    => $request->get('state'),
                'province' => $request->get('province'),
            ]);
            return Helper::jsonResponse(true, 'Address added successfully', 201, $address);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Edit existing user address entry
     *
     * @param EditProfileRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function EditAddress(EditProfileRequest $request, int $id): JsonResponse {
        try {
            $address = UserAddress::where('user_id', Auth::id())->findOrFail($id);

            $address->update($request->only([
                'country',
                'city',
                'state',
                'province',
            ]));
            return Helper::jsonResponse(true, 'Address updated successfully', 200, $address);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete existing user address entry
     *
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function DeleteAddress(int $id): JsonResponse {
        try {
            $address = UserAddress::where('user_id', Auth::id())->findOrFail($id);
            $address->delete();
            return Helper::jsonResponse(true, 'Address deleted successfully', 200);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
