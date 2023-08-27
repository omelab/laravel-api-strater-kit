<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Spatie\Permission\Models\Permission as SpatiePermission; 

class Permission extends SpatiePermission
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'module_id',
        'name', 
        'guard_name', 
        'created_at', 
        'updated_at', 
    ];
}
