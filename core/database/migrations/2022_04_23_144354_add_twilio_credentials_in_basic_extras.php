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
        Schema::table('basic_extras', function (Blueprint $table) {
            $table->string('twilio_sid', 100)->nullable();
            $table->string('twilio_token', 100)->nullable();
            $table->string('twilio_phone_number', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('basic_extras', function (Blueprint $table) {
            $table->dropColumn(['twilio_sid','twilio_token','twilio_phone_number']);
        });
    }
};
