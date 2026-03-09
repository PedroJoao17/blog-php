@extends('layouts.blog', ['title' => 'Blog'])

@section('content')
    <div class="card">
        <h2 style="margin-top: 0;">Últimas postagens</h2>
        <p>Protótipo inicial do módulo de blog.</p>
    </div>

    @forelse ($posts as $post)
        <article class="card">
            <h2 class="post-title">
                <a href="{{ route('blog.show', $post->slug) }}">
                    {{ $post->title }}
                </a>
            </h2>

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
                <div class="excerpt">
                    {{ $post->excerpt }}
                </div>
            @endif

            <p style="margin-top: 18px;">
                <a href="{{ route('blog.show', $post->slug) }}">Ler postagem</a>
            </p>
        </article>
    @empty
        <div class="card">
            <p>Nenhuma postagem publicada até o momento.</p>
        </div>
    @endforelse

    <div class="pagination">
        {{ $posts->links() }}
    </div>
@endsection