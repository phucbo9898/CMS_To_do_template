<?php

use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\TaskController;
use Tabuna\Breadcrumbs\Trail;
use Illuminate\Support\Facades\Route;

// All route names are prefixed with 'admin.'.
Route::middleware('admin')->group(function () {
    Route::redirect('/', '/admin/dashboard', 301);
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('admin.dashboard'));
        });
});

Route::group(['prefix' => 'to-do-list', 'as' => 'to-do-list.'], function () {
    Route::get('to-do', [TaskController::class, 'index'])
        ->name('index')
        ->breadcrumbs(function (Trail $trail) {
            $trail->parent('admin.dashboard')
                ->push(__('List To Do'), route('admin.to-do-list.index'));
        });
    Route::get('create-to-do', [TaskController::class, 'create'])
        ->name('create')
        ->breadcrumbs(function (Trail $trail) {
           $trail->parent('admin.to-do-list.index')
               ->push(__('Create To Do'), route('admin.to-do-list.create'));
        });
    Route::post('create-to-do', [TaskController::class, 'store'])->name('store');
    Route::get('view/{id}', [TaskController::class, 'show'])
        ->name('view')
        ->breadcrumbs(function (Trail $trail, $id) {
            $trail->parent('admin.to-do-list.index', $id)
                ->push(__('Task ID: ') . $id, route('admin.to-do-list.view', $id));
        });
    Route::post('add-comment/{id}', [TaskController::class, 'addComment'])->name('add-comment');
    Route::get('view/{id}/view-sub-task/{subTaskId}', [TaskController::class, 'viewSubTask'])
        ->name('view-sub-task')
        ->breadcrumbs(function (Trail $trail, $id, $subTaskId) {
            $trail->parent('admin.to-do-list.view', $id)
                ->push(__('View Sub Task'), route('admin.to-do-list.view-sub-task', [$id, $subTaskId]));
        });
    Route::middleware('admin')->group(function () {
        Route::get('edit-to-do/{id}', [TaskController::class, 'edit'])
            ->name('edit')
            ->breadcrumbs(function (Trail $trail, $id) {
                $trail->parent('admin.to-do-list.index')
                    ->push(__('Edit To Do'), route('admin.to-do-list.edit', $id));
            });
        Route::post('edit-to-do', [TaskController::class, 'update'])->name('update');
        Route::delete('delete-to-do/{id}', [TaskController::class, 'destroy'])->name('destroy');
    });
});

