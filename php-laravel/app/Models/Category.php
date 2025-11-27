<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';
    
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'description',
        'color_hex'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    /**
     * Get all tools in this category
     */
    public function tools(): HasMany
    {
        return $this->hasMany(Tool::class, 'category_id');
    }
}
