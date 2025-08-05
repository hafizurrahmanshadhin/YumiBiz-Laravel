<?php

namespace App\Http\Controllers\Profile;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditProfileRequest;
use App\Models\UserEducation;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserEducationController extends Controller {
    /**
     * Add new education entry
     *
     * @param EditProfileRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function AddEducation(EditProfileRequest $request): JsonResponse {
        try {
            $education = UserEducation::create([
                'user_id'             => Auth::id(),
                'degree'              => $request->get('degree'),
                'institute'           => $request->get('institute'),
                'academic_year_start' => $request->get('academic_year_start'),
                'academic_year_end'   => $request->get('academic_year_end'),
            ]);
            return Helper::jsonResponse(true, 'Education added successfully', 201, $education);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Edit existing education entry
     *
     * @param EditProfileRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function EditEducation(EditProfileRequest $request, int $id): JsonResponse {
        try {
            $education = UserEducation::where('user_id', Auth::id())->findOrFail($id);

            $education->update($request->only([
                'degree',
                'institute',
                'academic_year_start',
                'academic_year_end',
            ]));
            return Helper::jsonResponse(true, 'Education updated successfully', 200, $education);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete existing education entry
     *
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function DeleteEducation(int $id): JsonResponse {
        try {
            $education = UserEducation::where('user_id', Auth::id())->findOrFail($id);
            $education->delete();
            return Helper::jsonResponse(true, 'Education deleted successfully', 200);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
