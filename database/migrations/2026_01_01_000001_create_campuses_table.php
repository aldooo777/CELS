<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('type')->default('PTN');          // PTN / PTS / Lainnya
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('email_domain')->nullable();      // contoh: ui.ac.id
            $table->boolean('is_listed')->default(true);     // false = "kampus tidak ada di daftar"
            $table->string('status')->default('belum_terdaftar');
            $table->date('trial_ends_at')->nullable();
            $table->date('subscription_ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campuses');
    }
};
