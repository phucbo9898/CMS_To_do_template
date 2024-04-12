<?php

namespace App\Http\Controllers;

use App\Domains\Auth\Models\User;
use App\Models\SubTask;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

            if (!empty($request->parent_id)) {
                SubTask::create([
                    'assignee_id' => Auth::id(),
                    'name' => $request->name_task,
                    'status' =>Task::OPEN['status'],
                    'content' => $request->content_task,
                    'parent_id' => $request->parent_id,
                    'category' => $request->category,
                    'total_sub_task' => 0,
                    'priority' => $request->priority,
                    'created_by' => Auth::id(),
                    'active' => $request->active ?? 0,
                    'start_due_date' => $start_due_date
                ]);

                $taskParent = Task::where('id', $request->parent_id)->first();
                Task::where('id', $request->parent_id)->update(['total_sub_task' => $taskParent->total_sub_task + 1]);
            } else {
                Task::create([
                    'assignee_id' => Auth::id(),
                    'name' => $request->name_task,
                    'status' =>Task::OPEN['status'],
                    'content' => $request->content_task,
                    'category' => $request->category,
                    'total_sub_task' => 0,
                    'priority' => $request->priority,
                    'created_by' => Auth::id(),
                    'active' => $request->active ?? 0,
                    'start_due_date' => $start_due_date
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
        $taskDetail = Task::where('id', $id)->first();
        $assignee = User::where('id', $taskDetail->user_id)->first();
        if (empty($taskDetail)) {
            return redirect()->back()->withInput()->withFlashDanger('Task not found');
        }
        $taskDetail['assignee'] = $assignee ? $assignee->name : '';
        $start_due_date = $taskDetail->start_due_date;
        $endDate = substr($start_due_date, strpos($start_due_date, '-') + 1);
        $startDate = str_replace('-' . $endDate, '', $start_due_date);
        $taskDetail['start_date'] = Carbon::parse($startDate)->format('Y-m-d');
        $taskDetail['end_date'] = Carbon::parse($endDate)->format('Y-m-d');
        return view('to_do.task-detail', ['taskDetail' => $taskDetail, 'lstUser' => $lstUser]);
    }

    public function viewSubTask($id, $subTaskId)
    {
        $lstUser = User::all();
        $taskDetail = SubTask::where('id', $id)->first();
        $assignee = User::where('id', $taskDetail->user_id)->first();
        if (empty($taskDetail)) {
            return redirect()->back()->withInput()->withFlashDanger('Task not found');
        }
        $taskDetail['assignee'] = $assignee ? $assignee->name : '';
        $start_due_date = $taskDetail->start_due_date;
        $endDate = substr($start_due_date, strpos($start_due_date, '-') + 1);
        $startDate = str_replace('-' . $endDate, '', $start_due_date);
        $taskDetail['start_date'] = Carbon::parse($startDate)->format('Y-m-d');
        $taskDetail['end_date'] = Carbon::parse($endDate)->format('Y-m-d');
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

    public function addComment()
    {
        return view('to_do.setting');
    }
}
