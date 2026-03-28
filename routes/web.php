<?php

use App\Http\Controllers\Admin\ActivityLevelController;
use App\Http\Controllers\Admin\AllergenController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CouponUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DietTypeController;
use App\Http\Controllers\Admin\ExercisePlanDayController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\FoodGroupController;
use App\Http\Controllers\Admin\FoodItemController;
use App\Http\Controllers\Admin\GoalController;
use App\Http\Controllers\Admin\ManageHomepageController;
use App\Http\Controllers\Admin\MealController;
use App\Http\Controllers\Admin\MealPlanController;
use App\Http\Controllers\Admin\MedicalIssueController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServingUnitController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SupplementController;
use App\Http\Controllers\Admin\SupplementTypeController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\FrontDashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('migrate',function(){
       Artisan::call('migrate', ['--force' => true]);
      return  $output = Artisan::output();

});
  

Route::controller(FrontController::class)->group(function () {
    Route::get('/sign-in', 'login')->name('sign-in');
    Route::get('/google/callback', 'handleGoogleCallback');
    Route::get('/sign-out', 'logout')->name('sign-out');

    Route::get('/register/step1', 'step1')->name('step1');
    Route::post('/store-step1', 'store_step1')->name('store-step1');
    Route::get('/register/step2', 'step2')->name('step2');
    Route::post('/store-step2', 'store_step2')->name('store-step2');
    Route::get('/register/step3', 'step3')->name('step3');
    Route::post('/store-step3', 'store_step3')->name('store-step3');
    Route::get('/register/step4', 'step4')->name('step4');
    Route::post('/store-step4', 'store_step4')->name('store-step4');
    Route::get('/register/step5', 'step5')->name('step5');
    Route::post('/store-step5', 'store_step5')->name('store-step5');
    Route::get('/register/step6', 'step6')->name('step6');
    Route::post('/store-step6', 'store_step6')->name('store-step6');
    Route::get('/register/step7', 'step7')->name('step7');
    Route::post('/store-step7', 'store_step7')->name('store-step7');
    Route::get('/register/step8', 'step8')->name('step8');
    Route::post('/store-step8', 'store_step8')->name('store-step8');
    Route::get('/register/step9', 'step9')->name('step9');
    Route::post('/store-step9', 'store_step9')->name('store-step9');
    Route::get('/register/step10', 'step10')->name('step10');
    Route::post('/store-step10', 'store_step10')->name('store-step10');
    Route::get('/register/step11', 'step11')->name('step11');
    Route::post('/store-step11', 'store_step11')->name('store-step11');
    Route::get('/register/step12', 'step12')->name('step12');
    Route::post('/store-step12', 'store_step12')->name('store-step12');

    Route::get('/buy-membership', 'buy_membership')->name('buy-membership');

    Route::get('/countries', 'get_countries')->name('get-countries');
    Route::post('/states', 'get_states')->name('get-states');
    Route::post('/cities', 'get_cities')->name('get-cities');

    Route::get('/weight-tracker', 'weight_tracker')->name('weight-tracker');
    Route::post('/update-current-weight', 'update_current_weight')->name('update-current-weight');
    Route::post('/weight-tracking-date-wise', 'weight_tracking_date_wise')->name('weight-tracking-date-wise');

    Route::get('/food-tracker', 'food_tracker')->name('food-tracker');
    Route::post('/food-tracking-date-wise', 'food_tracking_date_wise')->name('food-tracking-date-wise');

    Route::get('/bmi', 'bmi')->name('bmi');

    Route::get('/food-status', 'food_status')->name('food-status');
});

Route::controller(CheckoutController::class)->group(function () {
    // For admin side
    Route::get('/checkout/{id}', 'checkout')->name('checkout');
    Route::post('/apply-coupon', 'apply_coupon')->name('apply-coupon');
    Route::post('/complete-order', 'complete_order')->name('complete-order');
    Route::get('/complete-payment/{id}', 'complete_payment')->name('complete-payment');
    // For buy-membership
    Route::get('/process-payment-link', 'process_payment_link')->name('process-payment-link');
    Route::post('check-payment', 'check_payment')->name('check-payment');
    // Common
    Route::get('payment-success', 'payment_success')->name('payment-success');
    Route::get('payment-failure', 'payment_failure')->name('payment-failure');
});

Route::middleware(['auth:web'])->group(function () {
    Route::controller(FrontDashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('front-dashboard');
        Route::get('/add-food/{meal_id}', 'add_food')->name('add-food');
        Route::post('/food-item-details', 'food_item_details')->name('food-item-details');
        Route::post('/save-intake', 'save_intake')->name('save-intake');
        Route::post('/remove-intake-item', 'remove_intake_item')->name('remove-intake-item');
    });
});

