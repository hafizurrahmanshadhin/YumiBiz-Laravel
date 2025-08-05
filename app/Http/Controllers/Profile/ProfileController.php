<?php

namespace App\Http\Controllers\Profile;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditProfileRequest;
use App\Models\Like;
use App\Models\Membership;
use App\Models\ProfileView;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller {
    /**
     * Get the authenticated user's profile.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function Profile(): JsonResponse {
        try {
            $userId = auth()->id();

            $user = User::with([
                'profile',
                'userAddresses',
                'userEducations',
                'photoGalleries',
                'lookingFor',
                'businessExperiences',
                'memberships.subscription',
            ])->find($userId);

            if ($user) {
                // Determine the user type
                $userType = 'free';
                if ($user->memberships->isNotEmpty()) {
                    $userType = $user->memberships->first()->subscription->package_type;
                }
                $user->user_type = $userType;

                // Remove memberships from the user object to clean up the response
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

                // Prepare the data
                $data = $user;

                // Return the JSON response
                return response()->json([
                    'status'                        => true,
                    'message'                       => 'User found',
                    'code'                          => 200,
                    'profile_completion_percentage' => $user->getProfileCompletionPercentage(),
                    'data'                          => $data,
                ]);
            } else {
                return Helper::jsonResponse(false, 'User not found', 404);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function alluserExceptLoginUser() {
        try {
            $userId                            = auth()->id();
            $currentUserLookingForDescriptions = User::find($userId)->lookingFor->pluck('description')->toArray();
            $likedProfileIds                   = Like::where('user_id', $userId)->pluck('profile_id')->toArray();

            //! Query users with both is_subscribed and is_boost = 1
            $matchingBothUsers = User::with([
                'profile',
                'userAddresses',
                'userEducations',
                'photoGalleries',
                'lookingFor',
                'businessExperiences',
                'likes',
                'memberships.subscription',
                'boosts' => function ($query) {
                    $query->where('expires_at', '>', now());
                },
            ])
                ->whereNot('id', $userId)
                ->whereNotIn('id', $likedProfileIds)
                ->where('is_subscribed', 1)
                ->where('is_boost', 1)
                ->whereHas('lookingFor', function ($query) use ($currentUserLookingForDescriptions) {
                    $query->whereIn('description', $currentUserLookingForDescriptions);
                })
                ->get();

            //! Query users with either is_subscribed or is_boost = 1
            $matchingEitherUsers = User::with([
                'profile',
                'userAddresses',
                'userEducations',
                'photoGalleries',
                'lookingFor',
                'businessExperiences',
                'likes',
                'memberships.subscription',
                'boosts' => function ($query) {
                    $query->where('expires_at', '>', now());
                },
            ])
                ->whereNot('id', $userId)
                ->whereNotIn('id', $likedProfileIds)
                ->where(function ($query) {
                    $query->where('is_subscribed', 1)
                        ->orWhere('is_boost', 1);
                })
                ->whereDoesntHave('lookingFor', function ($query) use ($currentUserLookingForDescriptions) {
                    $query->whereIn('description', $currentUserLookingForDescriptions);
                })
                ->get();

            //! Query remaining users
            $remainingUsers = User::with([
                'profile',
                'userAddresses',
                'userEducations',
                'photoGalleries',
                'lookingFor',
                'businessExperiences',
                'likes',
                'memberships.subscription',
                'boosts' => function ($query) {
                    $query->where('expires_at', '>', now());
                },
            ])
                ->whereNot('id', $userId)
                ->whereNotIn('id', $likedProfileIds)
                ->whereDoesntHave('lookingFor', function ($query) use ($currentUserLookingForDescriptions) {
                    $query->whereIn('description', $currentUserLookingForDescriptions);
                })
                ->get();

            //! Merge all results
            $users = $matchingBothUsers->merge($matchingEitherUsers)->merge($remainingUsers);

            //! Map user types and clean up memberships
            $users = $users->map(function ($user) {
                $userType = 'free';
                if ($user->memberships->isNotEmpty()) {
                    $userType = $user->memberships->first()->subscription->package_type;
                }
                $user->user_type = $userType;
                unset($user->memberships);

                //! Check and update is_boost attribute
                if ($user->boosts->isNotEmpty()) {
                    $user->is_boost = $user->boosts->first()->expires_at > now() ? 1 : 0;
                } else {
                    $user->is_boost = 0;
                }

                //! Transform `lookingFor` to `looking_for`
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

                //! Remove the original `lookingFor` field
                unset($user->lookingFor);

                return $user;
            });

            if ($users->isNotEmpty()) {
                return Helper::jsonResponse(true, 'Users found', 200, $users->toArray());
            } else {
                return Helper::jsonResponse(false, 'No users found', 404);
            }
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500);
        }
    }

    public function EditProfile(EditProfileRequest $request) {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            if (!$user) {
                return Helper::jsonResponse(false, 'User not authenticated', 401);
            }

            // Update user information
            $user->update([
                'name'  => $request->get('name', $user->name),
                'email' => $request->get('email', $user->email),
            ]);

            // Update or create profile information
            $userDetailsData = $request->only(['user_name', 'age', 'gender', 'bio', 'phone', 'willing_to_invest']);
            if (!empty(array_filter($userDetailsData))) {
                $user->profile()->updateOrCreate(['user_id' => $user->id], $userDetailsData);
            }

            // Sync the `looking_for` data
            if ($request->has('looking_for')) {
                $user->lookingFor()->sync($request->get('looking_for'));
            }

            DB::commit();

            // Load updated data
            $user = $user->load('profile', 'lookingFor');

            // Transform the `lookingFor` data
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

            return Helper::jsonResponse(true, 'Profile successfully updated', 200, $user);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function showProfile($id) {
        try {
            $user = User::with([
                'profile',
                'userAddresses',
                'userEducations',
                'photoGalleries',
                'lookingFor',
                'businessExperiences',
                'memberships.subscription',
            ])->find($id);

            if (!$user) {
                return Helper::jsonResponse(false, 'User not found', 404);
            }

            // Record the profile view
            if (Auth::check() && Auth::id() != $id) {
                ProfileView::create([
                    'viewer_id'  => Auth::id(),
                    'profile_id' => $id,
                ]);
            }

            //! Determine the user type and clean up memberships
            $userType = 'free';
            if ($user->memberships->isNotEmpty()) {
                $userType = $user->memberships->first()->subscription->package_type;
            }
            $user->user_type = $userType;
            unset($user->memberships);

            $lookingFor = $user->lookingFor;
            unset($user->lookingFor);

            // Format the looking_for data and ensure pivot values are strings
            $user->looking_for = $lookingFor->map(function ($lookingFor) {
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

            return Helper::jsonResponse(true, 'User profile retrieved successfully', 200, $user);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500);
        }
    }

    public function whoViewedMyProfile(Request $request) {
        $user = $request->user();

        //! Check if the user has a membership and if it's of type 'prestige'
        $membership = Membership::where('user_id', $user->id)->latest()->first();

        if (!$membership || $membership->subscription->package_type !== 'prestige') {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this feature. Please purchase a subscription plan.',
                'code'    => 403,
            ]);
        }

        //! Retrieve the list of users who have viewed the authenticated user's profile
        $viewedByUsers = ProfileView::with('viewer')
            ->where('profile_id', $user->id)
            ->get()
            ->pluck('viewer');

        //? Transform the viewedByUsers collection to only include necessary user data
        $viewedByUsers = $viewedByUsers->map(function ($viewer) {
            return [
                'id'    => $viewer->id,
                'name'  => $viewer->name,
                'email' => $viewer->email,
            ];
        });

        //! Count the number of users who viewed the authenticated user's profile
        $viewedCount = $viewedByUsers->count();

        return response()->json([
            'success' => true,
            'message' => 'Users who viewed your profile fetched successfully.',
            'code'    => 200,
            'data'    => [
                'viewed_count'    => $viewedCount,
                'viewed_by_users' => $viewedByUsers,
            ],
        ]);
    }

    public function getProfilesByIds(Request $request) {
        $request->validate([
            'users'   => 'required|array',
            'users.*' => 'integer|exists:users,id',
        ]);
        $userIds = $request->input('users');

        $users = User::with('profile')
            ->whereIn('id', $userIds)
            ->get()
            ->map(function ($user) {
                $firstImage = $user->photoGalleries()->first();
                return [
                    'id'      => $user->id,
                    'name'    => $user->name,
                    'email'   => $user->email,
                    'image'   => $firstImage ? $firstImage->image : null,
                    'profile' => [
                        'id'                => $user->profile->id,
                        'user_id'           => $user->profile->user_id,
                        'user_name'         => $user->profile->user_name,
                        'age'               => $user->profile->age,
                        'gender'            => $user->profile->gender,
                        'phone'             => $user->profile->phone,
                        'willing_to_invest' => $user->profile->willing_to_invest,
                        'bio'               => $user->profile->bio,
                    ],
                ];
            });

        return response()->json([
            'users' => $users,
        ], 200);
    }
}
