<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Like;
use App\Models\Membership;
use App\Models\User;
use App\Notifications\ProfileDisliked;
use App\Notifications\ProfileLiked;
use Illuminate\Http\Request;

class LikeController extends Controller {
    protected function canPerformAction($userId) {
        // Get the user's membership
        $membership = Membership::where('user_id', $userId)->latest()->first();

        // If no membership, check for basic limit (5 swipes)
        if (!$membership) {
            $swipes = Like::where('user_id', $userId)->count();
            return $swipes < 20;
        }

        // Check if swipes limit is reached based on subscription
        $subscription = $membership->subscription;
        if ($membership->swipes >= $subscription->swipes_limit) {
            return false;
        }

        return true;
    }

    protected function incrementSwipes($userId) {
        // Get the user's membership
        $membership = Membership::where('user_id', $userId)->latest()->first();

        if ($membership) {
            // Increment the swipes count
            $membership->increment('swipes');
        }
    }

    public function likeProfile(Request $request, $profileId) {
        $userId = auth()->id();

        // Check if the user can perform the action
        if (!$this->canPerformAction($userId)) {
            return response()->json([
                'message' => 'Swipes limit reached. Please purchase a new subscription.',
            ], 403);
        }

        $like = Like::updateOrCreate(
            ['user_id' => $userId, 'profile_id' => $profileId],
            ['status' => 'like', 'is_like' => true]
        );

        // Increment the swipes count
        $this->incrementSwipes($userId);

        // Get the liked user's information
        $likedUser = User::findOrFail($profileId);
        $liker     = auth()->user();

        // Send notification to the liked user
        $likedUser->notify(new ProfileLiked($liker));

        return response()->json([
            'message' => 'Profile liked successfully',
            'like'    => $like,
        ]);
    }

    public function dislikeProfile(Request $request, $profileId) {
        $userId = auth()->id();

        // Check if the user can perform the action
        if (!$this->canPerformAction($userId)) {
            return response()->json([
                'message' => 'Swipes limit reached. Please purchase a new subscription.',
            ], 403);
        }

        $like = Like::updateOrCreate(
            ['user_id' => $userId, 'profile_id' => $profileId],
            ['status' => 'dislike', 'is_like' => false]
        );

        // Increment the swipes count
        $this->incrementSwipes($userId);

        // Get the disliked user's information
        $dislikedUser = User::findOrFail($profileId);
        $disliker     = auth()->user();

        // Send notification to the disliked user
        $dislikedUser->notify(new ProfileDisliked($disliker));

        return response()->json([
            'message' => 'Profile disliked successfully',
            'like'    => $like,
        ]);
    }

    public function back(Request $request) {
        $userId     = auth()->id();
        $membership = Membership::where('user_id', $userId)->latest()->first();

        if (!$membership) {
            return response()->json(['message' => 'No active membership found.'], 404);
        }

        $subscription = $membership->subscription;

        // Check if rewinds limit is reached
        if ($membership->rewinds >= $subscription->rewinds_limit) {
            return response()->json([
                'status'  => false,
                'message' => 'Rewinds limit reached. Please purchase a new subscription.',
            ], 403);
        }

        // Increment rewinds count
        $membership->increment('rewinds');

        // Perform back action
        $lastAction = Like::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastAction) {
            // Delete the last action
            $lastAction->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Last action undone successfully',
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'No actions to undo',
            ], 404);
        }
    }

    public function whoLikes(Request $request) {
        $user = $request->user();

        //! Check if the user has a membership and if it's of type 'prestige'
        $membership = Membership::where('user_id', $user->id)->latest()->first();

        if (!$membership || $membership->subscription->package_type !== 'prestige') {
            return Helper::jsonResponse(
                false,
                'You do not have access to this feature. Please purchase a Prestige subscription.',
                403
            );
        }

        //! Retrieve the list of users who have liked the authenticated user's profile
        $likedByUsers = $user->profile->likes()->with('user')->get();

        //? Transform the likedByUsers collection to only include necessary user data
        $likedByUsers = $likedByUsers->map(function ($like) {
            return [
                'id'    => $like->user->id,
                'name'  => $like->user->name,
                'email' => $like->user->email,
            ];
        });

        //! Count the number of users who liked the authenticated user's profile
        $likedCount = $likedByUsers->count();

        return Helper::jsonResponse(true, 'Users who liked your profile fetched successfully.', 200, [
            'liked_count'    => $likedCount,
            'liked_by_users' => $likedByUsers,
        ]);
    }

    public function whoViews(Request $request) {
        $user = $request->user();

        //! Check if the user has a membership and if it's of type 'prestige'
        $membership = Membership::where('user_id', $user->id)->latest()->first();

        if (!$membership || $membership->subscription->package_type !== 'prestige') {
            return Helper::jsonResponse(
                false,
                'You do not have access to this feature. Please purchase a Prestige subscription.',
                403
            );
        }

        //! Retrieve the list of users who have viewed the authenticated user's profile
        $viewedByUsers = $user->profile->views()->with('user')->get();

        //! Count the number of users who viewed the authenticated user's profile
        $viewedCount = $viewedByUsers->count();

        return Helper::jsonResponse(true, 'Users who viewed your profile fetched successfully.', 200, [
            'viewed_count'    => $viewedCount,
            'viewed_by_users' => $viewedByUsers,
        ]);
    }
}
