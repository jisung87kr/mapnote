<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('address_name')->nullable();
            $table->string('category_group_code')->nullable();
            $table->string('category_group_name')->nullable();
            $table->string('category_name')->nullable();
            $table->string('distance')->nullable();
            $table->string('map_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('place_name')->nullable();
            $table->string('place_url')->nullable();
            $table->string('road_address_name')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
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
        Schema::dropIfExists('locations');
    }
};
