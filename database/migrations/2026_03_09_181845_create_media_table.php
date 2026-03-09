<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blog_media', function (Blueprint $table) {
            $table->id();

            $table->morphs('attachable');

            $table->string('collection', 50); // content | featured
            $table->string('disk', 50)->default('public');
            $table->string('directory')->nullable();
            $table->string('filename');
            $table->string('path');
            $table->string('url');

            $table->string('mime_type')->nullable();
            $table->string('extension', 20)->nullable();
            $table->unsignedBigInteger('size')->nullable();

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('draft_token')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_media');
    }
};