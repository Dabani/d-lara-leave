<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get all employees in the department
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'department', 'name');
    }

    /**
     * Get active employees in the department
     */
    public function activeEmployees(): HasMany
    {
        return $this->hasMany(Employee::class, 'department', 'name')
            ->where('status', 'active');
    }

    /**
     * Get total employees count
     */
    public function getTotalEmployeesAttribute(): int
    {
        return $this->employees()->count();
    }
}
