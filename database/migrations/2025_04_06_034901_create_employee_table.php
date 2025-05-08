<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id('employee_id');
            $table->string('employee_name');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('password');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('owner_id');
            $table->rememberToken();
            $table->timestamps();

            // Khóa ngoại liên kết với bảng owners
            $table->foreign('owner_id')->references('owner_id')->on('owners')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
