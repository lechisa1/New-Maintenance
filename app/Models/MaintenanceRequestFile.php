<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class MaintenanceRequestFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_request_id',
        'filename',
        'original_name',
        'mime_type',
        'path',
        'size',
            'type',
    'uploaded_by',
    ];

    /**
     * Get file size in human readable format
     */
    public function getFileSize(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }

    /**
     * Get file icon based on mime type
     */
    public function getFileIcon(): string
    {
        if (str_contains($this->mime_type, 'image')) {
            return 'bi-file-image';
        }
        if (str_contains($this->mime_type, 'pdf')) {
            return 'bi-file-pdf';
        }
        if (str_contains($this->mime_type, 'word') || str_contains($this->mime_type, 'document')) {
            return 'bi-file-word';
        }
        if (str_contains($this->mime_type, 'text')) {
            return 'bi-file-text';
        }
        if (str_contains($this->mime_type, 'excel') || str_contains($this->mime_type, 'spreadsheet')) {
            return 'bi-file-excel';
        }
        if (str_contains($this->mime_type, 'zip') || str_contains($this->mime_type, 'compressed')) {
            return 'bi-file-zip';
        }
        return 'bi-file';
    }

    /**
     * Relationship with maintenance request
     */
    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }
}