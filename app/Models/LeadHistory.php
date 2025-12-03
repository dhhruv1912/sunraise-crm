<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadHistory extends Model
{
    use HasFactory;

    protected $table = 'lead_history';

    protected $fillable = [
        'lead_id',
        'changed_by',
        'action',
        'old_value',
        'new_value',
        'message',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    /**
     * Lead relationship
     * (NO foreign key constraints, just logical)
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    /**
     * User who changed something
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Accessor: human readable "time ago"
     */
    public function getAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
