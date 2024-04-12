@inject('model','\App\Models\Task')

@extends('backend.layouts.app')

@section('title', __('To Do List Management'))

@section('content')
    <x-backend.card>
        <x-slot name="header">
            @lang('Setting Task')
        </x-slot>
        @if($logged_in_user->isAdmin())
            <x-slot name="headerActions">
                <x-utils.link
                    icon="cil-plus"
                    class="btn btn-success btn-sm"
                    :href="route('admin.to-do-list.create')"
                    :text="__('Create To Do')"
                />
            </x-slot>
        @endif

        <x-slot name="body">
            <livewire:backend.tasks-table />
        </x-slot>
    </x-backend.card>
@endsection
