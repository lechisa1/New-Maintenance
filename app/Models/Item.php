<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Item extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
      
        'name',
        'type',
        'unit',
        'status'
    ];
        protected $keyType = 'string';
    public $incrementing = false;

    // Constants for status
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_MAINTENANCE = 'maintenance';

    // Constants for type
    const TYPE_COMPUTER = 'computer';
    const TYPE_PRINTER = 'printer';
    const TYPE_AIRCON = 'aircon';
    const TYPE_FURNITURE = 'furniture';
    const TYPE_ELECTRICAL = 'electrical';
    const TYPE_PLUMBING = 'plumbing';
    const TYPE_VEHICLE = 'vehicle';
    const TYPE_OTHER = 'other';

    // Constants for unit
    const UNIT_UNIT = 'unit';
    const UNIT_PIECE = 'piece';
    const UNIT_SET = 'set';
    const UNIT_SYSTEM = 'system';
    const UNIT_DEVICE = 'device';

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
           
        ];
    }

    /**
     * Get type options
     */
    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_COMPUTER => 'Computer',
            self::TYPE_PRINTER => 'Printer',
            self::TYPE_AIRCON => 'Air Conditioner',
            self::TYPE_FURNITURE => 'Furniture',
            self::TYPE_ELECTRICAL => 'Electrical Equipment',
            self::TYPE_PLUMBING => 'Plumbing Fixture',
            self::TYPE_VEHICLE => 'Vehicle',
            self::TYPE_OTHER => 'Other',
        ];
    }

    /**
     * Get unit options
     */
    public static function getUnitOptions(): array
    {
        return [
            self::UNIT_UNIT => 'Unit',
            self::UNIT_PIECE => 'Piece',
            self::UNIT_SET => 'Set',
            self::UNIT_SYSTEM => 'System',
            self::UNIT_DEVICE => 'Device',
        ];
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            self::STATUS_INACTIVE => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
            self::STATUS_MAINTENANCE => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }

    /**
     * Get status text
     */
    public function getStatusText(): string
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    /**
     * Get type text
     */
    public function getTypeText(): string
    {
        return self::getTypeOptions()[$this->type] ?? $this->type;
    }

    /**
     * Get unit text
     */
    public function getUnitText(): string
    {
        return self::getUnitOptions()[$this->unit] ?? $this->unit;
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for inactive items
     */
    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Scope for maintenance items
     */
    public function scopeMaintenance($query)
    {
        return $query->where('status', self::STATUS_MAINTENANCE);
    }

    /**
     * Scope by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}