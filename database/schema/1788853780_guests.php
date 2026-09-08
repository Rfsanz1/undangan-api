<?php

use Core\Database\Migration;
use Core\Database\Schema;
use Core\Database\Table;

return new class implements Migration
{
    public function up()
    {
        Schema::create('guests', function (Table $table) {
            $table->id();

            $table->integer('user_id');
            $table->string('uuid', 36)->unique();
            $table->string('token', 64)->unique();
            $table->string('name', 100);
            $table->string('greeting', 100)->nullable();
            $table->string('category', 50)->nullable();
            $table->boolean('presence')->nullable();

            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timeStamp();
        });
    }

    public function down()
    {
        Schema::drop('guests');
    }
};