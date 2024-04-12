<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignee_id')->nullable();
            $table->string('name');
            $table->integer('status')->comment('1: open | 2: in progress | 3: resolve | 4: close | 5: done');
            $table->longText('content');
            $table->string('category')->nullable();
            $table->integer('total_sub_task')->nullable();
            $table->integer('priority')->comment('1: high priority | 2: normal priority | 3: low priority');
            $table->unsignedBigInteger('created_by');
            $table->string('start_due_date')->nullable();
            $table->integer('active')->comment('1: active | 2: inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
