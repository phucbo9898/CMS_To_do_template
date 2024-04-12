@inject('model', '\App\Models\Task')

@extends('backend.layouts.app')

@section('title', __('View To Do detail'))

@section('content')
    <x-backend.card>
        <x-slot name="header">
            <h3>{{ $taskDetail->name }}</h3>
        </x-slot>

        <x-slot name="body">
            <div class="task-assignee">
                <label for="">Assignee:</label>
                <span>{{ $taskDetail->assignee }}</span>
            </div>
            <div class="task-content">
                {!! $taskDetail->content !!}
            </div>
            <hr class="mt-5">
            <div class="task-comment p-3">
                <label for="comment-task">Add Comment</label>
                <form action="" method="post">
                    <div class="form-group row">
                        <div class="col-md-9">
                            <textarea class="form-control" id="contentArea" placeholder="{{ __('Content') }}" name="content_task"></textarea>
                        </div>
                        <div class="col-md-3">
                            <div class="status-task">
                                <div class="label d-flex justify-content-between">
                                    <label for="">Status</label>
                                    @switch($taskDetail->status + 1)
                                        @case($model::OPEN['status'])
                                            <span class="text-success change-status cursor-pointer" data-status="{{ $taskDetail->status }}" >Set to "{{ $model::OPEN['name'] }}"</span>
                                            @break
                                        @case($model::IN_PROGRESS['status'])
                                            <span class="text-success change-status cursor-pointer" data-status="{{ $taskDetail->status }}" >Set to "{{ $model::IN_PROGRESS['name'] }}"</span>
                                            @break
                                        @case($model::RESOLVE['status'])
                                            <span class="text-success change-status cursor-pointer" data-status="{{ $taskDetail->status }}" >Set to "{{ $model::RESOLVE['name'] }}"</span>
                                            @break
                                        @case($model::CLOSE['status'])
                                            <span class="text-success change-status cursor-pointer" data-status="{{ $taskDetail->status }}" >Set to "{{ $model::CLOSE['name'] }}"</span>
                                            @break
                                        @default
                                            <span class="text-success change-status cursor-pointer" data-status="{{ $taskDetail->status }}" >Set to "{{ $model::DONE['name'] }}"</span>
                                    @endswitch
                                </div>
                                <div class="option-status">
                                    <select name="change_status" class="form-control change-status">
                                        <option value="{{ $model::OPEN['status'] }}" {{ $taskDetail->status === $model::OPEN['status'] ? 'selected' : '' }}>@lang('Open')</option>
                                        <option value="{{ $model::IN_PROGRESS['status'] }}" {{ $taskDetail->status === $model::IN_PROGRESS['status'] ? 'selected' : '' }}>@lang('In progress')</option>
                                        <option value="{{ $model::RESOLVE['status'] }}" {{ $taskDetail->status === $model::RESOLVE['status'] ? 'selected' : '' }}>@lang('Resolve')</option>
                                        <option value="{{ $model::CLOSE['status'] }}" {{ $taskDetail->status === $model::CLOSE['status'] ? 'selected' : '' }}>@lang('Close')</option>
                                        <option value="{{ $model::DONE['status'] }}" {{ $taskDetail->status === $model::DONE['status'] ? 'selected' : '' }}>@lang('Done')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between">
                                        <label for="">Assignee</label>
                                        <span class="text-success change-assignee" data-assignee="{{ $logged_in_user->id }}" style="cursor: pointer;">Set to "Me"</span>
                                    </div>
                                    <div class="assignee-task">
                                        <select name="change_status" class="form-control assignee">
                                            @foreach($lstUser as $user)
                                                <option class="assignee-{{ $user->id }}" value="{{ $user->id }}" {{ $taskDetail->user_id === $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="title-start-date">
                                        <label for="">@lang('Start date')</label>
                                    </div>
                                    <div>
                                        <input type="date" name="start_date" class="form-control start_date" value="{{ old('start_date') ?? ($task->start_date ?? '')}}">
                                    </div>
                                </div>
                            </div>
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
        })
    </script>
@endpush
