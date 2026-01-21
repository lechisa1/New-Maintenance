<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IssueType extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
        protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'name',
        'description',
        'slug',
        'is_active',
        'is_need_approval'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_need_approval' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($issueType) {
            if (empty($issueType->slug)) {
                $issueType->slug = Str::slug($issueType->name);
            }
        });

        static::updating(function ($issueType) {
            if ($issueType->isDirty('name')) {
                $issueType->slug = Str::slug($issueType->name);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}