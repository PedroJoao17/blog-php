@extends('layouts.blog', ['title' => 'Blog'])

@section('content')
    <div class="card">
        <h2 style="margin-top: 0;">Blog</h2>
        <p style="margin-bottom: 0;">Explore as postagens publicadas, filtre por categoria e tag, ou busque por termos do
            conteúdo.</p>
    </div>

    <div class="card">
        <form method="GET" action="{{ route('blog.index') }}">
            <label for="q" style="display:block; font-weight:bold; margin-bottom:8px;">Buscar no blog</label>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <input id="q" name="q" type="text" value="{{ $q }}"
                    placeholder="Buscar por título, resumo, conteúdo ou slug"
                    style="flex:1; min-width:260px; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px;">

                @if ($category !== '')
                    <input type="hidden" name="category" value="{{ $category }}">
                @endif

                @if ($tag !== '')
                    <input type="hidden" name="tag" value="{{ $tag }}">
                @endif

                <button type="submit"
                    style="padding:10px 14px; border:none; border-radius:8px; background:#2563eb; color:white; cursor:pointer;">
                    Buscar
                </button>

                @if ($q !== '' || $category !== '' || $tag !== '')
                    <a href="{{ route('blog.index') }}"
                        style="padding:10px 14px; border-radius:8px; background:#e5e7eb; color:#111827; text-decoration:none;">
                        Limpar filtros
                    </a>
                @endif
            </div>
        </form>

        <div style="margin-top: 18px;">
            <strong style="display:block; margin-bottom:8px;">Categorias</strong>

            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('blog.index', array_filter(['q' => $q, 'tag' => $tag])) }}"
                    style="padding:8px 12px; border-radius:999px; text-decoration:none; background:{{ $category === '' ? '#2563eb' : '#e5e7eb' }}; color:{{ $category === '' ? 'white' : '#111827' }};">
                    Todas
                </a>

                @foreach ($categories as $item)
                    <a href="{{ route('blog.index', array_filter(['q' => $q, 'category' => $item->slug, 'tag' => $tag])) }}"
                        style="padding:8px 12px; border-radius:999px; text-decoration:none; background:{{ $category === $item->slug ? '#2563eb' : '#e5e7eb' }}; color:{{ $category === $item->slug ? 'white' : '#111827' }};">
                        {{ $item->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <div style="margin-top: 18px;">
            <strong style="display:block; margin-bottom:8px;">Tags</strong>

            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('blog.index', array_filter(['q' => $q, 'category' => $category])) }}"
                    style="padding:8px 12px; border-radius:999px; text-decoration:none; background:{{ $tag === '' ? '#2563eb' : '#e5e7eb' }}; color:{{ $tag === '' ? 'white' : '#111827' }};">
                    Todas
                </a>

                @foreach ($tags as $item)
                    <a href="{{ route('blog.index', array_filter(['q' => $q, 'category' => $category, 'tag' => $item->slug])) }}"
                        style="padding:8px 12px; border-radius:999px; text-decoration:none; background:{{ $tag === $item->slug ? '#2563eb' : '#e5e7eb' }}; color:{{ $tag === $item->slug ? 'white' : '#111827' }};">
                        {{ $item->name }}
                    </a>
                @endforeach
            </div>
        </div>

        @if ($q !== '' || $category !== '' || $tag !== '')
            <p style="margin:14px 0 0 0; color:#4b5563;">
                @if ($q !== '')
                    Busca por: <strong>{{ $q }}</strong>
                @endif

                @if ($category !== '')
                    @if ($q !== '')
                        •
                    @endif
                    Categoria: <strong>{{ $category }}</strong>
                @endif

                @if ($tag !== '')
                    @if ($q !== '' || $category !== '')
                        •
                    @endif
                    Tag: <strong>{{ $tag }}</strong>
                @endif
            </p>
        @endif
    </div>

    @forelse ($posts as $post)
        <article class="card">
            @if ($post->featured_image)
                <a href="{{ route('blog.show', $post->slug) }}">
                    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="featured-image">
                </a>
            @endif

            <h2 class="post-title">
                <a href="{{ route('blog.show', $post->slug) }}">
                    {{ $post->title }}
                </a>
            </h2>

            <div class="meta">
                Publicado em {{ optional($post->published_at)->format('d/m/Y H:i') ?? 'Sem data' }}

                @if ($post->author)
                    • por {{ $post->author->name }}
                @endif

                @if ($post->category)
                    • categoria:
                    <a
                        href="{{ route('blog.index', array_filter(['q' => $q, 'category' => $post->category->slug, 'tag' => $tag])) }}">
                        {{ $post->category->name }}
                    </a>
                @endif
            </div>

            @if ($post->excerpt)
                <div class="excerpt">
                    {{ $post->excerpt }}
                </div>
            @endif

            @if ($post->tags->isNotEmpty())
                <div style="margin-top: 14px; display:flex; gap:8px; flex-wrap:wrap;">
                    @foreach ($post->tags as $item)
                        <a href="{{ route('blog.index', array_filter(['q' => $q, 'category' => $category, 'tag' => $item->slug])) }}"
                            style="padding:6px 10px; border-radius:999px; background:#e5e7eb; color:#111827; text-decoration:none; font-size:13px;">
                            #{{ $item->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            <p style="margin-top: 18px;">
                <a href="{{ route('blog.show', $post->slug) }}">Ler postagem</a>
            </p>
        </article>
    @empty
        <div class="card">
            <p>Nenhuma postagem encontrada com os filtros atuais.</p>
        </div>
    @endforelse

    <div class="pagination">
        {{ $posts->links() }}
    </div>
@endsection