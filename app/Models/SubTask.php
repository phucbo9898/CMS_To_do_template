<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTask extends Model
{
    use HasFactory;
    protected $table = 'sub_tasks';
    protected $guarded = [];

    public function task()
    {
        return $this->belongsTo(Task::class, 'sub_task_id', 'id');
    }
}
