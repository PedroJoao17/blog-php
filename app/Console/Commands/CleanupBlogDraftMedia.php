<?php

namespace App\Console\Commands;

use App\Services\Blog\MediaService;
use Illuminate\Console\Command;

class CleanupBlogDraftMedia extends Command
{
    protected $signature = 'blog:media:cleanup-drafts {--hours=24 : Remove mídias temporárias mais antigas que X horas}';

    protected $description = 'Remove imagens temporárias de conteúdo do blog que nunca foram vinculadas a um post.';

    public function handle(MediaService $mediaService): int
    {
        $hours = (int) $this->option('hours');
        $deleted = $mediaService->cleanupAbandonedDraftMedia($hours);

        $this->info("Mídias temporárias removidas: {$deleted}");

        return self::SUCCESS;
    }
}