<?php

namespace App\Services\Blog;

use Mews\Purifier\Facades\Purifier;

class HtmlContentSanitizer
{
    public function sanitize(?string $html): string
    {
        if (!is_string($html) || trim($html) === '') {
            return '';
        }

        $clean = Purifier::clean($html, 'blog_post_content');

        return is_string($clean) ? trim($clean) : '';
    }
}