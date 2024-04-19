@inject('model', '\App\Models\Task')

@extends('backend.layouts.app')

@section('title', __('Create To Do'))

@section('content')
    <style>
        .hidden {
            display: none;
        }

        .picture-box {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }

        .btn-delete-image {
            color: #fff;
            background-color: #de2727;
            border-color: #d82121;
        }
    </style>
    <x-forms.post :action="route('admin.to-do-list.store')" enctype="multipart/form-data">
        <x-backend.card>
            <x-slot name="header">
                @lang('Create to do')
            </x-slot>

            <x-slot name="headerActions">
                <x-utils.link class="card-header-action" :href="route('admin.to-do-list.index')" :text="__('Cancel')" />
            </x-slot>

            <x-slot name="body">
                <div class="form-group row">
                    <label for="name" class="col-md-2 col-form-label">@lang('Name Task')</label>
                    <div class="col-md-10 text-left">
                        <input type="text" name="name_task" class="form-control" placeholder="{{ __('Name Task') }}" value="{{ old('name_task') }}" maxlength="100"  />
                    </div>
                </div>

{{--                <div class="form-group row">--}}
{{--                    <label for="name" class="col-md-2 col-form-label">@lang('Status')</label>--}}
{{--                    <div class="col-md-2">--}}
{{--                        <select name="type" class="form-control"  x-on:change="userType = $event.target.value">--}}
{{--                            <option value="{{ $model::TYPE_OPEN }}">@lang('Open')</option>--}}
{{--                            <option value="{{ $model::TYPE_IN_PROGRESS }}">@lang('In progess')</option>--}}
{{--                            <option value="{{ $model::TYPE_RESOLVE }}">@lang('Resolve')</option>--}}
{{--                            <option value="{{ $model::TYPE_CLOSE }}">@lang('Close')</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </div>--}}

                <div class="form-group row">
                    <label for="" class="col-md-2 col-form-label">@lang('Content')</label>
                    <div class="col-md-10">
                        <textarea class="form-control" id="contentArea" placeholder="{{ __('Content') }}" name="content_task">{{ old('content_task') ?? '' }}</textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="assignee" class="col-md-2 col-form-label">@lang('Assignee')</label>

                    <div class="col-md-3">
                        <select class="form-control" name="assignee" disabled>
                            <option value="" disabled selected>{{ $logged_in_user->name }}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="status_task" class="col-md-2 col-form-label">@lang('Status Task')</label>

                    <div class="col-md-3">
                        <select class="form-control" name="status_task" disabled>
                            <option value="" disabled selected>@lang('Open')</option>
                        </select>
                    </div>
                </div>

                @if(count($lstTaskParent))
                    <div class="form-group row">
                        <label for="parent_id" class="col-md-2 col-form-label">@lang('Task Parent')</label>

                        <div class="col-md-3">
                            <select class="form-control" name="parent_id">
                                <option value="" disabled selected>@lang('Choose Task')</option>
                                @foreach($lstTaskParent as $task)
                                    <option value="{{ $task->id }}">{{ $task->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                <div class="form-group row">
                    <label for="category" class="col-md-2 col-form-label">@lang('Category')</label>
                    <div class="col-md-3">
                        <select class="form-control" name="category">
                            <option value="" {{ empty(old('category') ? 'selected' : '') }}>@lang('Choose category')</option>
                            <option value="CMS" {{ !empty(old('category')) && old('category') === 'CMS' ? 'selected' : '' }}>@lang('CMS')</option>
                            <option value="FE" {{ !empty(old('category')) && old('category') === 'FE' ? 'selected' : '' }}>@lang('FE')</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="priority" class="col-md-2 col-form-label">@lang('Priority')</label>
                    <div class="col-md-3">
                        <select class="form-control" name="priority">
                            <option value="">@lang('Choose priority')</option>
                            <option value="{{ $model::HIGH_PRIORITY['status'] }}" {{ !empty(old('priority')) ? (old('priority') == $model::HIGH_PRIORITY['status'] ? 'selected' : '') : '' }}>{{ $model::HIGH_PRIORITY['name'] }}</option>
                            <option value="{{ $model::NORMAL_PRIORITY['status'] }}" {{ !empty(old('priority')) ? (old('priority') == $model::NORMAL_PRIORITY['status'] ? 'selected' : '') : 'selected' }}>{{ $model::NORMAL_PRIORITY['name'] }}</option>
                            <option value="{{ $model::LOW_PRIORITY['status'] }}" {{ !empty(old('priority')) ? (old('priority') == $model::LOW_PRIORITY['status'] ? 'selected' : '') : '' }}>{{ $model::LOW_PRIORITY['name'] }}</option>
                        </select>
                    </div>
                </div>
                <div>
                    {{ !empty(old('priority')) ? (old('priority') === $model::HIGH_PRIORITY['status'] ? 'selected' : '') : '' }}
                </div>
                <div class="form-group row">
                    <label for="duration_date" class="col-md-2 col-form-label">@lang('Start date') - @lang('Due date')</label>
                    <div class="col-lg-2 d-flex">
                        <input type="date" name="start_date" class="form-control start-date" value="{{ old('start_date') ?? ''}}">
                        <span class="p-2">-</span>
                        <input type="date" name="due_date" class="form-control due-date" value="{{ old('due_date') ?? ''}}">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="active" class="col-md-2">@lang('Active')</label>
                    <div class="col-md-10">
                        <div class="form-check">
                            <input name="active" id="active" class="form-check-input" type="checkbox" value="1" {{ old('active', true) ? 'checked' : '' }} />
                        </div><!--form-check-->
                    </div>
                </div><!--form-group-->

                <!-- Multiple upload file -->
{{--                <div class="form-group row">--}}
{{--                    <label for="active" class="col-md-2 col-form-label">@lang('Image')</label>--}}
{{--                    <div class="col-md-10">--}}
{{--                        <div class="list-input-hidden-upload">--}}
{{--                            <input type="file" name="filenames[]" id="file_upload" class="form-check-input hidden">--}}
{{--                        </div>--}}
{{--                        <div class="input-group-btn">--}}
{{--                            <button class="btn btn-success btn-add-image" type="button">+Add image</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div><!--form-group-->--}}
{{--                <div class="form-group row">--}}
{{--                    <div class="col-md-2"></div>--}}
{{--                    <div class="col-md-10">--}}
{{--                        <div class="list-images d-none"></div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <!-- Modal -->--}}
{{--                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">--}}
{{--                    <div class="modal-dialog modal-dialog-centered" role="document">--}}
{{--                        <div class="modal-content">--}}
{{--                            <div class="modal-header">--}}
{{--                                <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>--}}
{{--                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                                    <span aria-hidden="true">&times;</span>--}}
{{--                                </button>--}}
{{--                            </div>--}}
{{--                            <div class="modal-body">--}}
{{--                            </div>--}}
{{--                            <div class="modal-footer">--}}
{{--                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </x-slot>

            <x-slot name="footer">
                <button class="btn btn-sm btn-primary float-right btn-submit" type="submit">@lang('Create Task')</button>
            </x-slot>
        </x-backend.card>
    </x-forms.post>
@endsection
@push('after-scripts')
    <script>
        $(document).ready(function () {
            function checkDueDate() {
                let start_date = $('.start-date').val()
                let due_date = $('.due-date').val()
                // console.log('start_date', start_date)
                // console.log('due_date', due_date)
                if (start_date !== '' && due_date !== '' && start_date >= due_date) {
                    $('.btn-submit').hide()
                    let checkClassExist = $('.card-footer').find('.btn-create-term')
                    if (checkClassExist.length <= 0) {
                        $('.card-footer').append('<span class="btn btn-sm btn-primary float-right btn-create-term disabled">Create Task</span>')
                    }
                    alert('The start date must be before the due date')
                } else {
                    $('.btn-create-term').remove()
                    $('.btn-submit').show()
                }
            }

            $('.start-date').on('change', function () {
                checkDueDate()
            })

            $('.due-date').on('change', function () {
                checkDueDate()
            })

            // Handle file
            // $(".btn-add-image").click(function(){
            //     $('#file_upload').trigger('click');
            // });
            // $('.list-input-hidden-upload').on('change', '#file_upload', function(event){
            //     let countImage = $("input[name='filenames[]']").length
            //
            //     console.log($("input[name='filenames[]']").length)
            //     let today = new Date();
            //     let time = today.getTime();
            //     let image = event.target.files[0];
            //     let file_name = event.target.files[0].name;
            //     console.log(file_name)
            //     if (countImage > 0) {
            //         let textUpload = ''
            //         $(".list-images").removeClass('d-none')
            //         $('.modal-image').remove()
            //         if (countImage > 0 && countImage <= 1) {
            //             textUpload = '<a href="#" data-toggle="modal" class="modal-image" data-target="#exampleModalCenter">' + file_name + '</button>'
            //         } else {
            //             textUpload = '<a href="#" data-toggle="modal" class="modal-image" data-target="#exampleModalCenter">' + countImage + ' files' + '</button>'
            //         }
            //         $('.list-images').append(textUpload)
            //     }
            //     let box_image = $('<div class="box-image row mb-2"></div>');
            //     box_image.append('<div class="col-md-2">' + '<img src="' + URL.createObjectURL(image) + '" class="picture-box">' + '</div>');
            //     box_image.append('<div class="col-md-8">' + '<span>' + file_name + '</span>' + '</div>');
            //     box_image.append('<div class="wrap-btn-delete col-md-2"><span data-id='+time+' class="btn-delete-image btn btn-danger">x</span></div>');
            //     $(".modal-body").append(box_image);
            //
            //     $(this).removeAttr('id');
            //     $(this).attr( 'id', time);
            //     let input_type_file = '<input type="file" name="filenames[]" id="file_upload" class="form-control hidden">';
            //     $('.list-input-hidden-upload').append(input_type_file);
            // });
            //
            // $(".modal-body").on('click', '.btn-delete-image', function(){
            //     let id = $(this).data('id');
            //     console.log('id', id)
            //     $('#'+id).remove();
            //     $(this).parents('.box-image').remove();
            //     let countImage = $("input[name='filenames[]']").length - 1
            //     console.log('countImage', countImage)
            //     if (countImage <= 0) {
            //         $(".list-images").addClass('d-none')
            //     }
            // });
        })
    </script>
@endpush
