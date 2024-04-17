@inject('task', '\App\Models\Task')

@extends('backend.layouts.app')

@section('title', __('View To Do detail'))

@section('content')
    <style>
        .task-comment {
            width: 600px !important;
            height: auto !important;
        }

        .task-comment > p > img {
            max-width: 100%;
            height: auto;
        }

        .task-content {
            width: 600px !important;
            height: auto !important;
        }

        .task-content > p > img {
            max-width: 100%;
            height: auto;
        }
    </style>

    <div class="d-flex justify-content-between">
        <h3 class="px-3">{{ $taskDetail->name }}</h3>
        <div>
            <x-utils.edit-button :href="route('admin.to-do-list.edit', $taskDetail->id)" />
        </div>
    </div>

    <x-backend.card>
        <x-slot name="header">
            <div class="infor-user d-flex">
                <div class="avatar">
                    <img src="{{ $taskDetail->user_create->avatar ?? '' }}" alt="" style="width: 50px; border-radius: 50%;">
                </div>
                <div class="infor ml-2">
                    <div class="font-weight-bold">{{ $taskDetail->user_create->name ?? '' }}</div>
                    <div>
                        <label style="color: silver;">@lang('Created') {{ $taskDetail->created_at }}</label>
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="body">
            <div class="task-assignee">
                <label for="">@lang('Assignee'):</label>
                <span>{{ $taskDetail->assignee }}</span>
            </div>
            <div class="task-content">
                {!! $taskDetail->content !!}
            </div>
            <!-- Start Modal -->
            <div class="task-content-modal"></div>
            <!-- End Modal -->
        </x-slot>
    </x-backend.card>

    <h3 class="px-3">@lang('Comments') ({{ count($taskDetail->comments) }})</h3>
    <x-backend.card>
        <x-slot name="body">
            @foreach($taskDetail->comments as $comment)
                <div class="infor-user d-flex">
                    <div class="avatar">
                        <img src="{{ $comment->userComment->avatar ?? '' }}" alt="" style="width: 50px; border-radius: 50%;">
                    </div>
                    <div class="infor ml-2">
                        <div class="font-weight-bold">{{ $comment->userComment->name ?? '' }}</div>
                        <div>
                            <label style="color: silver;">{{ \Carbon\Carbon::parse($comment->created_at ?? '')->tz('Asia/Ho_Chi_Minh')->format('M d,Y H:i:s') }}</label>
                        </div>
                    </div>
                </div>

                <div class="information-comment p-3">
                    <div class="information">
                        <ul style="font-size: 12px;">
                            @foreach($comment->commentLogs as $log)
                                @if($log->type === 5)
                                    <li>@lang('Change Status'): {{ $log->message }} <i class="cil-arrow-right font-weight-bold"></i> {{ $log->message_update }}</li>
                                @endif
                                @if($log->type === 1)
                                    <li>@lang('Change Assignee'): {{ $log->message }} <i class="cil-arrow-right font-weight-bold"></i> {{ $log->message_update }}</li>
                                @endif
                                @if($log->type === 3)
                                    <li>@lang('Change Category'): {{ $log->message }} <i class="cil-arrow-right font-weight-bold"></i> {{ $log->message_update }}</li>
                                @endif
                                @if($log->type === 4)
                                    <li>@lang('Change Priority'): {{ $task::PRIORITY[$log->message] }} <i class="cil-arrow-right font-weight-bold"></i> {{ $task::PRIORITY[$log->message_update] }}</li>
                                @endif
                                @if($log->type === 2)
                                    <li>@lang('Change date'): {{ $log->message }} <i class="cil-arrow-right font-weight-bold"></i> {{ $log->message_update }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <div class="task-comment">
                        {!! $comment->comment !!}
                    </div>
                </div>
                <hr>
                <!-- Start Modal -->
                <div class="modal-append"></div>
                <!-- End Modal -->
            @endforeach
        </x-slot>
    </x-backend.card>

    <h3>@lang('Add Comment')</h3>
    <x-backend.card>
        <x-slot name="body">
            <div class="p-3">
                <form action="{{ route('admin.to-do-list.add-comment', $taskDetail->id) }}" method="post">
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-9">
                            <textarea class="form-control" id="contentArea" placeholder="{{ __('Content') }}" name="content_task"></textarea>
                        </div>
                        <div class="col-md-3">
                            <div class="status-task mb-2">
                                <div class="label d-flex justify-content-between">
                                    <label for="">@lang('Status')</label>
                                    @switch($taskDetail->status + 1)
                                        @case($task::OPEN['status'])
                                            <span class="text-success change-status cursor-pointer" data-status="{{ $taskDetail->status }}" >Set to "{{ $task::OPEN['name'] }}"</span>
                                            @break
                                        @case($task::IN_PROGRESS['status'])
                                            <span class="text-success change-status cursor-pointer" data-status="{{ $taskDetail->status }}" >Set to "{{ $task::IN_PROGRESS['name'] }}"</span>
                                            @break
                                        @case($task::RESOLVE['status'])
                                            <span class="text-success change-status cursor-pointer" data-status="{{ $taskDetail->status }}" >Set to "{{ $task::RESOLVE['name'] }}"</span>
                                            @break
                                        @case($task::CLOSE['status'])
                                            <span class="text-success change-status cursor-pointer" data-status="{{ $taskDetail->status }}" >Set to "{{ $task::CLOSE['name'] }}"</span>
                                            @break
                                        @default
                                            <span class="text-success change-status cursor-pointer" data-status="{{ $taskDetail->status }}" >Set to "{{ $task::DONE['name'] }}"</span>
                                    @endswitch
                                </div>
                                <div class="option-status">
                                    <select name="change_status" class="form-control change-status">
                                        <option value="{{ $task::OPEN['status'] }}" {{ $taskDetail->status === $task::OPEN['status'] ? 'selected' : '' }}>@lang('Open')</option>
                                        <option value="{{ $task::IN_PROGRESS['status'] }}" {{ $taskDetail->status === $task::IN_PROGRESS['status'] ? 'selected' : '' }}>@lang('In progress')</option>
                                        <option value="{{ $task::RESOLVE['status'] }}" {{ $taskDetail->status === $task::RESOLVE['status'] ? 'selected' : '' }}>@lang('Resolve')</option>
                                        <option value="{{ $task::CLOSE['status'] }}" {{ $taskDetail->status === $task::CLOSE['status'] ? 'selected' : '' }}>@lang('Close')</option>
                                        <option value="{{ $task::DONE['status'] }}" {{ $taskDetail->status === $task::DONE['status'] ? 'selected' : '' }}>@lang('Done')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between">
                                        <label for="">@lang('Assignee')</label>
                                        <span class="text-success change-assignee" data-assignee="{{ $logged_in_user->id }}" style="cursor: pointer;">@lang('Set to "Me"')</span>
                                    </div>
                                    <div class="assignee-task">
                                        <select name="change_assignee" class="form-control assignee">
                                            @foreach($lstUser as $user)
                                                <option class="assignee-{{ $user->id }}" value="{{ $user->id }}" {{ $taskDetail->assignee_id === $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="title-start-date">
                                        <label for="">@lang('Start date')</label>
                                    </div>
                                    <div>
                                        <input type="date" name="change_start_date" class="form-control start-date" value="{{ old('start_date') ?? ($taskDetail->start_date ?? '')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between">
                                        <label for="">@lang('Category')</label>
                                    </div>
                                    <div class="category-task">
                                        <select name="change_category" class="form-control">
                                            <option value="CMS" {{ $taskDetail->category === 'CMS' ? 'selected' : '' }}>@lang('CMS')</option>
                                            <option value="FE" {{ $taskDetail->category === 'FE' ? 'selected' : '' }}>@lang('FE')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="title-start-date">
                                        <label for="">@lang('Due date')</label>
                                    </div>
                                    <div>
                                        <input type="date" name="change_due_date" class="form-control due-date" value="{{ old('due_date') ?? ($taskDetail->due_date ?? '')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between">
                                        <label for="">@lang('Priority')</label>
                                    </div>
                                    <div class="priority-task">
                                        <select name="change_priority" class="form-control">
                                            <option value="{{ $task::HIGH_PRIORITY['status'] }}" {{ $taskDetail->priority === $task::HIGH_PRIORITY['status'] ? 'selected' : '' }}>{{ $task::HIGH_PRIORITY['name'] }}</option>
                                            <option value="{{ $task::NORMAL_PRIORITY['status'] }}" {{ $taskDetail->priority === $task::NORMAL_PRIORITY['status'] ? 'selected' : '' }}>{{ $task::NORMAL_PRIORITY['name'] }}</option>
                                            <option value="{{ $task::LOW_PRIORITY['status'] }}" {{ $taskDetail->priority === $task::LOW_PRIORITY['status'] ? 'selected' : '' }}>{{ $task::LOW_PRIORITY['name'] }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3" style="margin: auto;">
                            <button class="btn btn-success">@lang('Add Comment')</button>
                        </div>
                    </div>
                </form>
            </div>
        </x-slot>
    </x-backend.card>
@endsection
@push('after-scripts')
    <script>
        $(document).ready(function () {
            $('.change-status').on('click', function () {
                let status = $(this).data('status')
                $('.change-status option:selected').removeAttr('selected');
                $('.change-status').find('option:eq(' + status + ')').attr('selected', 'selected');
            })
            $('.change-assignee').on('click', function () {
                let user_id = $(this).data('assignee')
                $('.assignee option:selected').removeAttr('selected');
                $('.assignee-' + user_id).attr('selected', 'selected');
            })

            $('.task-content').find('p').find('img').each(function(index, item) {
                $('.task-content').find('p').find('img').eq(index).addClass('zoom-in').attr('data-toggle', 'modal').attr('data-target', '#exampleModalTask-' + index)
                $('.task-content-modal').append(`
                <div class="modal fade bd-example-modal-lg" id="exampleModalTask-${index}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document" style="margin: 0 !important; left: 25%; top: 25%">
                        <div class="modal-content" style="width: 1035px; height: auto;">
                            <div class="modal-body comment-${index}">
                                <img src="${item.src}" style="width: 1000px; height: auto;">
                            </div>
                        </div>
                    </div>
                </div>`)
            })

            $('.task-comment').find('p').find('img').each(function(index, item) {
                $('.task-comment').find('p').find('img').eq(index).addClass('zoom-in').attr('data-toggle', 'modal').attr('data-target', '#exampleModalTaskComment-' + index)
                $('.modal-append').append(`
                <div class="modal fade bd-example-modal-lg" id="exampleModalTaskComment-${index}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document" style="margin: 0 !important; left: 25%; top: 25%">
                        <div class="modal-content" style="width: 1035px; height: auto;">
                            <div class="modal-body comment-${index}">
                                <img src="${item.src}" style="width: 1000px; height: auto;">
                            </div>
                        </div>
                    </div>
                </div>`)
            })
        })
    </script>
@endpush
