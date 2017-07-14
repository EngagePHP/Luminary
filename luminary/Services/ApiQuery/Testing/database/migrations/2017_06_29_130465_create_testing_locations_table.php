<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestingLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('street');
            $table->string('city');
            $table->string('zip');
            $table->string('phone');
            $table->timestamps();
            $table->softDeletes();

            $table->index('id');
            $table->index('name');
            $table->index('street');
            $table->index('city');
            $table->index('zip');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
