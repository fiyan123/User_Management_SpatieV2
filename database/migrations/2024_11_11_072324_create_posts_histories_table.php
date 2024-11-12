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
        Schema::create('posts_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id'); // ID dari tabel utama
            $table->string('judul');
            $table->string('nama_pembuat');
            $table->text('isi_posts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts_histories');
    }
};
