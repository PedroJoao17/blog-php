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
                    <th>Slug</th>
                    <th>Publicação</th>
                    <th>Autor</th>
                    <th width="140">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr>
                        <td>{{ $post->title }}</td>
                        <td>
                            @if ($post->status === 'published')
                                <span class="badge badge-published">Publicado</span>
                            @else
                                <span class="badge badge-draft">Rascunho</span>
                            @endif
                        </td>
                        <td>{{ $post->slug }}</td>
                        <td>{{ optional($post->published_at)->format('d/m/Y H:i') ?: '-' }}</td>
                        <td>{{ optional($post->author)->name ?: '-' }}</td>
                        <td>
                            <a href="{{ route('admin.blog.posts.edit', $post->id) }}">Editar</a>
                            |
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Nenhuma postagem encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $posts->links() }}
        </div>
    </div>
</div>