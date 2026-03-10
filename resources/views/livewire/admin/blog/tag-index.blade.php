<div>
    <div class="card">
        <div class="topbar">
            <div>
                <h1 style="margin:0;">Tags</h1>
                <p class="text-muted" style="margin:8px 0 0 0;">Gerencie as tags do blog.</p>
            </div>

            <div class="actions">
                <a href="{{ route('admin.blog.tags.create') }}" class="btn btn-primary">Nova tag</a>
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
        <input id="search" type="text" wire:model.debounce.400ms="search" placeholder="Buscar por nome ou slug">
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Slug</th>
                    <th>Posts</th>
                    <th width="180">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tags as $tag)
                    <tr>
                        <td>{{ $tag->name }}</td>
                        <td>{{ $tag->slug }}</td>
                        <td>{{ $tag->posts_count }}</td>
                        <td>
                            <a href="{{ route('admin.blog.tags.edit', $tag->id) }}">Editar</a>
                            |
                            <button type="button" wire:click="delete({{ $tag->id }})"
                                onclick="confirm('Deseja realmente excluir esta tag?') || event.stopImmediatePropagation()"
                                style="background:none; border:none; color:#b91c1c; cursor:pointer; padding:0;">
                                Excluir
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Nenhuma tag encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:20px;">
            {{ $tags->links() }}
        </div>
    </div>
</div>