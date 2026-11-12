<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();   // akun admin kampus (PIC)
            $table->string('pic_name');
            $table->string('pic_position');
            $table->string('pic_phone');
            $table->string('institution_email');
            $table->string('document_path');                                 // surat tugas, dll.
            $table->string('status')->default('menunggu');                   // menunggu | disetujui | ditolak
            $table->text('admin_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_registrations');
    }
};
