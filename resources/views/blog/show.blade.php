@extends('layouts.blog', ['title' => $post->title])

@section('content')
    <a href="{{ route('blog.index') }}" class="back-link">← Voltar para o blog</a>

    <article class="card">
        <h1 class="post-title">{{ $post->title }}</h1>

        <div class="meta">
            Publicado em
            {{ optional($post->published_at)->format('d/m/Y H:i') ?? 'Sem data' }}

            @if ($post->author)
                • por {{ $post->author->name }}
            @endif

            @if ($post->category)
                • categoria: {{ $post->category->name }}
            @endif
        </div>

        @if ($post->featured_image)
            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="featured-image">
        @endif

        @if ($post->excerpt)
            <p style="font-size: 18px; color: #4b5563;">
                {{ $post->excerpt }}
            </p>
        @endif

        <div class="content">
            {!! $post->content !!}
        </div>
    </article>
@endsection