<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;          // ← ADD
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',            // already present
        'role',              // ← ADD
        'heads_department',  // ← ADD
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // UNCHANGED
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    // UNCHANGED
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    // ← ADD — required by LeaveComment model and comment thread component
    public function leaveComments(): HasMany
    {
        return $this->hasMany(LeaveComment::class);
    }

    // =========================================================================
    // ROLE HELPERS 
    // =========================================================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAssessor(): bool
    {
        return $this->role === 'assessor';
    }

    public function isManagingPartner(): bool
    {
        return $this->role === 'managing_partner';
    }

    public function isRegularUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Can this user assess the given leave request?
     *
     * Logic:
     *  - Admin      → can assess everything
     *  - MP         → assesses only HOD (assessor-role) applications
     *  - HOD        → assesses their own department, never peers or superiors
     *  - Everyone else → cannot assess
     */
    public function canAssess(\App\Models\LeaveRequest $request): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isManagingPartner()) {
            return $request->employee?->user?->role === 'assessor';
        }

        if ($this->isAssessor()) {
            $applicantRole = $request->employee?->user?->role;
            if (in_array($applicantRole, ['assessor', 'managing_partner', 'admin'])) {
                return false;
            }
            return $request->employee?->department === $this->heads_department;
        }

        return false;
    }

    // =========================================================================
    // GENDER HELPERS 
    // =========================================================================

    public function isMale(): bool
    {
        return $this->gender === 'male';
    }

    public function isFemale(): bool
    {
        return $this->gender === 'female';
    }

    // =========================================================================
    // EMPLOYEE STATUS HELPERS 
    // =========================================================================

    public function isApprovedEmployee(): bool
    {
        return $this->employee && $this->employee->status === 'active';
    }

    public function isPendingApproval(): bool
    {
        return !$this->employee || $this->employee->status !== 'active';
    }

    public function getProfileImageUrl(): ?string
    {
        if ($this->employee && $this->employee->profile_image) {
            $path = 'storage/' . $this->employee->profile_image;
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }
        return null;
    }
}
