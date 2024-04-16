<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskLog extends Model
{
    use HasFactory;
    protected $table = 'task_logs';
    protected $guarded = [];
    const CHANGE_ASSIGNEE = 1;
    const CHANGE_DATE = 2;
    const CHANGE_CATEGORY = 3;
    const CHANGE_PRIORITY = 4;
    const CHANGE_STATUS = 5;
}
