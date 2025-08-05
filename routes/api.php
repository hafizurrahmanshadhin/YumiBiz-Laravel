<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\BoostController;
use App\Http\Controllers\FirebaseTokenController;
use App\Http\Controllers\GETRequestController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Profile\BusinessExperienceController;
use App\Http\Controllers\Profile\PhotoGalleryController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\SearchController;
use App\Http\Controllers\Profile\UserAddressController;
use App\Http\Controllers\Profile\UserEducationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//! Auth Routes
Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
Route::post('/send-otp', [ResetPasswordController::class, 'SendOTPCode'])->name('send-otp');
Route::post('/verify-otp', [ResetPasswordController::class, 'VerifyOTP'])->name('verify-otp');
Route::post('/reset-password', [ResetPasswordController::class, 'ResetPassword'])->name('reset-password');

//! Profile Routes
Route::get('/profile', [ProfileController::class, 'Profile'])->name('profile');
Route::get('/alluserExceptLoginUser', [ProfileController::class, 'alluserExceptLoginUser'])->name('allprofile');
Route::post('/profile/edit', [ProfileController::class, 'EditProfile'])->name('profile.edit');
Route::post('/multiple-profile', [ProfileController::class, 'getProfilesByIds']);

Route::post('/profile/upload-images', [PhotoGalleryController::class, 'UploadImage'])->name('profile.upload-images');
Route::delete('/profile/delete-image/{imageId}', [PhotoGalleryController::class, 'DeleteImage'])->name('profile.delete-image');

Route::post('/profile/education/add', [UserEducationController::class, 'AddEducation'])->name('profile.education.add');
Route::post('/profile/education/{id}/edit', [UserEducationController::class, 'EditEducation'])->name('profile.education.edit');
Route::delete('/profile/education/{id}', [UserEducationController::class, 'DeleteEducation'])->name('profile.education.delete');

Route::post('/profile/experiences/add', [BusinessExperienceController::class, 'AddExperience'])->name('profile.experiences.add');
Route::post('/profile/experiences/{id}/edit', [BusinessExperienceController::class, 'EditExperience'])->name('profile.experiences.edit');
Route::delete('/profile/experiences/{id}', [BusinessExperienceController::class, 'DeleteExperience'])->name('profile.experiences.delete');

Route::post('/profile/addresses/add', [UserAddressController::class, 'AddAddress'])->name('profile.addresses.add');
Route::post('/profile/addresses/{id}/edit', [UserAddressController::class, 'EditAddress'])->name('profile.addresses.edit');
Route::delete('/profile/addresses/{id}', [UserAddressController::class, 'DeleteAddress'])->name('profile.addresses.delete');

//! Search and Filter Routes
Route::get('/users/filter', [SearchController::class, 'filter'])->name('search');
Route::get('/users/search', [SearchController::class, 'search'])->name('search');

//! Like Dislike and Back Profile Routes
Route::post('/profile/{profileId}/like', [LikeController::class, 'likeProfile']);
Route::post('/profile/{profileId}/dislike', [LikeController::class, 'dislikeProfile']);
Route::post('/profile/back', [LikeController::class, 'back'])->name('profile.back');
Route::get('/profile/{id}', [ProfileController::class, 'showProfile'])->name('profile.show');

//! Route who like me and who view my profile
Route::get('/who-likes', [LikeController::class, 'whoLikes'])->name('who-likes');
Route::get('/who-viewed', [ProfileController::class, 'whoViewedMyProfile'])->name('who-viewed');

//! Notification Routes
Route::get('/notifications', [NotificationController::class, 'getNotifications']);

//! Subscriptions and Boost Routes
Route::post('/purchase-subscription', [SubscriptionController::class, 'purchaseSubscription'])->name('purchase.subscription');
Route::post('/boost/purchase', [BoostController::class, 'purchaseBoost'])->name('boost.purchase');

//! Token
Route::post('/firebase/token/store', [FirebaseTokenController::class, 'store']);
Route::post('/firebase/token/get', [FirebaseTokenController::class, 'getToken']);
Route::post('/firebase/token/delete', [FirebaseTokenController::class, 'deleteToken']);

//! Fetch data
Route::get('/fetch-looking-for', [GETRequestController::class, 'fetchLookingFor'])->name('looking-for');
Route::get('/fetch-industry', [GETRequestController::class, 'fetchIndustry'])->name('industry');
Route::get('/fetch-years-of-experience', [GETRequestController::class, 'fetchYearsOfExperience'])->name('years-of-experience');
Route::get('/fetch-expertise', [GETRequestController::class, 'fetchExpertise'])->name('expertise');
Route::get('/fetch-support-offer', [GETRequestController::class, 'fetchSupportOffer'])->name('support-offer');
Route::get('/fetch-subscriptions', [GETRequestController::class, 'fetchSubscriptions'])->name('subscriptions');
Route::get('/fetch-boost', [GETRequestController::class, 'fetchBoost'])->name('boost');
Route::get('/fetch-countries', [GETRequestController::class, 'fetchCountries'])->name('fetch-countries');
Route::post('/fetch-cities', [GETRequestController::class, 'fetchCities'])->name('fetch-cities');
Route::post('/fetch-states', [GETRequestController::class, 'fetchStates'])->name('fetch-states');
Route::post('/fetch-province', [GETRequestController::class, 'fetchProvinces'])->name('fetch-province');

//! Laravel Socialite Login. Login With Google
Route::post('/login/google', [SocialLoginController::class, 'googleLogin']);

//! Delete Account
Route::delete('/user/delete', [UserController::class, 'deleteAccount'])->middleware('auth');
