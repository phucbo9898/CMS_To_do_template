<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentTask extends Model
{
    use HasFactory;
    protected $table = 'comment_tasks';
    protected $guarded = [];

    public function userComment()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function commentLogs()
    {
        return $this->hasMany(TaskLog::class, 'comment_task_id', 'id');
    }
}
