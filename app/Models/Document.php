<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'project_id',   // legacy support
        'type',
        'file_name',
        'file_path',
        'mime_type',
        'size',
        'description',
        'tags',
        'uploaded_by',
        'meta',
    ];

    protected $casts = [
        'tags' => 'array',
        'meta' => 'array',
    ];

    // Human readable size
    public function getHumanSizeAttribute()
    {
        $bytes = (int) $this->size;
        if ($bytes === 0) return '0 B';
        $units = ['B','KB','MB','GB','TB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }

    // Accessor: full public URL (storage disk 'public')
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    public function entity()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
