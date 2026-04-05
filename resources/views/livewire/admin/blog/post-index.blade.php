<div>
    <div class="card">
        <div class="topbar">
            <div>
                <h1 style="margin:0;">Postagens</h1>
                <p class="text-muted" style="margin:8px 0 0 0;">Gerencie as postagens do blog.</p>
            </div>

            <div class="actions">
                <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-primary">Nova postagem</a>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="flash">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <label for="search">Buscar</label>
        <input id="search" type="text" wire:model.debounce.400ms="search"
            placeholder="Buscar por título, slug ou status">
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Status</th>
                    <th>Categorias</th>
                    <th>Tags</th>
                    <th>Publicação</th>
                    <th>Autor</th>
                    <th width="180">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $post->title }}</div>

                            @if ($post->excerpt)
                                <div class="text-muted" style="font-size:13px; margin-top:4px;">
                                    {{ $post->excerpt }}
                                </div>
                            @endif
                        </td>

                        <td>
                            @if ($post->isScheduled())
                                <span class="badge badge-draft">Agendado</span>
                            @elseif ($post->isPubliclyVisible())
                                <span class="badge badge-published">Publicado</span>
                            @else
                                <span class="badge badge-draft">Rascunho</span>
                            @endif
                        </td>

                        <td>
                            @if ($post->categories->isNotEmpty())
                                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                    @foreach ($post->categories as $category)
                                        <span
                                            style="padding:4px 8px; border-radius:999px; background:#dbeafe; color:#1d4ed8; font-size:12px;">
                                            {{ $category->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td>
                            @if ($post->tags->isNotEmpty())
                                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                    @foreach ($post->tags as $tag)
                                        <span
                                            style="padding:4px 8px; border-radius:999px; background:#e5e7eb; color:#111827; font-size:12px;">
                                            #{{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td>{{ optional($post->published_at)->format('d/m/Y H:i') ?: '-' }}</td>
                        <td>{{ optional($post->author)->name ?: '-' }}</td>

                        <td>
                            <a href="{{ route('admin.blog.posts.edit', $post->id) }}">Editar</a>

                            @if ($post->isPubliclyVisible())
                                |
                                <a href="{{ route('blog.show', $post->slug) }}" target="_blank">Ver</a>
                            @endif

                            |
                            <button type="button" wire:click="delete({{ $post->id }})"
                                onclick="confirm('Deseja realmente excluir esta postagem?') || event.stopImmediatePropagation()"
                                style="background:none; border:none; color:#b91c1c; cursor:pointer; padding:0;">
                                Excluir
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Nenhuma postagem encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $posts->links() }}
        </div>
    </div>
</div>