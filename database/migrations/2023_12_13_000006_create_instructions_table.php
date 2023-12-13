<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstructionsTable extends Migration
{
    public function up()
    {
        Schema::create('instructions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('text');
            $table->string('position');
            $table->boolean('active')->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
