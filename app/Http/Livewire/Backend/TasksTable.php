<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Auth\Models\User;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Traits\WithSearch;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

/**
 * Class UsersTable.
 */
class TasksTable extends DataTableComponent
{
    use WithSearch;
    public bool $searchEnabled = true;
    public array $perPageOptions = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100];
    public int $perPage = 10;
    public $customSearch;
    public $searchStatus;
    public $search;
    public $activeTask;
    public bool $showPagination = true;
    public bool $paginationEnabled = true;

    const TASK_SEARCH_FROM_STATUS = 'task_search_from_status';
    const TASK_IS_ACTIVE = 'task_is_active';
    const TASK_SEARCH_FROM_SEARCH = 'task_search_from_search';
    const TASK_PER_PAGE= 'task_per_page';
    const TASK_PAGE_NUMBER = 'task_page_number';
    const SELECTED = 'selected';
    const ACTIVE = [
        'name' => 'Active',
        'number' => 1
    ];
    const INACTIVE = [
        'name' => 'Inactive',
        'number' => 2
    ];

    public function __construct()
    {
        $this->selectedType = '';
        $this->selectedOpenTask = '';
        $this->selectedInProgressTask = '';
        $this->selectedResolveTask = '';
        $this->selectedCloseTask = '';
        $this->selectedTaskName = __('Choose type');
        $this->selectedOpenTaskName = __(Task::OPEN['name']);
        $this->selectedInProgressTaskName = __(Task::IN_PROGRESS['name']);
        $this->selectedResolveTaskName = __(Task::RESOLVE['name']);
        $this->selectedCloseTaskName = __(Task::CLOSE['name']);
        $this->activeStatus = '';
        $this->inactiveStatus = '';
    }

    public function setPageNumber()
    {
        $key = self::TASK_PAGE_NUMBER;
        Session::forget($key);
        Session::save();
        Session::push($key, $this->page);
    }

    public function renderTypeInTask($value)
    {
        $this->resetPage();
        $this->searchStatus = $value;
        $key = self::TASK_SEARCH_FROM_STATUS;
        Session::forget($key);
        Session::forget(self::TASK_PAGE_NUMBER);
        Session::save();
        Session::push($key, $value);
    }

    public function renderActiveInTask($value)
    {
        $this->resetPage();
        $this->activeTask = $value;
        $key = self::TASK_IS_ACTIVE;
        Session::forget($key);
        Session::forget(self::TASK_PAGE_NUMBER);
        Session::save();
        Session::push($key, $value);
    }

    public function renderPageBySearch()
    {
        Session::forget(self::TASK_PAGE_NUMBER);
        Session::save();
    }

    public function renderPerPage($value)
    {
        $this->resetPage();
        $this->perPage = $value;
        $key = self::TASK_PER_PAGE;
        Session::forget($key);
        Session::forget(self::TASK_PAGE_NUMBER);
        Session::save();
        Session::push($key, $value);
    }

    public function mount(): void
    {
        if (Session::has(self::TASK_SEARCH_FROM_STATUS)) {
            $this->searchStatus = Session::get(self::TASK_SEARCH_FROM_STATUS);
        }

        if (Session::has(self::TASK_IS_ACTIVE)) {
            $this->activeTask = Session::get(self::TASK_IS_ACTIVE);
        }

        if (Session::has(self::TASK_SEARCH_FROM_SEARCH)) {
            $this->search = Session::get(self::TASK_SEARCH_FROM_SEARCH);
        }

        if (Session::has(self::TASK_PER_PAGE)) {
            $this->perPage = Session::get(self::TASK_PER_PAGE);
        } else {
            $this->perPage = 10;
        }

        if (Session::has(self::TASK_PER_PAGE)) {
            $this->page = Session::get(self::TASK_PER_PAGE);
        } else {
            $this->page = 1;
        }
    }

    public function updated($name, $value)
    {
        $key = 'task_search_form_' . $name;
        Session::forget($key);
        Session::save();
        Session::push($key, $value);
    }

    /**
     * @return Builder
     */
    public function query(): Builder
    {
        $status = '';
        if (Session::has(self::TASK_IS_ACTIVE)) {
            $active = Session::get(self::TASK_IS_ACTIVE);
            if ($active[0] == self::ACTIVE['number']) {
                $this->activeStatus = self::SELECTED;
                $this->inactiveStatus = '';
                $status = '';
            } else {
                $this->activeStatus = '';
                $this->inactiveStatus = self::SELECTED;
                $status = '';
            }
        } else {
            $status = self::SELECTED;
            $this->activeStatus = '';
            $this->inactiveStatus = '';
        }
        if (Session::has(self::TASK_SEARCH_FROM_STATUS)) {
            $statusTask = Session::get(self::TASK_SEARCH_FROM_STATUS);
            if ($statusTask == Task::OPEN['status']) {
                $this->selectedType = '';
                $this->selectedOpenTask = self::SELECTED;
                $this->selectedInProgressTask = '';
                $this->selectedResolveTask = '';
                $this->selectedCloseTask = '';
            }

            if ($statusTask == Task::IN_PROGRESS['status']) {
                $this->selectedType = '';
                $this->selectedOpenTask = '';
                $this->selectedInProgressTask = self::SELECTED;
                $this->selectedResolveTask = '';
                $this->selectedCloseTask = '';
            }

            if ($statusTask == Task::RESOLVE['status']) {
                $this->selectedType = '';
                $this->selectedOpenTask = '';
                $this->selectedInProgressTask = '';
                $this->selectedResolveTask = self::SELECTED;
                $this->selectedCloseTask = '';
            }

            if ($statusTask == Task::CLOSE['status']) {
                $this->selectedType = '';
                $this->selectedOpenTask = '';
                $this->selectedInProgressTask = '';
                $this->selectedResolveTask = '';
                $this->selectedCloseTask = self::SELECTED;
            }

            if ($statusTask == 0) {
                $this->selectedType = self::SELECTED;
                $this->selectedOpenTask = '';
                $this->selectedInProgressTask = '';
                $this->selectedResolveTask = '';
                $this->selectedCloseTask = '';
            }
        }

        $this->customSearch = '<div class="col-md-3 mr-2">
                <label for="keyword" class="mb-2">'.__('Keyword Search').'</label>
                <input wire:model.debounce.350ms="search" wire:change="renderPageBySearch()" class="form-control" type="text" placeholder="' . __('Search by keyword') . '">
            </div>';
        $this->customSearch .= '<div class="col-md-3 mr-2">
                <label for="status_task" class="mb-2">'.__('Status Task').'</label>
                <select name="search_type" class="form-control" wire:change="renderTypeInTask($event.target.value)">
                    <option value="0" '.$this->selectedType.'>'.$this->selectedTaskName.'</option>
                    <option value="'.Task::OPEN['status'].'" '.$this->selectedOpenTask.'>'.$this->selectedOpenTaskName.'</option>
                    <option value="'.Task::IN_PROGRESS['status'].'" '.$this->selectedInProgressTask.'>'.$this->selectedInProgressTaskName.'</option>
                    <option value="'.Task::RESOLVE['status'].'" '.$this->selectedResolveTask.'>'.$this->selectedResolveTaskName.'</option>
                    <option value="'.Task::CLOSE['status'].'" '.$this->selectedCloseTask.'>'.$this->selectedCloseTaskName.'</option>
                </select>
            </div>';

        $query = Task::select('tasks.*', 'users.name as assignee')
            ->leftJoin('users', 'users.id', '=', 'tasks.assignee_id')
            ->with([
                'subTasks' => function ($q) {
                    $q->select('sub_tasks.*')->with([
                        'task' => function ($q1) {
                            return $q1->select('tasks.*', 'users.name as assignee')
                                ->leftJoin('users', 'users.id', '=', 'tasks.assignee_id')
                                ->where('tasks.active', Task::ACTIVE['number'])
                                ->whereNotNull('parent_id');
                        }
                    ]);
                }
            ])
            ->where('tasks.active', Task::ACTIVE['number'])
            ->whereNull('parent_id')
        ;

        if (!empty($this->searchStatus) && $this->searchStatus[0] != "0") {
            $query = $query->where('tasks.status', $this->searchStatus);
        }
        if (!empty($this->search)) {
            $keyWord = $this->search;
            $query = $query->where('tasks.name', 'like', '%' . $keyWord . '%');
        }
        return $query;
    }

    /**
     * @return array
     */
    public function columns(): array
    {
        return [
            Column::make(__('ID'), 'id')
                ->sortable(),
            Column::make(__('Name Task'), 'name')
                ->sortable()
                ->searchable(),
            Column::make(__('Assignee'))
                ->sortable(),
            Column::make(__('Task Type'), 'status')
                ->sortable(),
            Column::make(__('Category'), 'category')
                ->sortable(),
            Column::make(__('Priority'), 'priority')
                ->sortable(),
            Column::make(__('Total Subtask'), 'total_sub_task')
                ->sortable(),
            Column::make(__('Start Due Date'), 'start_due_date'),
            Column::make(__('Action')),
        ];
    }

    public function rowView(): string
    {
        return 'to_do.includes.row';
    }
}
