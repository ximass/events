<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificateValidationsTable extends Migration
{
    public function up()
    {
        Schema::create('certificate_validations', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_code');
            $table->timestamp('validated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign('certificate_code')->references('certificate_code')->on('certificates')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificate_validations');
    }
}
