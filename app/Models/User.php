<?php

namespace App\Models;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject {
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
        'status',
        'otp',
        'linkedin_id',
        'role',
        'agree_to_terms',
        'is_subscribed',
        'is_boost',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'agree_to_terms'    => 'boolean',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier(): mixed {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array {
        return [];
    }

    public function profile(): HasOne {
        return $this->hasOne(Profile::class);
    }

    public function userAddresses(): HasMany {
        return $this->hasMany(UserAddress::class);
    }

    public function userEducations(): HasMany {
        return $this->hasMany(UserEducation::class);
    }

    public function photoGalleries(): HasMany {
        return $this->hasMany(PhotoGallery::class);
    }

    public function lookingFor(): BelongsToMany {
        return $this->belongsToMany(Meta::class, 'user_responses', 'user_id', 'meta_id');
    }

    public function businessExperiences(): HasMany {
        return $this->hasMany(BusinessExperience::class);
    }

    public function likes() {
        return $this->hasMany(Like::class, 'user_id', 'id');
    }

    public function profileViews(): HasMany {
        return $this->hasMany(ProfileView::class, 'profile_id');
    }

    public function viewedProfiles(): HasMany {
        return $this->hasMany(ProfileView::class, 'viewer_id');
    }

    public function user_responses(): HasMany {
        return $this->hasMany(UserResponse::class);
    }

    public function passwordReset() {
        return $this->hasOne(PasswordReset::class, 'email', 'email');
    }

    public function isSubscribed() {
        return $this->membership()->exists();
    }
    public function memberships(): HasMany {
        return $this->hasMany(Membership::class);
    }

    public function boosts() {
        return $this->hasMany(UserBoost::class);
    }

    //! Method to check for active boosts
    public function hasActiveBoost() {
        return $this->boosts()->where('expires_at', '>', now())->exists();
    }

    //! Calculate profile completion percentage
    public function getProfileCompletionPercentage(): int {
        $totalCriteria     = 22;
        $completedCriteria = 0;

        if ($this->name) {
            $completedCriteria++;
        }

        if ($this->email) {
            $completedCriteria++;
        }

        if ($this->profile) {
            if ($this->profile->user_name) {
                $completedCriteria++;
            }

            if ($this->profile->age) {
                $completedCriteria++;
            }

            if ($this->profile->gender) {
                $completedCriteria++;
            }
            if ($this->profile->bio) {
                $completedCriteria++;
            }
        }

        if ($this->userAddresses->count() > 0) {
            $address = $this->userAddresses->first();
            if ($address->country) {
                $completedCriteria++;
            }

            if ($address->city) {
                $completedCriteria++;
            }

            if ($address->state) {
                $completedCriteria++;
            }

            if ($address->province) {
                $completedCriteria++;
            }
        }

        if ($this->photoGalleries->count() > 0) {
            $completedCriteria++;
        }

        if ($this->businessExperiences->count() > 0) {
            $experience = $this->businessExperiences->first();
            if ($experience->industry) {
                $completedCriteria++;
            }

            if ($experience->years_of_experience) {
                $completedCriteria++;
            }

            if ($experience->areas_of_expertise) {
                $completedCriteria++;
            }

            if ($experience->support_offer) {
                $completedCriteria++;
            }
            if ($experience->other_industry) {
                $completedCriteria++;
            }
            if ($experience->other_expertise) {
                $completedCriteria++;
            }
            if ($experience->other_support_offer) {
                $completedCriteria++;
            }
            if ($experience->designation) {
                $completedCriteria++;
            }
            if ($experience->company_name) {
                $completedCriteria++;
            }
            if ($experience->experience_from) {
                $completedCriteria++;
            }
            if ($experience->experience_to) {
                $completedCriteria++;
            }
        }

        return ($completedCriteria / $totalCriteria) * 100;
    }
}
