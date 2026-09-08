<?php

use Core\Database\Migration;
use Core\Database\Schema;
use Core\Database\Table;

return new class implements Migration
{
    public function up()
    {
        Schema::table('comments', function (Table $table) {
            $table->addColumn(function (Table $table) {
                $table->integer('guest_id')->nullable();
            });

            $table->index('guest_id');
            $table->foreign('guest_id')->references('id')->on('guests');
        });
    }

    public function down()
    {
        Schema::table('comments', function (Table $table) {
            $table->dropForeign('guest_id');
            $table->dropColumn('guest_id');
        });
    }
};