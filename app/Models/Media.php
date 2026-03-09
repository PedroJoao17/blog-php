<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $table = 'blog_media';

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'collection',
        'disk',
        'directory',
        'filename',
        'path',
        'url',
        'mime_type',
        'extension',
        'size',
        'uploaded_by',
        'draft_token',
    ];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}