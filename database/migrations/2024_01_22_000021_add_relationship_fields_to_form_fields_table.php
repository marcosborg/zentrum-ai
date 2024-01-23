<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToFormFieldsTable extends Migration
{
    public function up()
    {
        Schema::table('form_fields', function (Blueprint $table) {
            $table->unsignedBigInteger('form_id')->nullable();
            $table->foreign('form_id', 'form_fk_9421569')->references('id')->on('forms');
        });
    }
}