Route::get('get-coupons', [HomeController::class, 'getCoupons'])->name('getCoupons');

Route::post('ckeditor/upload', [HomeController::class, 'uploadfile'])->name('ckeditor.upload');
Route::get('countries', [HomeController::class, 'getCountries'])->name('getCountries');
Route::get('states', [HomeController::class, 'getStates'])->name('getStates');
Route::get('cities', [HomeController::class, 'getCities'])->name('getCities');

Route::get('get-users', [HomeController::class, 'getUsers'])->name('getUsers');

// admin
Route::prefix('secureAdmin')->name('admin.')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('loginForm');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::middleware('adminAuth:admin')->group(function () {

        Route::controller(LoginController::class)->group(function () {
            Route::get('/change-password', 'change_password')->name('change-password');
            Route::post('/update-password', 'update_password')->name('update-password');
        });

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::controller(ActivityLevelController::class)->group(function () {
            Route::get('/activity-levels', 'index')->name('activity-levels');
            Route::get('/activity-levels/create', 'create')->name('activity-levels.create');
            Route::post('/activity-levels/store', 'store')->name('activity-levels.store');
            Route::get('/activity-levels/edit/{id}', 'edit')->name('activity-levels.edit');
            Route::post('/activity-levels/update/{id}', 'update')->name('activity-levels.update');
            Route::post('/activity-levels/destroy', 'destroy')->name('activity-levels.destroy');
            Route::post('/activity-levels/update-active', 'updateActive')->name('activity-levels.updateActive');
        });
        Route::controller(GoalController::class)->group(function () {
            Route::get('/goals', 'index')->name('goals');
            Route::get('/goals/create', 'create')->name('goals.create');
            Route::post('/goals/store', 'store')->name('goals.store');
            Route::get('/goals/edit/{id}', 'edit')->name('goals.edit');
            Route::post('/goals/update/{id}', 'update')->name('goals.update');
            Route::post('/goals/destroy', 'destroy')->name('goals.destroy');
            Route::post('/goals/update-active', 'updateActive')->name('goals.updateActive');
        });

        Route::controller(AllergenController::class)->group(function () {
            Route::get('/allergens', 'index')->name('allergens');
            Route::get('/allergens/create', 'create')->name('allergens.create');
            Route::post('/allergens/store', 'store')->name('allergens.store');
            Route::get('/allergens/edit/{id}', 'edit')->name('allergens.edit');
            Route::post('/allergens/update/{id}', 'update')->name('allergens.update');
            Route::post('/allergens/destroy', 'destroy')->name('allergens.destroy');
            Route::post('/allergens/update-active', 'updateActive')->name('allergens.updateActive');
        });

        Route::controller(DietTypeController::class)->group(function () {
            Route::get('/diet-types', 'index')->name('diet-types');
            Route::get('/diet-types/create', 'create')->name('diet-types.create');
            Route::post('/diet-types/store', 'store')->name('diet-types.store');
            Route::get('/diet-types/edit/{id}', 'edit')->name('diet-types.edit');
            Route::post('/diet-types/update/{id}', 'update')->name('diet-types.update');
            Route::post('/diet-types/destroy', 'destroy')->name('diet-types.destroy');
            Route::post('/diet-types/update-active', 'updateActive')->name('diet-types.updateActive');
        });

        Route::controller(FoodGroupController::class)->group(function () {
            Route::get('/food-groups', 'index')->name('food-groups');
            Route::get('/food-groups/create', 'create')->name('food-groups.create');
            Route::post('/food-groups/store', 'store')->name('food-groups.store');
            Route::get('/food-groups/edit/{id}', 'edit')->name('food-groups.edit');
            Route::post('/food-groups/update/{id}', 'update')->name('food-groups.update');
            Route::post('/food-groups/destroy', 'destroy')->name('food-groups.destroy');
            Route::post('/food-groups/update-active', 'updateActive')->name('food-groups.updateActive');
        });

        // Page
        Route::resource('pages', PageController::class);

        Route::controller(PageController::class)->group(function () {
            Route::post('/pages/toggle', 'toggle')->name('pages.toggle');
            Route::post('/pages/destroy', 'destroy')->name('pages.destroy');
        });

        Route::controller(FoodItemController::class)->group(function () {
            Route::get('/food-items', 'index')->name('food-items');
            Route::get('/food-items/create', 'create')->name('food-items.create');
            Route::post('/food-items/store', 'store')->name('food-items.store');
            Route::get('/food-items/edit/{id}', 'edit')->name('food-items.edit');
            Route::post('/food-items/update/{id}', 'update')->name('food-items.update');
            Route::post('/food-items/destroy', 'destroy')->name('food-items.destroy');
            Route::post('/food-items/update-active', 'updateActive')->name('food-items.updateActive');
            Route::get('/food-items/get-similar-items', 'getSimilarItems')->name('food-items.getSimilarItems');
            Route::get('/food-items/add-similar-items', 'addSimilarItem')->name('food-items.addSimilarItem');
            Route::get('/food-items/remove-similar-items', 'removeSimilarItem')->name('food-items.removeSimilarItem');
            Route::get('/food-items/update-similar-item-qty', 'updateSimilarItemQty')->name('food-items.updateSimilarItemQty');
        });

        Route::controller(MealController::class)->group(function () {
            Route::get('/meals', 'index')->name('meals');
            Route::get('/meals/create', 'create')->name('meals.create');
            Route::post('/meals/store', 'store')->name('meals.store');
            Route::get('/meals/edit/{id}', 'edit')->name('meals.edit');
            Route::post('/meals/update/{id}', 'update')->name('meals.update');
            Route::post('/meals/destroy', 'destroy')->name('meals.destroy');
            Route::post('/meals/update-active', 'updateActive')->name('meals.updateActive');
        });

        Route::controller(MealPlanController::class)->group(function () {
            Route::get('/meal-plans', 'index')->name('meal-plans');
            Route::get('/meal-plans/create', 'create')->name('meal-plans.create');
            Route::post('/meal-plans/store', 'store')->name('meal-plans.store');
            Route::get('/meal-plans/edit/{id}', 'edit')->name('meal-plans.edit');
            Route::get('/meal-plans/meals/{id}', 'meals')->name('meal-plans.meals');
            Route::post('/meal-plans/update/{id}', 'update')->name('meal-plans.update');
            Route::post('/meal-plans/destroy', 'destroy')->name('meal-plans.destroy');
            Route::post('/meal-plans/update-active', 'updateActive')->name('meal-plans.updateActive');
            Route::post('/meal-plans/update-food-items', 'updateFoodItems')->name('meal-plans.updateFoodItems');
            Route::post('/meal-plans/update-food-item_qty', 'updateFoodItemQty')->name('meal-plans.updateFoodItemQty');
            Route::post('/remove-item-from-meal-plan', 'remove_item_from_meal_plan')->name('meal-plans.remove-item');
            Route::post('/meal-plans/fetch-meals', 'fetch_meals')->name('meal-plans.fetch-meals');
        });

        Route::controller(ServingUnitController::class)->group(function () {
            Route::get('/serving-units', 'index')->name('serving-units');
            Route::get('/serving-units/create', 'create')->name('serving-units.create');
            Route::post('/serving-units/store', 'store')->name('serving-units.store');
            Route::get('/serving-units/edit/{id}', 'edit')->name('serving-units.edit');
            Route::post('/serving-units/update/{id}', 'update')->name('serving-units.update');
            Route::post('/serving-units/destroy', 'destroy')->name('serving-units.destroy');
            Route::post('/serving-units/update-active', 'updateActive')->name('serving-units.updateActive');
        });

        Route::controller(PackageController::class)->group(function () {
            Route::get('/packages', 'index')->name('packages');
            Route::get('/packages/create', 'create')->name('packages.create');
            Route::post('/packages/store', 'store')->name('packages.store');
            Route::get('/packages/edit/{id}', 'edit')->name('packages.edit');
            Route::post('/packages/update/{id}', 'update')->name('packages.update');
            Route::post('/packages/destroy', 'destroy')->name('packages.destroy');
            Route::post('/packages/update-active', 'updateActive')->name('packages.updateActive');
        });

        Route::controller(FeatureController::class)->group(function () {
            Route::get('/features', 'index')->name('features');
            Route::get('/features/create', 'create')->name('features.create');
            Route::post('/features/store', 'store')->name('features.store');
            Route::get('/features/edit/{id}', 'edit')->name('features.edit');
            Route::post('/features/update/{id}', 'update')->name('features.update');
            Route::post('/features/destroy', 'destroy')->name('features.destroy');
            Route::post('/features/update-active', 'updateActive')->name('features.updateActive');
        });

        Route::controller(MembershipController::class)->group(function () {
            Route::get('/memberships', 'index')->name('memberships');
            Route::get('/memberships/create', 'create')->name('memberships.create');
            Route::post('/memberships/store', 'store')->name('memberships.store');
            Route::get('/memberships/edit/{id}', 'edit')->name('memberships.edit');
            Route::post('/memberships/update/{id}', 'update')->name('memberships.update');
            Route::post('/memberships/destroy', 'destroy')->name('memberships.destroy');
            Route::post('/memberships/update-active', 'updateActive')->name('memberships.updateActive');
            Route::get('/memberships/user-membership/{id}', 'user_membership')->name('memberships.user-membership');
            Route::post('/memberships/store-user-membership', 'store_user_membership')->name('memberships.store-user-membership');
            Route::get('/expired-memberships', 'expired_memberships')->name('expired-memberships');
            Route::get('/expiring-memberships', 'expiring_memberships')->name('expiring-memberships');
            Route::post('/memberships', 'memberships')->name('fetch-memberships');
        });

        Route::controller(MedicalIssueController::class)->group(function () {
            Route::get('/medical-issues', 'index')->name('medical-issues');
            Route::get('/medical-issues/create', 'create')->name('medical-issues.create');
            Route::post('/medical-issues/store', 'store')->name('medical-issues.store');
            Route::get('/medical-issues/edit/{id}', 'edit')->name('medical-issues.edit');
            Route::post('/medical-issues/update/{id}', 'update')->name('medical-issues.update');
            Route::post('/medical-issues/destroy', 'destroy')->name('medical-issues.destroy');
            Route::post('/medical-issues/update-active', 'updateActive')->name('medical-issues.updateActive');
        });

        Route::controller(SupplementTypeController::class)->group(function () {
            Route::get('/supplement-types', 'index')->name('supplement-types');
            Route::get('/supplement-types/create', 'create')->name('supplement-types.create');
            Route::post('/supplement-types/store', 'store')->name('supplement-types.store');
            Route::get('/supplement-types/edit/{id}', 'edit')->name('supplement-types.edit');
            Route::post('/supplement-types/update/{id}', 'update')->name('supplement-types.update');
            Route::post('/supplement-types/destroy', 'destroy')->name('supplement-types.destroy');
            Route::post('/supplement-types/update-active', 'updateActive')->name('supplement-types.updateActive');
        });

        Route::controller(SupplementController::class)->group(function () {
            Route::get('/supplements', 'index')->name('supplements');
            Route::get('/supplements/create', 'create')->name('supplements.create');
            Route::post('/supplements/store', 'store')->name('supplements.store');
            Route::get('/supplements/edit/{id}', 'edit')->name('supplements.edit');
            Route::post('/supplements/update/{id}', 'update')->name('supplements.update');
            Route::post('/supplements/destroy', 'destroy')->name('supplements.destroy');
            Route::post('/supplements/update-active', 'updateActive')->name('supplements.updateActive');
        });

        Route::controller(UserController::class)->group(function () {
            Route::get('/users', 'index')->name('users');
            Route::get('/users/create', 'create')->name('users.create');
            Route::post('/users/store', 'store')->name('users.store');
            Route::get('/users/edit/{id}', 'edit')->name('users.edit');
            Route::post('/users/update/{id}', 'update')->name('users.update');
            Route::post('/users/destroy', 'destroy')->name('users.destroy');
            Route::post('/users/update-active', 'updateActive')->name('users.updateActive');
            Route::get('/users/diet-plan/{id}', 'diet_plan')->name('users.diet-plan');
            Route::post('/users/fetch-meal', 'fetch_meal')->name('users.fetch-meal');
            Route::post('/remove-food-item-from-meal-plan', 'remove_food_item_from_meal_plan')->name('users.remove-food-item');
            Route::post('/drag-food-item', 'drag_food_item')->name('users.drag-food-item');
            Route::post('/fetch-items', 'fetch_items')->name('users.fetch-items');
            Route::post('/fetch-items-randomly', 'fetch_items_randomly')->name('users.fetch-items-randomly');
            Route::post('/add-items-to-meal', 'add_items_to_meal')->name('users.add-items-to-meal');
            Route::post('/generate-payment-link', 'generate_payment_link')->name('generate-payment-link');
        });

        Route::controller(ExercisePlanDayController::class)->group(function () {
            Route::get('/exercise-plan-days', 'index')->name('exercise-plan-days');
            Route::get('/exercise-plan-days/create', 'create')->name('exercise-plan-days.create');
            Route::post('/exercise-plan-days/store', 'store')->name('exercise-plan-days.store');
            Route::get('/exercise-plan-days/edit/{id}', 'edit')->name('exercise-plan-days.edit');
            Route::post('/exercise-plan-days/update/{id}', 'update')->name('exercise-plan-days.update');
            Route::post('/exercise-plan-days/destroy', 'destroy')->name('exercise-plan-days.destroy');
            Route::post('/exercise-plan-days/update-active', 'updateActive')->name('exercise-plan-days.updateActive');
        });

        Route::controller(CouponController::class)->group(function () {
            Route::get('/coupons', 'index')->name('coupons');
            Route::get('/coupons/create', 'create')->name('coupons.create');
            Route::post('/coupons/store', 'store')->name('coupons.store');
            Route::get('/coupons/edit/{id}', 'edit')->name('coupons.edit');
            Route::post('/coupons/update/{id}', 'update')->name('coupons.update');
            Route::post('/coupons/destroy', 'destroy')->name('coupons.destroy');
            Route::post('/coupons/update-active', 'updateActive')->name('coupons.updateActive');
            Route::get('/coupons/generate-random-coupon', 'generateRandomCoupon')->name('coupons.generateRandomCoupon');
            Route::get('/coupons/get-coupon/{id}', 'getCoupons')->name('coupons.getCoupon');
        });

        // coupon-users
        Route::resource('coupon-users', CouponUserController::class);
        Route::get('/coupon-users/copy/{id}', [CouponUserController::class, 'copy'])->name('coupon-users.copy');
        Route::post('/coupon-users/destroy', [CouponUserController::class, 'destroy'])->name('coupon-users.destroy');
        Route::post('/coupon-users/update-active', [CouponUserController::class, 'updateActive'])->name('coupon-users.updateActive');

        Route::controller(SettingController::class)->group(function () {
            Route::get('/settings', 'index')->name('settings');
            Route::post('/settings/update', 'update')->name('settings.update');

            Route::get('/chatgpt-settings', 'chatgptSettings')->name('chatgptSettings');
            Route::post('/chatgpt-settings/update', 'chatgptSettingsUpdate')->name('chatgptSettings.update');

            Route::get('/login-settings', 'loginSettings')->name('loginSettings');
            Route::post('/login-settings/update', 'loginSettingsUpdate')->name('loginSettings.update');
        });

        Route::controller(ManageHomepageController::class)->group(function () {
            Route::get('/manage-homepage', 'index')->name('manage-homepage');
            Route::post('/update-manage-homepage', 'update')->name('update-manage-homepage');
            Route::post('/upload-photo', 'upload_photo')->name('upload-photo');
            Route::post('/delete-our-clients-image', 'delete_our_clients_image')->name('delete-our-clients-image');
        });

        Route::controller(ServiceController::class)->group(function () {
            Route::get('/services', 'index')->name('services');
            Route::get('/service', 'create')->name('add-service');
            Route::post('/store-service', 'store')->name('store-service');
            Route::get('/service/{service}', 'edit')->name('edit-service');
            Route::post('/update-service', 'update')->name('update-service');
            Route::post('/service/update-active', 'updateActive')->name('service.updateActive');
            Route::post('/service/destroy', 'destroy')->name('service.destroy');
        });

        Route::controller(TestimonialController::class)->group(function () {
            Route::get('/testimonials', 'index')->name('testimonials');
            Route::get('/testimonial', 'create')->name('add-testimonial');
            Route::post('/store-testimonial', 'store')->name('store-testimonial');
            Route::get('/testimonial/{testimonial}', 'edit')->name('edit-testimonial');
            Route::post('/update-testimonial', 'update')->name('update-testimonial');
            Route::post('/testimonial/update-active', 'updateActive')->name('testimonial.updateActive');
            Route::post('/testimonial/destroy', 'destroy')->name('testimonial.destroy');
        });

        // Newsletter
        Route::controller(NewsletterController::class)->group(function () {
            Route::get('/newsletter', 'index')->name('newsletter');
            Route::post('/update-newsletter', 'update')->name('update-newsletter');
        });
    });
});

Route::get('/test-notification', [App\Http\Controllers\ScriptController::class, 'testNotification'])->name('testNotification');
Route::get('/user-diet-plan', [App\Http\Controllers\ScriptController::class, 'dietPlan'])->name('dietPlan');

Route::controller(HomeController::class)->group(function () {
    Route::post('/store-contact-form', 'store_contact_form')->name('store-contact-form');
    Route::post('/subscribe-newsletter', 'subscribe_newsletter')->name('subscribe-newsletter');
    Route::get('/{alias?}', 'index')->name('home');
});

Route::fallback(function () {
    if (\Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    if (request()->is('admin') || request()->is('secureAdmin/*')) {
        return redirect()->route('admin.loginForm');
    }

    return view('page-not-found');
});
