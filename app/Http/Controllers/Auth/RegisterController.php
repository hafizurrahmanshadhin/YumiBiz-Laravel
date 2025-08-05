<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller {
    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse {
        DB::beginTransaction();

        try {
            $user = $this->createUser($request);

            $this->createUserProfile($user, $request);
            $this->createUserAddress($user, $request);

            if ($request->hasFile('images')) {
                $this->uploadUserImages($user, $request);
            }

            $user->lookingFor()->attach($request->input('looking_for'));

            $this->createBusinessExperience($user, $request);

            DB::commit();

            $credentials = ['email' => $user->email, 'password' => $request->input('password')];
            $token       = auth()->attempt($credentials);

            //! Transform lookingFor relationship data
            $lookingFor = $user->lookingFor->map(function ($item) {
                return [
                    'id'          => $item->id,
                    'type'        => 'lookingFor',
                    'description' => $item->description,
                    'pivot'       => [
                        'user_id' => (string) $item->pivot->user_id,
                        'meta_id' => (string) $item->pivot->meta_id,
                    ],
                ];
            });

            //! Calculate profile completion percentage
            $profileCompletionPercentage = $user->getProfileCompletionPercentage();

            $data = $user->load(
                'profile',
                'userAddresses',
                'photoGalleries',
                'lookingFor',
                'businessExperiences'
            );

            return response()->json([
                'status'                        => true,
                'message'                       => 'User successfully registered',
                'code'                          => 201,
                'profile_completion_percentage' => $profileCompletionPercentage,
                'token_type'                    => 'bearer',
                'token'                         => $token,
                'expires_in'                    => auth()->factory()->getTTL() * 60,
                'data'                          => [
                    'user'        => $data,
                    'looking_for' => $lookingFor,
                ],
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return Helper::jsonResponse(false, 'User registration failed', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Create a new user.
     *
     * @param RegisterRequest $request
     * @return User
     */
    private function createUser(RegisterRequest $request): User {
        return User::create([
            'name'           => $request->input('name'),
            'email'          => $request->input('email'),
            'password'       => Hash::make($request->input('password')),
            'agree_to_terms' => $request->input('agree_to_terms'),
        ]);
    }

    /**
     * Create user profile.
     *
     * @param User $user
     * @param RegisterRequest $request
     */
    private function createUserProfile(User $user, RegisterRequest $request): void {
        $user->profile()->create([
            'user_name' => $request->input('user_name'),
            'age'       => $request->input('age'),
            'gender'    => $request->input('gender'),
        ]);
    }

    /**
     * Create user address.
     *
     * @param User $user
     * @param RegisterRequest $request
     */
    private function createUserAddress(User $user, RegisterRequest $request): void {
        $user->userAddresses()->create([
            'country'  => $request->input('country'),
            'city'     => $request->input('city'),
            'state'    => $request->input('state'),
            'province' => $request->input('province'),
        ]);
    }

    /**
     * Upload user images.
     *
     * @param User $user
     * @param RegisterRequest $request
     */
    private function uploadUserImages(User $user, RegisterRequest $request): void {
        foreach ($request->file('images') as $key => $image) {
            $imageName = ($key + 1) . '_' . time() . '_' . $image->getClientOriginalName();
            $imagePath = Helper::fileUpload($image, 'user_images', $imageName);

            $user->photoGalleries()->create([
                'image' => $imagePath,
            ]);
        }
    }

    /**
     * Create user business experience.
     *
     * @param User $user
     * @param RegisterRequest $request
     */
    private function createBusinessExperience(User $user, RegisterRequest $request): void {
        $areasOfExpertise = $request->input('areas_of_expertise', []);
        $otherExpertise   = $request->input('other_expertise', []);

        //! Merge other_expertise into areas_of_expertise
        $mergedExpertise = array_merge($areasOfExpertise, $otherExpertise);

        $supportOffer      = $request->input('support_offer', []);
        $otherSupportOffer = $request->input('other_support_offer', []);

        //! Merge other_support_offer into support_offer
        $mergedSupportOffer = array_merge($supportOffer, $otherSupportOffer);

        //! Convert arrays to comma-separated strings
        $formattedExpertise    = str_replace('],[', ',', implode(',', $mergedExpertise));
        $formattedSupportOffer = str_replace('],[', ',', implode(',', $mergedSupportOffer));

        $user->businessExperiences()->create([
            'industry'            => $request->input('industry'),
            'other_industry'      => $request->input('other_industry'),
            'years_of_experience' => $request->input('years_of_experience'),
            'areas_of_expertise'  => $formattedExpertise,
            'support_offer'       => $formattedSupportOffer,
        ]);
    }
}
