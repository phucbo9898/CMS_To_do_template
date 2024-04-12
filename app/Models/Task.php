<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $table = 'tasks';
    protected $guarded = [];
    const ACTIVE = [
        'number' => 1,
        'name' => 'Active'
    ];
    const INACTIVE = [
        'number' => 2,
        'name' => 'Inactive'
    ];

    const OPEN = [
        'name' => 'Open',
        'status' => 1
    ];
    const IN_PROGRESS = [
        'name' => 'In progress',
        'status' => 2
    ];
    const RESOLVE = [
        'name' => 'Resolve',
        'status' => 3
    ];
    const CLOSE = [
        'name' => 'Close',
        'status' => 4
    ];

    const DONE = [
        'name' => 'Done',
        'status' => 5
    ];

    const HIGH_PRIORITY = [
        'name' => 'High Priority',
        'status' => 1
    ];

    const NORMAL_PRIORITY = [
        'name' => 'Normal Priority',
        'status' => 2
    ];

    const LOW_PRIORITY = [
        'name' => 'Low Priority',
        'status' => 3
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function subTasks()
    {
        return $this->hasMany(SubTask::class, 'parent_id', 'id');
    }
}
