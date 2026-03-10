<div>
    <div class="card">
        <div class="topbar">
            <div>
                <h1 style="margin:0;">
                    {{ $categoryId ? 'Editar categoria' : 'Nova categoria' }}
                </h1>
                <p class="text-muted" style="margin:8px 0 0 0;">
                    Cadastre e organize as categorias do blog.
                </p>
            </div>

            <div class="actions">
                <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-secondary">Voltar</a>
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
            <label for="name">Nome</label>
            <input id="name" type="text" wire:model.lazy="name">
            @error('name') <div class="error">{{ $message }}</div> @enderror

            <label for="slug">Slug</label>
            <div style="display:flex; gap:10px;">
                <input id="slug" type="text" wire:model.lazy="slug">
                <button type="button" wire:click="generateSlug" class="btn btn-secondary">Gerar</button>
            </div>
            @error('slug') <div class="error">{{ $message }}</div> @enderror

            <div class="actions" style="margin-top: 12px;">
                <button type="submit" class="btn btn-success">Salvar categoria</button>
            </div>
        </div>
    </form>
</div>