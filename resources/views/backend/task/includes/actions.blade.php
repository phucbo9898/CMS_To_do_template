<x-utils.view-button :href="route('admin.to-do-list.view', $task->id)" />
@if($logged_in_user->isAdmin())
    <x-utils.edit-button :href="route('admin.to-do-list.edit', $task->id)" />
    <x-utils.delete-button :href="route('admin.to-do-list.destroy', $task->id)" />
@endif
