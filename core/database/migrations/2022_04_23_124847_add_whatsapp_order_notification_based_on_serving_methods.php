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
            $table->dropColumn('whatsapp_order_notification');
            $table->tinyInteger('whatsapp_home_delivery')->default(0)->comment('0 - enabled, 1 - disabled');
            $table->tinyInteger('whatsapp_pickup')->default(0)->comment('0 - enabled, 1 - disabled');
            $table->tinyInteger('whatsapp_on_table')->default(0)->comment('0 - enabled, 1 - disabled');
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
            $table->dropColumn(['whatsapp_home_delivery', 'whatsapp_pickup', 'whatsapp_on_table']);
        });
    }
};
