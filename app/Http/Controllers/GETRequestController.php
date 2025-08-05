<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Boost;
use App\Models\Meta;
use App\Models\Subscription;
use App\Models\UserAddress;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GETRequestController extends Controller {
    /**
     * Fetch active lookingFor descriptions from the metas table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    // public function fetchLookingFor(Request $request): JsonResponse {
    //     try {
    //         $lookingForDescriptions = Meta::where('type', 'lookingFor')
    //             ->where('status', 'active')
    //             ->pluck('description');
    //         return Helper::jsonResponse(true, 'Active lookingFor descriptions fetched successfully.', 200, $lookingForDescriptions);
    //     } catch (Exception $e) {
    //         return Helper::jsonResponse(false, 'Failed to fetch active lookingFor descriptions.', 500, ['error' => $e->getMessage()]);
    //     }
    // }
    public function fetchLookingFor(Request $request): JsonResponse {
        try {
            $lookingForDescriptions = Meta::where('type', 'lookingFor')
                ->where('status', 'active')
                ->select('id', 'description')
                ->get();
            return Helper::jsonResponse(true, 'Active lookingFor descriptions fetched successfully.', 200, $lookingForDescriptions);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to fetch active lookingFor descriptions.', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Fetch active industry descriptions from the metas table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchIndustry(Request $request): JsonResponse {
        try {
            $industryDescriptions = Meta::where('type', 'industry')
                ->where('status', 'active')
                ->pluck('description');

            return Helper::jsonResponse(true, 'Active industry descriptions fetched successfully.', 200, $industryDescriptions);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to fetch active industry descriptions.', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Fetch active years of experience descriptions from the metas table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchYearsOfExperience(Request $request): JsonResponse {
        try {
            $yearsOfExperienceDescriptions = Meta::where('type', 'yearsOfExperience')
                ->where('status', 'active')
                ->pluck('description');

            return Helper::jsonResponse(true, 'Active years of experience descriptions fetched successfully.', 200, $yearsOfExperienceDescriptions);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to fetch active years of experience descriptions.', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Fetch active expertise descriptions from the metas table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchExpertise(Request $request): JsonResponse {
        try {
            $expertiseDescriptions = Meta::where('type', 'expertise')
                ->where('status', 'active')
                ->pluck('description');

            return Helper::jsonResponse(true, 'Active expertise descriptions fetched successfully.', 200, $expertiseDescriptions);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to fetch active expertise descriptions.', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Fetch active support offer descriptions from the metas table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchSupportOffer(Request $request): JsonResponse {
        try {
            $supportOfferDescriptions = Meta::where('type', 'supportOffer')
                ->where('status', 'active')
                ->pluck('description');

            return Helper::jsonResponse(true, 'Active support offer descriptions fetched successfully.', 200, $supportOfferDescriptions);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to fetch active support offer descriptions.', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Fetch all active subscriptions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchSubscriptions(Request $request): JsonResponse {
        try {
            $subscriptions = Subscription::where('status', 'active')->get();
            $subscriptions->transform(function ($subscription) {
                $subscription->feature = json_decode($subscription->feature);
                return $subscription;
            });
            return Helper::jsonResponse(true, 'Subscriptions list fetched successfully.', 200, $subscriptions);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to fetch subscriptions list.', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Fetch boost.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchBoost(Request $request): JsonResponse {
        try {
            $boost = Boost::get();
            return Helper::jsonResponse(true, 'Boosts list fetched successfully.', 200, $boost);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to fetch boosts list.', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Fetch active countries from the user_addresses table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchCountries(Request $request): JsonResponse {
        try {
            $countries = UserAddress::where('status', 'active')
                ->distinct()
                ->pluck('country');

            if ($countries->isEmpty()) {
                return Helper::jsonResponse(false, 'No active countries found', 200, []);
            }

            return Helper::jsonResponse(true, 'Active countries fetched successfully.', 200, $countries);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to fetch active countries.', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Fetch active cities for a specific country from the user_addresses table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchCities(Request $request): JsonResponse {
        try {
            $country = $request->input('country');

            $cities = UserAddress::where('country', $country)
                ->where('status', 'active')
                ->distinct()
                ->pluck('city');

            if ($cities->isEmpty()) {
                return Helper::jsonResponse(false, 'No active cities found for ' . $country . '.', 200, []);
            }

            return Helper::jsonResponse(true, 'Active cities for ' . $country . ' fetched successfully.', 200, $cities);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to fetch active cities.', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Fetch active states for a specific country and city from the user_addresses table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchStates(Request $request): JsonResponse {
        try {
            $country = $request->input('country');
            $city    = $request->input('city');

            $states = UserAddress::where('country', $country)
                ->where('city', $city)
                ->where('status', 'active')
                ->distinct()
                ->pluck('state');

            if ($states->isEmpty()) {
                return Helper::jsonResponse(false, 'No active states found for ' . $city . ', ' . $country . '.', 200, []);
            }

            return Helper::jsonResponse(true, 'Active states for ' . $city . ', ' . $country . ' fetched successfully.', 200, $states);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to fetch active states.', 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Fetch active provinces for a specific country, city, and state from the user_addresses table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchProvinces(Request $request): JsonResponse {
        try {
            $country = $request->input('country');
            $city    = $request->input('city');
            $state   = $request->input('state');

            $provinces = UserAddress::where('country', $country)
                ->where('city', $city)
                ->where('state', $state)
                ->where('status', 'active')
                ->distinct()
                ->pluck('province');

            if ($provinces->isEmpty()) {
                return Helper::jsonResponse(false, 'No active provinces found for ' . $country . ', ' . $city . ', and ' . $state . '.', 200, []);
            }

            return Helper::jsonResponse(true, 'Active provinces for ' . $country . ', ' . $city . ', and ' . $state . ' fetched successfully.', 200, $provinces);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Failed to fetch active provinces.', 500, ['error' => $e->getMessage()]);
        }
    }
}
