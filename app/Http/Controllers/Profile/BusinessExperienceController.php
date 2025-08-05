<?php

namespace App\Http\Controllers\Profile;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditProfileRequest;
use App\Models\BusinessExperience;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BusinessExperienceController extends Controller {
    /**
     * Add new business experience entry
     *
     * @param EditProfileRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function AddExperience(EditProfileRequest $request): JsonResponse {
        try {
            $experience = BusinessExperience::create([
                'user_id'         => Auth::id(),
                'meta_id'         => $request->get('meta_id'),
                'designation'     => $request->get('designation'),
                'company_name'    => $request->get('company_name'),
                'experience_from' => $request->get('experience_from'),
                'experience_to'   => $request->get('experience_to'),
            ]);
            return Helper::jsonResponse(true, 'Experience added successfully', 201, $experience);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Edit existing business experience entry
     *
     * @param EditProfileRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function EditExperience(EditProfileRequest $request, int $id): JsonResponse {
        try {
            $experience = BusinessExperience::where('user_id', Auth::id())->findOrFail($id);

            $experience->update($request->only([
                'meta_id',
                'designation',
                'company_name',
                'experience_from',
                'experience_to',
            ]));
            return Helper::jsonResponse(true, 'Experience updated successfully', 200, $experience);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete existing business experience entry
     *
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function DeleteExperience(int $id): JsonResponse {
        try {
            $experience = BusinessExperience::where('user_id', Auth::id())->findOrFail($id);
            $experience->delete();
            return Helper::jsonResponse(true, 'Experience deleted successfully', 200);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
