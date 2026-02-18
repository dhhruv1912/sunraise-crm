<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PanelAttachment extends Model
{
    protected $fillable = [
        'batch_id',
        'panel_id',
        'path',
        'type',
        'original_filename',
        'ocr_text',
        'structured_data',
        'uploaded_by',
    ];

    protected $casts = [
        'structured_data' => 'array',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function panel()
    {
        return $this->belongsTo(Panel::class, 'panel_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
