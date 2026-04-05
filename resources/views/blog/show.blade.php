@extends('layouts.blog', [
    'title' => $post->seo_title ?? $post->title,
    'metaDescription' => $post->seo_description ?? ($post->excerpt ?: 'Postagem do blog.'),
    'canonicalUrl' => $post->canonical_url ?: $post->public_url,
    'ogImage' => $post->featured_image
])

@section('content')
    <a href="{{ route('blog.index') }}" class="back-link">← Voltar para o blog</a>

    <article class="card">
        <h1 class="post-title" style="font-size: 34px;">{{ $post->title }}</h1>

        <div class="meta">
            Publicado em {{ optional($post->published_at)->format('d/m/Y H:i') ?? 'Sem data' }}

            @if ($post->author)
                • por {{ $post->author->name }}
            @endif
        </div>

        @if ($post->categories->isNotEmpty())
            <div style="margin: 14px 0 18px 0; display:flex; gap:8px; flex-wrap:wrap;">
                @foreach ($post->categories as $item)
                    <a
                        href="{{ route('blog.index', ['category' => $item->slug]) }}"
                        style="padding:6px 10px; border-radius:999px; background:#dbeafe; color:#1d4ed8; text-decoration:none; font-size:13px;"
                    >
                        {{ $item->name }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($post->featured_image)
            <img
                src="{{ $post->featured_image }}"
                alt="{{ $post->title }}"
                class="featured-image"
            >
        @endif

        @if ($post->excerpt)
            <p style="font-size: 18px; color: #4b5563; line-height:1.7;">
                {{ $post->excerpt }}
            </p>
        @endif

        @if ($post->tags->isNotEmpty())
            <div style="margin: 18px 0 24px 0; display:flex; gap:8px; flex-wrap:wrap;">
                @foreach ($post->tags as $item)
                    <a
                        href="{{ route('blog.index', ['tag' => $item->slug]) }}"
                        style="padding:6px 10px; border-radius:999px; background:#e5e7eb; color:#111827; text-decoration:none; font-size:13px;"
                    >
                        #{{ $item->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="content">
            {!! $post->content !!}
        </div>
    </article>

    @if ($relatedPosts->isNotEmpty())
        <section class="card">
            <h2 style="margin-top:0;">Postagens relacionadas</h2>

            <div style="display:grid; gap:16px;">
                @foreach ($relatedPosts as $item)
                    <div style="padding-top:4px;">
                        <h3 style="margin:0 0 8px 0;">
                            <a href="{{ route('blog.show', $item->slug) }}" style="text-decoration:none; color:#111827;">
                                {{ $item->title }}
                            </a>
                        </h3>

                        <div class="meta" style="margin-bottom:8px;">
                            {{ optional($item->published_at)->format('d/m/Y') ?? 'Sem data' }}
                        </div>

                        @if ($item->categories->isNotEmpty())
                            <div style="margin-bottom:8px; display:flex; gap:8px; flex-wrap:wrap;">
                                @foreach ($item->categories as $category)
                                    <span style="padding:4px 8px; border-radius:999px; background:#dbeafe; color:#1d4ed8; font-size:12px;">
                                        {{ $category->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if ($item->excerpt)
                            <p style="margin:0; color:#4b5563;">
                                {{ $item->excerpt }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection