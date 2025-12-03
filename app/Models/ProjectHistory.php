<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectHistory extends Model
{
    use HasFactory;

    protected $table = 'project_history';

    protected $fillable = [
        'project_id',
        'status',
        'changed_by',
        'notes',
    ];

    /**
     * Log message templates
     * (Merged from your old model)
     */
    public const MESSAGES = [
        'new_project'       => 'New Project Is Added',
        'change_assignee'   => '{USER} changed assignee to {TO} from {FROM}',
        'change_reporter'   => '{USER} changed reporter to {TO} from {FROM}',
        'change_status'     => '{USER} changed status to {TO} from {FROM}',
        'change_of_field'   => '{USER} updated field "{FIELD}" to {TO} from {FROM}',
        'change_of_doc'     => '{USER} updated project document "{FIELD}"',
        'note_change'       => '{USER} updated note from: {FROM} to: {TO}',
    ];

    /**
     * Relationship: Project
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Relationship: User who changed something
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Accessor: formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d M Y h:i A');
    }

    /**
     * Accessor: time ago
     */
    public function getAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }


    public function changedBy() {
        return $this->belongsTo(\App\Models\User::class, 'changed_by');
    }
}
