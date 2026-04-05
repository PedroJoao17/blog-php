<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blog_post_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('blog_categories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['post_id', 'category_id'], 'blog_post_category_unique');
        });

        DB::table('blog_posts')
            ->whereNotNull('category_id')
            ->orderBy('id')
            ->chunkById(100, function ($posts) {
                $rows = [];

                foreach ($posts as $post) {
                    $rows[] = [
                        'post_id' => $post->id,
                        'category_id' => $post->category_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($rows)) {
                    DB::table('blog_post_category')->insertOrIgnore($rows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_category');
    }
};