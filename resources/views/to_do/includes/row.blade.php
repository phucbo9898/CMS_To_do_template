@inject('model','\App\Models\Task')
<style>
    .border {
        border-radius: 10px;
    }
</style>
<x-livewire-tables::bs4.table.cell>
    {{ $row->id }}
</x-livewire-tables::bs4.table.cell>

<x-livewire-tables::bs4.table.cell>
    {{ $row->name }}
</x-livewire-tables::bs4.table.cell>

<x-livewire-tables::bs4.table.cell>
    {{ $row->assignee }}
</x-livewire-tables::bs4.table.cell>

<x-livewire-tables::bs4.table.cell>
    @switch($row->status)
        @case($model::OPEN['status'])
            <span class="badge badge-danger">{{ $model::OPEN['name'] }}</span>
            @break
        @case($model::IN_PROGRESS['status'])
            <span class="badge badge-primary">{{ $model::IN_PROGRESS['name'] }}</span>
            @break
        @case($model::RESOLVE['status'])
            <span class="badge badge-success">{{ $model::RESOLVE['name'] }}</span>
            @break
        @default
            <span class="badge badge-dark">{{ $model::CLOSE['name'] }}</span>
    @endswitch
</x-livewire-tables::bs4.table.cell>

<x-livewire-tables::bs4.table.cell>
    @if($row->category == 'CMS')
        <span class="badge badge-warning">CMS</span>
    @else
        <span class="badge badge-info">FE</span>
    @endif
</x-livewire-tables::bs4.table.cell>

<x-livewire-tables::bs4.table.cell>
    @if($row->priority == $model::HIGH_PRIORITY['status'])
        <i class="cil-arrow-top font-weight-bold" style="color: #e55353; font-size: 17px;"></i>
        <span class="badge badge-danger">{{ $model::HIGH_PRIORITY['name'] }}</span>
    @elseif($row->priority == $model::NORMAL_PRIORITY['status'])
        <i class="cil-arrow-right font-weight-bold" style="color: #321fdb; font-size: 17px;"></i>
        <span class="badge badge-primary">{{ $model::NORMAL_PRIORITY['name'] }}</span>
    @else
        <i class="cil-arrow-bottom font-weight-bold" style="color: #2eb85c; font-size: 17px;"></i>
        <span class="badge badge-success">{{ $model::LOW_PRIORITY['name'] }}</span>
    @endif
</x-livewire-tables::bs4.table.cell>

<x-livewire-tables::bs4.table.cell>
    <a href="#" data-toggle="modal" class="modal-subtask" data-target="#exampleModalSubtask-{{$row->id}}">{{ $row->total_sub_task }}</a>
</x-livewire-tables::bs4.table.cell>

<x-livewire-tables::bs4.table.cell>
    {{ $row->start_due_date }}
</x-livewire-tables::bs4.table.cell>

<x-livewire-tables::bs4.table.cell>
    @include('to_do.includes.actions', ['task' => $row])
</x-livewire-tables::bs4.table.cell>

<!-- Start Modal -->
<div class="modal fade" id="exampleModalSubtask-{{$row->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Sub Task by <b class="text-primary">{{ $row->name }}</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if(count($row->subTasks))
                    @foreach($row->subTasks as $subTask)
                        <div class="sub-task-{{ $subTask->id }} p-3 border border-info mb-2">
                            <h3 class="font-weight-bold text-center">
                                <a href="{{ route('admin.to-do-list.view-sub-task', [$row->id, $subTask->id]) }}">{{ $subTask->name }}</a>
                            </h3>
                            <div class="information-subtask">
                                <div class="assignee">
                                    <span>@lang('Assignee: '){{ $subTask->assignee }}</span>
                                </div>
                                <div class="task-type">
                                    <span>@lang('Status: ')</span>
                                    @switch($subTask->status)
                                        @case($model::OPEN['status'])
                                            <span class="badge badge-danger">{{ $model::OPEN['name'] }}</span>
                                            @break
                                        @case($model::IN_PROGRESS['status'])
                                            <span class="badge badge-primary">{{ $model::IN_PROGRESS['name'] }}</span>
                                            @break
                                        @case($model::RESOLVE['status'])
                                            <span class="badge badge-success">{{ $model::RESOLVE['name'] }}</span>
                                            @break
                                        @default
                                            <span class="badge badge-dark">{{ $model::CLOSE['name'] }}</span>
                                    @endswitch
                                </div>
                                <div class="category">
                                    <span>@lang('Category: ')</span>
                                    @if($subTask->category == 'CMS')
                                        <span class="badge badge-warning">CMS</span>
                                    @else
                                        <span class="badge badge-info">FE</span>
                                    @endif
                                </div>
                                <div class="priority">
                                    <span>@lang('Priority: ')</span>
                                    @if($subTask->priority == $model::HIGH_PRIORITY['status'])
                                        <span class="badge badge-danger">{{ $model::HIGH_PRIORITY['name'] }}</span>
                                    @elseif($subTask->priority == $model::NORMAL_PRIORITY['status'])
                                        <span class="badge badge-primary">{{ $model::NORMAL_PRIORITY['name'] }}</span>
                                    @else
                                        <span class="badge badge-success">{{ $model::LOW_PRIORITY['name'] }}</span>
                                    @endif
                                </div>
                                <div class="start-due-date">@lang('Date: '){{ $subTask->start_due_date }}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div><b class="text-primary">{{ $row->name }}</b> not have sub task</div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End Modal -->
