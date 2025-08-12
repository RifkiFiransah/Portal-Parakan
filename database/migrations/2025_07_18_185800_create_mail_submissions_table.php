<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mail_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Field umum
            $table->string('name'); // Nama utama
            $table->bigInteger('nik')->nullable();
            $table->bigInteger('no_kk')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->string('alamat')->nullable();
            
            // Field tambahan khusus per surat
            $table->string('nama_sekolah')->nullable(); // Untuk Tidak Mampu
            $table->string('nama_ortu')->nullable();    // Untuk Tidak Mampu
            $table->string('pekerjaan_ortu')->nullable();
            $table->string('pekerjaan')->nullable();   // Untuk Usaha, Umum
            $table->string('jenis_usaha')->nullable(); // Untuk Usaha
            
            $table->string('alamat_ktp')->nullable();      // Untuk Domisili
            $table->string('alamat_domisili')->nullable(); // Untuk Domisili

            // Field umum untuk semua jenis surat
            $table->enum('jenis_surat', [
                'Surat Keterangan Domisili',
                'Surat Keterangan Usaha',
                'Surat Keterangan Tidak Mampu',
                'Surat Keterangan Kematian',
                'Surat Keterangan Lahir',
                'Surat Keterangan Pindah',
                'Surat Keterangan Belum Menikah',
                'Surat Keterangan Cerai',
            ]);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'process', 'completed'])->default('pending');
            $table->string('file')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_submissions');
    }
};
