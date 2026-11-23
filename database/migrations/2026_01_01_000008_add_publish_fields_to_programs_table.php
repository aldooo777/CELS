<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->after('is_open');              // null = draf
            $table->string('audience', 20)->default('internal')->after('published_at');  // internal | semua
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['published_at', 'audience']);
        });
    }
};
