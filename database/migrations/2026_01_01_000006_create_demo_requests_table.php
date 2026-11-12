<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Formulir "Minta Demo / Hubungi Kami" di landing page
        Schema::create('demo_requests', function (Blueprint $table) {
            $table->id();
            $table->string('institution_name');
            $table->string('contact_name');
            $table->string('email');
            $table->string('phone');
            $table->string('student_estimate')->nullable();
            $table->string('package')->default('belum_tahu');   // belum_tahu | dasar | premium
            $table->text('message')->nullable();
            $table->boolean('is_handled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_requests');
    }
};
