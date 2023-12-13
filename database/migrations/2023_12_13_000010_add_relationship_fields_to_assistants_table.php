<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToAssistantsTable extends Migration
{
    public function up()
    {
        Schema::table('assistants', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable();
            $table->foreign('project_id', 'project_fk_9301120')->references('id')->on('projects');
        });
    }
}
