<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $blueprint) {
            $blueprint->string('user_id')->primary();
            $blueprint->string('name');
            $blueprint->string('email');
            $blueprint->enum('status', ['NEW CUSTOMER', 'LOYAL CUSTOMER']);
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
