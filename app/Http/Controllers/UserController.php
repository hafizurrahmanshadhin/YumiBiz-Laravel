<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\BusinessExperience;
use App\Models\PhotoGallery;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserEducation;
use App\Models\UserResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller {
    public function deleteAccount(Request $request): JsonResponse {
        try {
            $userId = Auth::id();

            DB::transaction(function () use ($userId) {
                UserAddress::where('user_id', $userId)->forceDelete();
                UserEducation::where('user_id', $userId)->forceDelete();
                PhotoGallery::where('user_id', $userId)->forceDelete();
                UserResponse::where('user_id', $userId)->forceDelete();
                BusinessExperience::where('user_id', $userId)->forceDelete();
                Profile::where('user_id', $userId)->forceDelete();

                User::where('id', $userId)->forceDelete();
            });

            return Helper::jsonResponse(true, 'Account deleted successfully', 200);

        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Something went wrong', 500);
        }
    }
}
