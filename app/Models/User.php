<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    // Helper method to check gender
    public function isMale(): bool
    {
        return $this->gender === 'male';
    }

    public function isFemale(): bool
    {
        return $this->gender === 'female';
    }

    /**
     * Check if user has approved employee record
     */
    public function isApprovedEmployee(): bool
    {
        return $this->employee && $this->employee->status === 'active';
    }

    /**
     * Check if user is pending approval
     */
    public function isPendingApproval(): bool
    {
        return !$this->employee || $this->employee->status !== 'active';
    }

    /**
     * Get profile image URL with fallback
     */
    public function getProfileImageUrl(): ?string
    {
        if ($this->employee && $this->employee->profile_image) {
            $path = 'storage/' . $this->employee->profile_image;
            // Check if file exists in public directory
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }
        return null;
    }
}
