<div>
    <div class="card">
        <div class="topbar">
            <div>
                <h1 style="margin:0;">
                    {{ $postId ? 'Editar postagem' : 'Nova postagem' }}
                </h1>
                <p class="text-muted" style="margin:8px 0 0 0;">
                    Preencha os dados básicos da postagem.
                </p>
            </div>

            <div class="actions">
                <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-secondary">Voltar</a>
                @if ($slug)
                    <a href="{{ route('blog.show', $slug) }}" target="_blank" class="btn btn-secondary">Ver pública</a>
                @endif
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="flash">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="card">
            <div class="row">
                <div class="col">
                    <label for="title">Título</label>
                    <input id="title" type="text" wire:model.lazy="title">
                    @error('title') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="col">
                    <label for="slug">Slug</label>
                    <div style="display:flex; gap:10px;">
                        <input id="slug" type="text" wire:model.lazy="slug">
                        <button type="button" wire:click="generateSlug" class="btn btn-secondary">Gerar</button>
                    </div>
                    @error('slug') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <label for="category_id">Categoria</label>
                    <select id="category_id" wire:model="category_id">
                        <option value="">Selecione</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="col">
                    <label for="status">Status</label>
                    <select id="status" wire:model="status">
                        <option value="draft">Rascunho</option>
                        <option value="published">Publicado</option>
                    </select>
                    @error('status') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="col">
                    <label for="published_at">Data de publicação</label>
                    <input id="published_at" type="datetime-local" wire:model="published_at">
                    @error('published_at') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <label for="excerpt">Resumo</label>
            <textarea id="excerpt" wire:model.lazy="excerpt"></textarea>
            @error('excerpt') <div class="error">{{ $message }}</div> @enderror

            <label for="content">Conteúdo</label>
            <textarea id="content" wire:model.lazy="content" style="min-height: 260px;"></textarea>
            @error('content') <div class="error">{{ $message }}</div> @enderror

            <div class="actions" style="margin-top: 12px;">
                <button type="submit" class="btn btn-success">Salvar postagem</button>
            </div>
        </div>
    </form>
</div>