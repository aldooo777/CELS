<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();   // mahasiswa
            $table->text('motivation')->nullable();
            $table->string('cv_path');
            $table->string('document_path')->nullable();
            $table->string('status')->default('diajukan');    // diajukan | diproses | disetujui | ditolak
            $table->text('admin_note')->nullable();
            $table->timestamp('status_changed_at')->nullable();
            $table->timestamps();

            $table->unique(['program_id', 'user_id']);         // satu mahasiswa sekali daftar per program
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
