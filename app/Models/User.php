<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function activity_levels(): BelongsToMany
    {
        return $this->belongsToMany(ActivityLevel::class, UserActivityLevel::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class, 'goal_id');
    }

    public function medical_issues(): BelongsToMany
    {
        return $this->belongsToMany(MedicalIssue::class, UserMedicalIssue::class);
    }

    public function preferences(): BelongsToMany
    {
        return $this->belongsToMany(DietType::class, UserPreference::class);
    }
    public function cookingPrep(): BelongsTo
    {
        return $this->belongsTo(CookingPrep::class, 'cooking_prep_id');
    }
    public function cuisine(): BelongsTo
    {
        return $this->belongsTo(Cuisine::class, 'cuisine_id');
    }
    public function cooking_time(): BelongsTo
    {
        return $this->belongsTo(CookingTime::class, 'cooking_time_id');
    }
    public function appliances(): BelongsToMany
    {
        // We specify 'user_appliances' because that's the table name we created
        return $this->belongsToMany(Appliance::class, 'user_appliances', 'user_id', 'appliance_id');
    }
    public function mealPrepSchedule(): BelongsTo
    {
        return $this->belongsTo(MealPrepSchedule::class, 'meal_prep_schedule_id');
    }
    public function mealCount(): BelongsTo
    {
        return $this->belongsTo(MealCount::class);
    }

    /**
     * Specifies the user's FCM token
     *
     * @return string|array
     */
    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }

    public function user_membership()
    {
        return $this->hasOne(UserMembership::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }
    
    public function subscriptions()
    {
        return $this->hasMany(\App\Models\Subscription::class);
    }

    public function batchMealConfig()
    {
        return $this->hasOne(BatchMealConfig::class, 'user_id');
    }

    public function batchMealInventories()
    {
        return $this->hasMany(BatchMealInventory::class, 'user_id');
    }

}
