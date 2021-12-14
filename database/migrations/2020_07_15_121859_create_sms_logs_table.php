<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class CreateSmsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = config('bitamessage.tableName', 'sms_logs');
        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->nullable();
            $table->string('from', 100)->nullable();
            $table->string('to', 100)->nullable();
            $table->string('message', 500)->nullable();
            $table->boolean('status')->nullable();
            $table->boolean('delivery_check_needed')->default(1);
            $table->string('delivery_status')->default(0);
            $table->string('service', 50)->nullable();
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
        Schema::dropIfExists('sms_logs');
    }
}
