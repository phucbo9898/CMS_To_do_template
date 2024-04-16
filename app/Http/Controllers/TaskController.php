<?php

namespace App\Http\Controllers;

use App\Domains\Auth\Models\User;
use App\Models\CommentTask;
use App\Models\SubTask;
use App\Models\Task;
use App\Models\TaskLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index()
    {
        return view('to_do.list');
    }

    public function create()
    {
        $lstUser = User::where('active', User::ACTIVE)->get();
        $lstTaskParent = Task::get();
        return view('to_do.create', ['lstUser' => $lstUser, 'lstTaskParent' => $lstTaskParent]);
    }

    public function store(Request $request)
    {
        try {
            $start_due_date = '';
            $startDate = Carbon::parse($request->get('start_date'))->format('Y/m/d');
            $dueDate = Carbon::parse($request->get('due_date'))->format('Y/m/d');
            if ($startDate > $dueDate) {
                return redirect()->back()->withInput()->withFlashDanger('Time invalid');
            } else if ($startDate == $dueDate) {
                $start_due_date = $startDate;
            } else {
                $start_due_date = $startDate . '-' . $dueDate;
            }

            $taskCreate = Task::create([
                'assignee_id' => Auth::id(),
                'name' => $request->name_task,
                'status' => Task::OPEN['status'],
                'content' => $request->content_task,
                'parent_id' => !empty($request->parent_id) ? $request->parent_id : null,
                'category' => $request->category,
                'total_sub_task' => 0,
                'priority' => $request->priority,
                'created_by' => Auth::id(),
                'active' => $request->active ?? 0,
                'start_due_date' => $start_due_date,
            ]);
            $taskParent = Task::where('id', $request->parent_id)->first();
            if (!empty($taskParent)) {
                Task::where('id', $request->parent_id)->update(['total_sub_task' => $taskParent->total_sub_task + 1]);
                SubTask::create([
                    'task_parent_id' => $request->parent_id,
                    'sub_task_id' => $taskCreate->id,
                ]);
            }

            return redirect()->route('admin.to-do-list.index')->withFlashSuccess('Create task success');
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return redirect()->back()->withInput()->withFlashDanger('Create task failed');
        }
    }

    public function show(Request $request, $id)
    {
        $lstUser = User::all();
        $taskDetail = Task::where('id', $id)->with([
            'comments' => function ($query) {
                return $query->select(['comment_tasks.*'])->with([
                    'userComment' => function ($subQuery1) {
                        return $subQuery1->select('users.id', 'users.name', 'users.avatar');
                    },
                    'commentLogs' => function ($subQuery2) {
                        return $subQuery2->select(['task_logs.*']);
                    }
                ]);
            }
        ])->first();
        if (empty($taskDetail)) {
            return redirect()->back()->withInput()->withFlashDanger('Task not found');
        }
        $assignee = User::where('id', $taskDetail->assignee_id)->where('active', 1)->first();
        $taskDetail['assignee'] = $assignee ? $assignee->name : '';
        $start_due_date = $taskDetail->start_due_date;
        $endDate = substr($start_due_date, strpos($start_due_date, '-') + 1);
        $startDate = str_replace('-' . $endDate, '', $start_due_date);
        $taskDetail['start_date'] = Carbon::parse($startDate)->format('Y-m-d');
        $taskDetail['due_date'] = Carbon::parse($endDate)->format('Y-m-d');

        // get owner task
        $userCreateTask = User::where('id', $taskDetail->created_by)->where('active', 1)->first();
        $taskDetail['user_create'] = $userCreateTask ?? [];

        $taskDetail['created_at'] = Carbon::parse($taskDetail->created_at ?? '')->tz($taskDetail->user_create->timezone)->format('M d, Y H:i:s');

        return view('to_do.task-detail', ['taskDetail' => $taskDetail, 'lstUser' => $lstUser]);
    }

    public function edit($id)
    {
        $lstUser = User::where('active', User::ACTIVE)->get();
        $task = Task::where('id', $id)->first();
        if (empty($task)) {
            return redirect()->route('to-do-list.index')->withFlashDanger('Task not found');
        }
        $start_due_date = $task->start_due_date;
        $endDate = substr($start_due_date, strpos($start_due_date, '-') + 1);
        $startDate = str_replace('-' . $endDate, '', $start_due_date);
        $task['start_date'] = Carbon::parse($startDate)->format('Y-m-d');
        $task['end_date'] = Carbon::parse($endDate)->format('Y-m-d');
        return view('to_do.edit', ['task' => $task, 'lstUser' => $lstUser]);
    }

    public function addComment(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $task = Task::where('id', $id)->first();
            if (empty($task)) {
                return redirect()->route('admin.to-do-list.index')->withFlashDanger('Task not found');
            }
            $start_due_date = $task->start_due_date;
            $endDate = substr($start_due_date, strpos($start_due_date, '-') + 1);
            $startDate = str_replace('-' . $endDate, '', $start_due_date);
            $start_date_by_task = Carbon::parse($startDate)->format('Y-m-d');
            $due_date_by_task = Carbon::parse($endDate)->format('Y-m-d');
            $addComment = $task->comments()->save(new CommentTask([
                'sender_id' => Auth::id(),
                'comment' => $request->content_task,
                'change_information' => ''
            ]));

            // Save log when change status
            if (!empty($request->change_status) && $request->change_status != $task->status) {
                $task->logs()->save(new TaskLog([
                    'type' => TaskLog::CHANGE_STATUS,
                    'comment_task_id' => $addComment->id,
                    'message' => Task::STATUS[$task->status],
                    'message_update' => Task::STATUS[$request->change_status],
                ]));
                $task->update([
                    'status' => $request->change_status
                ]);
            }

            // Save log when change assignee
            if (!empty($request->change_assignee) && $request->change_assignee != $task->assignee_id) {
                $assigneeOld = User::where('id', $task->assignee_id)->first();
                $assigneeNew = User::where('id', $request->change_assignee)->first();
                $task->logs()->save(new TaskLog([
                    'type' => TaskLog::CHANGE_ASSIGNEE,
                    'comment_task_id' => $addComment->id,
                    'message' => !empty($assigneeOld) ? $assigneeOld->name : '',
                    'message_update' => !empty($assigneeNew) ? $assigneeNew->name : '',
                ]));
                $task->update([
                    'assignee_id' => $request->change_assignee
                ]);
            }

            // Save log when change category task
            if (!empty($request->change_category) && strcmp($request->change_category, $task->category) !== 0) {
                $task->logs()->save(new TaskLog([
                    'type' => TaskLog::CHANGE_CATEGORY,
                    'comment_task_id' => $addComment->id,
                    'message' => $task->category,
                    'message_update' => $request->change_category,
                ]));
                $task->update([
                    'category' => $request->change_category
                ]);
            }

            // Save log when change priority task
            if (!empty($request->change_priority) && $request->change_priority != $task->priority) {
                $task->logs()->save(new TaskLog([
                    'type' => TaskLog::CHANGE_PRIORITY,
                    'comment_task_id' => $addComment->id,
                    'message' => $task->priority,
                    'message_update' => $request->change_priority,
                ]));
                $task->update([
                    'priority' => $request->change_priority
                ]);
            }

            // Save log when change start date
            if (strcmp($start_date_by_task, $request->change_start_date) !== 0) {
                $task->logs()->save(new TaskLog([
                    'type' => TaskLog::CHANGE_DATE,
                    'comment_task_id' => $addComment->id,
                    'message' => $start_date_by_task,
                    'message_update' => $request->change_start_date,
                ]));
            }

            // Save log when change due date
            if (strcmp($due_date_by_task, $request->change_due_date) !== 0) {
                $task->logs()->save(new TaskLog([
                    'type' => TaskLog::CHANGE_DATE,
                    'comment_task_id' => $addComment->id,
                    'message' => $due_date_by_task,
                    'message_update' => $request->change_due_date,
                ]));
            }

            if (strcmp($start_date_by_task, $request->change_start_date) !== 0 && strcmp($due_date_by_task, $request->change_due_date) === 0) {
                $task->update([
                    'start_due_date' => Carbon::parse($request->change_start_date)->format('Y/m/d') . '-' . Carbon::parse($due_date_by_task)->format('Y/m/d')
                ]);
            } elseif (strcmp($start_date_by_task, $request->change_start_date) === 0 && strcmp($due_date_by_task, $request->change_due_date) !== 0) {
                $task->update([
                    'start_due_date' => Carbon::parse($start_date_by_task)->format('Y/m/d') . '-' . Carbon::parse($request->change_due_date)->format('Y/m/d')
                ]);
            } else {
                $task->update([
                    'start_due_date' => Carbon::parse($request->change_start_date)->format('Y/m/d') . '-' . Carbon::parse($request->change_due_date)->format('Y/m/d')
                ]);
            }
            DB::commit();
            return redirect()->back()->withFlashSuccess('Add comment success');
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception->getMessage());
            return redirect()->back()->withFlashDanger('Add comment failed');
        }
    }
}
