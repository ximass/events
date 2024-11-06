<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckinsTable extends Migration
{
    public function up()
    {
        Schema::create('checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->nullable()->constrained('registrations')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->timestamp('checkin_time')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    public function down()
    {
        Schema::dropIfExists('checkins');
    }
}