<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Membership;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SubscriptionController extends Controller {
    public function purchaseSubscription(Request $request) {
        // Validate the request
        $validated = $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
        ]);

        $user = auth()->user();

        // Retrieve the selected subscription plan
        $subscription = Subscription::find($validated['subscription_id']);

        // Calculate the end date based on the timeline
        $startDate = Carbon::now();
        $endDate   = $startDate->copy()->addMonths($subscription->timeline);

        // Check if the user already has a membership
        $membership = Membership::where('user_id', $user->id)->first();

        if ($membership) {
            // Update the existing membership
            $membership->update([
                'subscription_id' => $subscription->id,
                'start_date'      => $startDate,
                'end_date'        => $endDate,
                'swipes'          => 0,
                'rewinds'         => 0,
            ]);
        } else {
            // Create a new membership entry
            Membership::create([
                'user_id'         => $user->id,
                'subscription_id' => $subscription->id,
                'start_date'      => $startDate,
                'end_date'        => $endDate,
                'swipes'          => 0,
                'rewinds'         => 0,
            ]);
        }

        // Update user subscription status
        $user->is_subscribed = 1;
        $user->save();

        return Helper::jsonResponse(true, 'Subscription purchased successfully.', 200);
    }
}
