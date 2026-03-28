<?php

use App\Http\Controllers\Api\PaymentSubscriptionDataController;
use App\Http\Controllers\Api\StripeWebhookController;
use App\Http\Controllers\Api\TestDBController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\AppController;
use App\Http\Controllers\Api\BatchMealController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\MealController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FavouriteMealController;
use App\Http\Controllers\Api\SubscriptionPlanController;
use App\Http\Middleware\CheckValidRequestBasedOnUserAndKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: user,key,token,Content-Type, x-xsrf-token');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

Route::middleware([CheckValidRequestBasedOnUserAndKey::class])->group(function () {

    
    Route::post('/testDB', [TestDBController::class, 'test']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::prefix('v1')->group(function () {

        Route::controller(AuthController::class)->group(function () {
            Route::post('authenticate', 'Authenticate');
            Route::post('verify-otp', 'verify_otp');
            Route::post('social-login', 'socialLogin');
            Route::post('refresh', 'refresh')->middleware('auth:sanctum');
            Route::post('resend-otp', 'resend_otp');
        });

        Route::middleware('auth:sanctum')->group(function () {

            //one time payment
            Route::post('/create-payment-intent', [PaymentController::class, 'create']);

 
            // Subscription
            Route::post('/create-subscription', [SubscriptionController::class, 'createSubscription']);
            
            // Cancel subscription
            Route::post('/cancel-subscription', [SubscriptionController::class, 'cancel']);

            //verify subscription
            Route::post('/verify-payment', [SubscriptionController::class, 'verify']);

            //Povide all data about payments
            Route::get('/payments-subscriptions-data', [PaymentSubscriptionDataController::class,'index']);



            Route::controller(UserController::class)->group(function () {
                Route::post('update-user-information', 'updateUserInformation');
                Route::post('update-fcm-token', 'updateFcmToken');
                Route::post('buy-membership', 'buyMembership');
                Route::get('my-payments', 'myPayments');
                Route::post('update-current-weight', 'updateCurrentWeight');
                Route::post('update-target-weight', 'updateTargetWeight');
                Route::get('fetch-weight-history', 'fetchWeightHistory');
                Route::get('my-packages', 'myPackages');
                Route::post('replace-food-item', 'replaceFoodItem');
                Route::post('upload-image', 'uploadImage');
                Route::get('user-progress-images', 'userProgressImages');
                Route::post('update-profile', 'updateProfile');
                Route::post('upload-profile-image', 'uploadProfileImage');
                Route::get('my-coupons', 'myCoupons');
                Route::post('apply-coupon', 'applyCoupon');
                Route::get('get-daily-average', 'getAveragDailyDietGoals');
                Route::get('notifications', 'notifications');
                Route::post('delete-profile', 'deleteProfile');
                Route::post('generate-user-diet-plan', 'generateUserDietPlan');
            });

            Route::controller(MealController::class)->group(function () {
                Route::post('save-user-diet-plan', 'saveUserDietPlan');
                Route::get('fetch-meals', 'fetchMeals');
                Route::post('save-intake-food-item', 'saveIntakeFoodItem');
                Route::post('update-water-intake', 'updateWaterIntake');
                Route::get('fetch-my-meals', 'fetchMyMeals');
                Route::get('fetch-macro-tracker-data', 'fetchMacroTrackerData');
            });

            Route::controller(DashboardController::class)->group(function () {
                Route::get('get-dashboard-data', 'fetchDashboardData');
            });

            Route::controller(FoodController::class)->group(function () {
                Route::post('like-dislike-food-item', 'likeDislike');
                Route::get('similar-food-items', 'similarFoodItems');
            });
        });

        Route::controller(MealController::class)->group(function () {
            Route::get('get-food-items', 'getFoodItems');
            Route::get('getWeeklyDietPlanFromChatGPT', 'getWeeklyDietPlanFromChatGPT');
        });

        Route::controller(AppController::class)->group(function () {
            Route::get('app-config', 'appConfig');
            Route::get('get-countries', 'getCountries');
            Route::get('get-states', 'getStates');
            Route::get('get-cities', 'getCities');
        });

        Route::controller(PackageController::class)->group(function () {
            Route::get('get-packages', 'getPackages');
        });

        Route::controller(ChatController::class)->group(function () {
            Route::post('get-full-diet-plan', 'getFullDietPlanFromChatGPTforUser');
        });

        Route::controller(ChatController::class)->group(function () {
            Route::post('get-surprise-meal', 'getSurpriseMeal');
        });

        Route::controller(ChatController::class)->group(function () {
            Route::post('get-user-meal', 'generateUserMeal');
        });

        Route::controller(SubscriptionPlanController::class)->group(function () {
            Route::post('/plans/create', 'store');
        });

        Route::controller(SubscriptionPlanController::class)->group(function () {
            Route::post('/plans/delete', 'destroy');
        });

        Route::controller(SubscriptionPlanController::class)->group(function () {
            Route::post('/plans/update', 'update');
        });

        Route::controller(SubscriptionPlanController::class)->group(function () {
            Route::get('/plans/getAllPlans', 'getPlans');
        });

        Route::controller(PaymentSubscriptionDataController::class)->group(function () {
            Route::post('/checkSubscription', 'checkSubscription');
        });

        Route::controller(FavouriteMealController::class)->group(function () {
            Route::post('/add-favourite-meal', 'addFavourite');
        });

        Route::controller(FavouriteMealController::class)->group(function () {
            Route::post('/delete-favourite-meal', 'deleteFavourite');
        });

        Route::controller(FavouriteMealController::class)->group(function () {
            Route::post('/get-favourite-meal', 'getFavouriteMeals');
        });

        Route::controller(FavouriteMealController::class)->group(function () {
            Route::post('/check-isFavourite', 'checkIsFavourite');
        });

        Route::controller(FavouriteMealController::class)->group(function () {
            Route::post('/check-SM-isFavourite', 'checkSM_IsFavourite');
        });
        

        Route::controller(FavouriteMealController::class)->group(function () {
            Route::post('/update-isFavourite', 'updateIsFavourite');
        });

        Route::controller(FavouriteMealController::class)->group(function () {
            Route::post('/remove-isFavourite', 'removeFavourite');
        });

        Route::controller(BatchMealController::class)->group(function () {
            Route::post('/generate-batch-meal', 'generateBatchMeal');
        });

        Route::controller(BatchMealController::class)->group(function () {
            Route::post('/consume-batch-meal', 'consumePortion');
        });






    });
});
