<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('mahasiswa')->after('email');   // admin_cels | admin_kampus | mahasiswa
            $table->foreignId('campus_id')->nullable()->after('role')->constrained()->nullOnDelete();
            $table->string('nim')->nullable()->after('campus_id');
            $table->string('prodi')->nullable()->after('nim');
            $table->string('phone')->nullable()->after('prodi');
            $table->string('position')->nullable()->after('phone');        // jabatan PIC admin kampus
            $table->timestamp('verified_by_campus_at')->nullable()->after('email_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campus_id');
            $table->dropColumn(['role', 'nim', 'prodi', 'phone', 'position', 'verified_by_campus_at']);
        });
    }
};
