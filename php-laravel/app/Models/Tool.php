<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tool extends Model
{
    protected $table = 'tools';
    
    protected $fillable = [
        'name',
        'description',
        'vendor',
        'website_url',
        'category_id',
        'monthly_cost',
        'active_users_count',
        'owner_department',
        'status'
    ];

    protected $casts = [
        'monthly_cost' => 'decimal:2',
        'active_users_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the category that owns the tool
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Scope for active tools only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for filtering by department
     */
    public function scopeDepartment($query, $department)
    {
        return $query->where('owner_department', $department);
    }

    /**
     * Scope for filtering by category
     */
    public function scopeCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for filtering by cost range
     */
    public function scopeCostBetween($query, $min, $max)
    {
        return $query->whereBetween('monthly_cost', [$min, $max]);
    }
}
