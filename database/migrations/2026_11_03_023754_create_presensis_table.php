<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->date('tgl_presensi');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->double('lokasi_lat')->nullable();
            $table->double('lokasi_long')->nullable();
            $table->string('foto_selfie', 255)->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'pulang_cepat', 'sakit', 'izin', 'alpa'])->default('alpa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};