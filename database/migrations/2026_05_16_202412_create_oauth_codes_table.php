<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("oauth_codes", function (Blueprint $table) {
            $table->string("code", 64)->primary();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->timestamp("expires_at");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("oauth_codes");
    }
};
