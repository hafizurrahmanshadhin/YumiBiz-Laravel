<?php

namespace App\Http\Controllers\Profile;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller {
    /**
     * Filter for users based on location.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function filter(Request $request): JsonResponse {
        $query = User::query();

        if ($request->has('country')) {
            $query->whereHas('userAddresses', function ($q) use ($request) {
                $q->where('country', $request->input('country'));
            });
        }

        if ($request->has('city')) {
            $query->whereHas('userAddresses', function ($q) use ($request) {
                $q->where('city', $request->input('city'));
            });
        }

        if ($request->has('state')) {
            $query->whereHas('userAddresses', function ($q) use ($request) {
                $q->where('state', $request->input('state'));
            });
        }

        if ($request->has('province')) {
            $query->whereHas('userAddresses', function ($q) use ($request) {
                $q->where('province', $request->input('province'));
            });
        }

        $users = $query->with([
            'profile',
            'userAddresses',
            'userEducations',
            'photoGalleries',
            'lookingFor',
            'businessExperiences',
            'memberships.subscription',
        ])->get();

        // Map over the users to set the user_type, clean up memberships, and format looking_for data
        $users = $users->map(function ($user) {
            // Determine the user type
            $userType = 'free';
            if ($user->memberships->isNotEmpty()) {
                $userType = $user->memberships->first()->subscription->package_type;
            }
            $user->user_type = $userType;
            unset($user->memberships);

            // Format the looking_for data
            $user->looking_for = $user->lookingFor->map(function ($lookingFor) {
                return [
                    'id'          => $lookingFor->id,
                    'type'        => 'lookingFor',
                    'description' => $lookingFor->description,
                    'pivot'       => [
                        'user_id' => (string) $lookingFor->pivot->user_id,
                        'meta_id' => (string) $lookingFor->pivot->meta_id,
                    ],
                ];
            });
            unset($user->lookingFor);

            return $user;
        });

        if ($users->isEmpty()) {
            return Helper::jsonResponse(false, 'Users not found', 404);
        }

        return Helper::jsonResponse(true, 'Users found', 200, $users);
    }

    /**
     * Search for users based on name.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse {
        $query = User::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $users = $query->with([
            'profile',
            'userAddresses',
            'userEducations',
            'photoGalleries',
            'lookingFor',
            'businessExperiences',
            'memberships.subscription',
        ])->get();

        // Map over the users to set the user_type and clean up memberships
        $users = $users->map(function ($user) {
            // Determine the user type
            $userType = 'free';
            if ($user->memberships->isNotEmpty()) {
                $userType = $user->memberships->first()->subscription->package_type;
            }
            $user->user_type = $userType;
            unset($user->memberships);

            // Transform the lookingFor data to ensure pivot fields are strings and remove the `lookingFor` field
            $user->looking_for = $user->lookingFor->map(function ($item) {
                return [
                    'id'          => $item->id,
                    'type'        => $item->type,
                    'description' => $item->description,
                    'pivot'       => [
                        'user_id' => (string) $item->pivot->user_id,
                        'meta_id' => (string) $item->pivot->meta_id,
                    ],
                ];
            });

            // Remove the `lookingFor` field
            unset($user->lookingFor);

            return $user;
        });

        return Helper::jsonResponse(true, 'Users found', 200, $users->toArray());
    }
}
