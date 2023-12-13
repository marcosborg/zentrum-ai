<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenaisTable extends Migration
{
    public function up()
    {
        Schema::create('openais', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('organization');
            $table->string('openai_api_key');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
