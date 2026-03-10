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

            <div class="row">
                <div class="col">
                    <label for="tag_ids">Tags</label>
                    <select id="tag_ids" wire:model="tag_ids" multiple style="min-height: 140px;">
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Segure Ctrl para selecionar várias tags.</small>
                    @error('tag_ids') <div class="error">{{ $message }}</div> @enderror
                    @error('tag_ids.*') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <label for="excerpt">Resumo</label>
            <textarea id="excerpt" wire:model.lazy="excerpt"></textarea>
            @error('excerpt') <div class="error">{{ $message }}</div> @enderror

            <label for="content-editor">Conteúdo</label>

            <div wire:ignore>
                <textarea id="content-editor"
                    style="min-height: 260px; width:100%; border:1px solid #d1d5db; border-radius:8px; padding:12px;">{{ $content }}</textarea>
            </div>

            @error('content') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="card">
            <label for="featuredImageUpload">Imagem destacada</label>
            <input id="featuredImageUpload" type="file" wire:model="featuredImageUpload" accept="image/*">
            @error('featuredImageUpload') <div class="error">{{ $message }}</div> @enderror

            <div wire:loading wire:target="featuredImageUpload" class="text-muted" style="margin-bottom: 12px;">
                Enviando imagem...
            </div>

            @if ($featuredImageUpload)
                <div style="margin-top: 12px;">
                    <p><strong>Prévia da nova imagem:</strong></p>
                    <img src="{{ $featuredImageUpload->temporaryUrl() }}" alt="Prévia"
                        style="max-width: 280px; border-radius: 8px;">
                </div>
            @elseif ($currentFeaturedImage)
                <div style="margin-top: 12px;">
                    <p><strong>Imagem atual:</strong></p>
                    <img src="{{ $currentFeaturedImage }}" alt="Imagem destacada"
                        style="max-width: 280px; border-radius: 8px;">
                    <div style="margin-top: 10px;">
                        <button type="button" wire:click="removeFeaturedImage" class="btn btn-secondary">
                            Remover imagem
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <div class="card">
            <div class="actions">
                <button type="submit" class="btn btn-success">Salvar postagem</button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        function initBlogEditor() {
            const editorElement = document.getElementById('content-editor');

            if (!editorElement) {
                console.error('CKEditor: elemento #content-editor não encontrado.');
                return;
            }

            if (editorElement.dataset.ckeditorInitialized === 'true') {
                return;
            }

            if (!window.BlogCkeditor || !window.BlogCkeditor.ClassicEditor) {
                setTimeout(initBlogEditor, 300);
                return;
            }

            window.BlogCkeditor.ClassicEditor
                .create(editorElement, window.BlogCkeditor.config)
                .then(editor => {
                    editorElement.dataset.ckeditorInitialized = 'true';
                    window.blogEditorInstance = editor;

                    // Seta o conteúdo inicial uma vez.
                    editor.setData(@this.get('content') || '');

                    // Atualiza o Livewire, mas sem regravar o editor a cada resposta.
                    editor.model.document.on('change:data', () => {
                        @this.set('content', editor.getData());
                    });
                })
                .catch(error => {
                    console.error('Erro ao iniciar CKEditor 5:', error);
                });
        }

        document.addEventListener('livewire:load', function () {
            initBlogEditor();
        });
    </script>
@endpush