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
        Schema::create('basic_extras', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('whatsapp_order_notification')->default(0)->comment('0 - disable, 1 - enable');
            $table->tinyInteger('whatsapp_order_status_notification')->default(0)->comment('0 - disable, 1 - enable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('basic_extras');
    }
};
