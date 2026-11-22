<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->enum('status', ['hadir', 'terlambat', 'pulang_cepat', 'sakit', 'izin', 'dispen', 'alpa'])
                ->default('alpa')
                ->change();
        });
    }

    public function down(): void
    {
        DB::table('presensis')->where('status', 'dispen')->update(['status' => 'izin']);

        Schema::table('presensis', function (Blueprint $table) {
            $table->enum('status', ['hadir', 'terlambat', 'pulang_cepat', 'sakit', 'izin', 'alpa'])
                ->default('alpa')
                ->change();
        });
    }
};
