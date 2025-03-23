<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DateTimeInterface;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Verified;
use App\Notifications\VerifyUserNotification;
use App\Models\ChatRoom;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use SoftDeletes, Notifiable, HasFactory;

    public $table = 'users';

    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $dates = [
        'email_verified_at',
        'verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'first_name', // Updated from 'name'
        'last_name', // Added
        'email',
        'email_verified_at',
        'password',
        'profile_picture', // Added
        'role', // Added (admin, contributor, supervisor)
        'verified',
        'verified_at',
        'remember_token',
        'city_id', // Updated from 'villes_id'
        'solde',
        'completed',
        'created_at',
        'updated_at',
        'deleted_at',

        // Supervisor-specific attributes
        'account_status', // Added
        'CNI', // Added
        'city', // Added
        'organisation', // Added
        'region', // Added
        'organisation_id_card_recto', // Added
        'organisation_id_card_verso', // Added

        // Contributor-specific attributes
        'phone_number', // Added
        'username', // Added
        'credibility_score', // Added
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'verified' => 'boolean',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Get all signals created by the user.
     */
    public function signals()
    {
        return $this->hasMany(Signal::class, 'created_by');
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Check if the user is a contributor.
     */
    public function isContributor()
    {
        return $this->role === 'contributor';
    }

    /**
     * Check if the user is a supervisor.
     */
    public function isSupervisor()
    {
        return $this->role === 'supervisor';
    }

    /**
     * Automatically handle user creation events.
     */
   /* public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        self::created(function (self $user) {
            if (auth()->check()) {
                $user->verified    = 1;
                $user->verified_at = Carbon::now()->format(config('panel.date_format') . ' ' . config('panel.time_format'));
                $user->save();
            } elseif (! $user->verification_token) {
                $token     = Str::random(64);
                $usedToken = self::where('verification_token', $token)->first();

                while ($usedToken) {
                    $token     = Str::random(64);
                    $usedToken = self::where('verification_token', $token)->first();
                }

                $user->verification_token = $token;
                $user->save();

                $registrationRole = config('panel.registration_default_role');
                if (! $user->roles()->get()->contains($registrationRole)) {
                    $user->roles()->attach($registrationRole);
                }

                $user->notify(new VerifyUserNotification($user));
            }
        });
    }*/

    /**
     * Format email_verified_at attribute.
     */
    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    /**
     * Hash the password before saving.
     */
    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    /**
     * Send password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Format verified_at attribute.
     */
    public function getVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    /**
     * Set verified_at attribute.
     */
    public function setVerifiedAtAttribute($value)
    {
        $this->attributes['verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }
    public function markEmailAsVerified()
    {
        if (!$this->hasVerifiedEmail()) {
            $this->forceFill([
                'email_verified_at' => $this->freshTimestamp(),
                'verified' => 1,
            ])->save();
        }
    }

    public function getEmailForVerification()
    {
        return $this->email;
    }

    /**
     * Relationship with Role model.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Relationship with City model.
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id'); // Updated from 'villes_id'
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyUserNotification($this));
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Add these relationships for chat functionality
    public function chatRooms(): BelongsToMany
    {
        return $this->belongsToMany(ChatRoom::class, 'chat_participants')
                    ->withPivot(['role', 'joined_at'])
                    ->withTimestamps();
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Get the user's region subscriptions.
     */
    public function regionSubscriptions()
    {
        return $this->hasMany(RegionSubscription::class);
    }

    /**
     * Subscribe to a region.
     */
    public function subscribeToRegion(string $region, bool $emailNotifications = true, bool $pushNotifications = true)
    {
        return $this->regionSubscriptions()->create([
            'region' => $region,
            'email_notifications' => $emailNotifications,
            'push_notifications' => $pushNotifications,
        ]);
    }

    /**
     * Unsubscribe from a region.
     */
    public function unsubscribeFromRegion(string $region)
    {
        return $this->regionSubscriptions()->where('region', $region)->delete();
    }

    /**
     * Check if user is subscribed to a region.
     */
    public function isSubscribedToRegion(string $region): bool
    {
        return $this->regionSubscriptions()->where('region', $region)->exists();
    }

    /**
     * Get the user's collections.
     */
    public function collections()
    {
        return $this->hasMany(Collecte::class, 'user_id');
    }

    /**
     * Get the user's contributions.
     */
    public function contributions()
    {
        return $this->belongsToMany(Collecte::class, 'collecte_contributor')
                    ->withTimestamps();
    }
}