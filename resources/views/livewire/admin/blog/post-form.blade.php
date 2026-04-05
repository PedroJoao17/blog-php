<div>
    <div class="card">
        <div class="topbar">
            <div>
                <h1 style="margin:0;">
                    {{ $postId ? 'Editar postagem' : 'Nova postagem' }}
                </h1>
                <p class="text-muted" style="margin:8px 0 0 0;">
                    Defina o conteúdo, categorias, tags e publicação da postagem.
                </p>

                @if ($status === 'draft')
                    <p class="text-muted" style="margin:8px 0 0 0;">
                        Situação atual: <strong>Rascunho</strong>
                    </p>
                @elseif ($this->isScheduled)
                    <p class="text-muted" style="margin:8px 0 0 0;">
                        Situação atual: <strong>Agendado</strong> para
                        {{ \Carbon\Carbon::parse($published_at)->format('d/m/Y H:i') }}
                    </p>
                @elseif ($this->isPubliclyVisible)
                    <p class="text-muted" style="margin:8px 0 0 0;">
                        Situação atual: <strong>Publicado</strong>
                    </p>
                @endif
            </div>

            <div class="actions">
                <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-secondary">Voltar</a>

                @if ($slug && $this->isPubliclyVisible)
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

    <form wire:submit.prevent="saveDraft">
        <div class="card">
            <div class="row">
                <div class="col">
                    <label for="title">Título</label>
                    <input id="title" type="text" wire:model.lazy="title">
                    @error('title') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="col">
                    <label for="excerpt">Subtítulo</label>
                    <input id="excerpt" type="text" wire:model.lazy="excerpt">
                    @error('excerpt') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div
                        style="display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:6px;">
                        <label style="margin-bottom:0;">Categorias</label>
                        <button type="button" wire:click="openCategoryModal" class="btn btn-secondary"
                            style="padding:6px 10px;">+</button>
                    </div>

                    <div x-data="{ open: false }" style="position:relative;">
                        <button type="button" @click="open = !open"
                            style="width:100%; text-align:left; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; background:white; cursor:pointer;">
                            @if ($this->selectedCategories->isNotEmpty())
                                {{ $this->selectedCategories->pluck('name')->join(', ') }}
                            @else
                                Selecione uma ou mais categorias
                            @endif
                        </button>

                        <div x-show="open" @click.outside="open = false" x-transition
                            style="position:absolute; top:calc(100% + 6px); left:0; width:100%; background:white; border:1px solid #d1d5db; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.08); z-index:50; max-height:240px; overflow:auto; padding:10px;">
                            @forelse ($categories as $category)
                                <label
                                    style="display:flex; align-items:center; gap:8px; padding:6px 0; font-weight:normal;">
                                    <input type="checkbox" wire:model="category_ids" value="{{ $category->id }}"
                                        style="width:auto; margin:0;">
                                    <span>{{ $category->name }}</span>
                                </label>
                            @empty
                                <p class="text-muted" style="margin:0;">Nenhuma categoria cadastrada.</p>
                            @endforelse
                        </div>
                    </div>

                    @if ($this->selectedCategories->isNotEmpty())
                        <div style="margin-top:10px; display:flex; gap:8px; flex-wrap:wrap;">
                            @foreach ($this->selectedCategories as $category)
                                <span
                                    style="padding:6px 10px; border-radius:999px; background:#dbeafe; color:#1d4ed8; font-size:13px;">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    @error('category_ids') <div class="error">{{ $message }}</div> @enderror
                    @error('category_ids.*') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="col">
                    <label for="published_at">Data de publicação</label>
                    <input id="published_at" type="datetime-local" wire:model="published_at">
                    @error('published_at') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div
                        style="display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:6px;">
                        <label style="margin-bottom:0;">Tags</label>
                        <button type="button" wire:click="openTagModal" class="btn btn-secondary"
                            style="padding:6px 10px;">+</button>
                    </div>

                    <div x-data="{ open: false }" style="position:relative;">
                        <button type="button" @click="open = !open"
                            style="width:100%; text-align:left; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; background:white; cursor:pointer;">
                            @if ($this->selectedTags->isNotEmpty())
                                {{ $this->selectedTags->pluck('name')->join(', ') }}
                            @else
                                Selecione uma ou mais tags
                            @endif
                        </button>

                        <div x-show="open" @click.outside="open = false" x-transition
                            style="position:absolute; top:calc(100% + 6px); left:0; width:100%; background:white; border:1px solid #d1d5db; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.08); z-index:50; max-height:240px; overflow:auto; padding:10px;">
                            @forelse ($tags as $tag)
                                <label
                                    style="display:flex; align-items:center; gap:8px; padding:6px 0; font-weight:normal;">
                                    <input type="checkbox" wire:model="tag_ids" value="{{ $tag->id }}"
                                        style="width:auto; margin:0;">
                                    <span>{{ $tag->name }}</span>
                                </label>
                            @empty
                                <p class="text-muted" style="margin:0;">Nenhuma tag cadastrada.</p>
                            @endforelse
                        </div>
                    </div>

                    @if ($this->selectedTags->isNotEmpty())
                        <div style="margin-top:10px; display:flex; gap:8px; flex-wrap:wrap;">
                            @foreach ($this->selectedTags as $tag)
                                <span
                                    style="padding:6px 10px; border-radius:999px; background:#e5e7eb; color:#111827; font-size:13px;">
                                    #{{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    @error('tag_ids') <div class="error">{{ $message }}</div> @enderror
                    @error('tag_ids.*') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

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
                <button type="button" wire:click="saveDraft" class="btn btn-secondary">Salvar rascunho</button>
                <button type="button" wire:click="publish" class="btn btn-success">
                    {{ $this->isPubliclyVisible ? 'Atualizar publicação' : 'Publicar' }}
                </button>
            </div>
        </div>
    </form>

    @if ($showCategoryModal)
        <div
            style="position:fixed; inset:0; background:rgba(17,24,39,.55); display:flex; align-items:center; justify-content:center; z-index:9999;">
            <div class="card" style="width:100%; max-width:500px; margin:0;">
                <h2 style="margin-top:0;">Nova categoria</h2>

                <label for="newCategoryName">Nome</label>
                <input id="newCategoryName" type="text" wire:model.defer="newCategoryName">
                @error('newCategoryName') <div class="error">{{ $message }}</div> @enderror

                <div class="actions" style="margin-top: 12px;">
                    <button type="button" wire:click="closeCategoryModal" class="btn btn-secondary">Cancelar</button>
                    <button type="button" wire:click="saveNewCategory" class="btn btn-success">Adicionar</button>
                </div>
            </div>
        </div>
    @endif

    @if ($showTagModal)
        <div
            style="position:fixed; inset:0; background:rgba(17,24,39,.55); display:flex; align-items:center; justify-content:center; z-index:9999;">
            <div class="card" style="width:100%; max-width:500px; margin:0;">
                <h2 style="margin-top:0;">Nova tag</h2>

                <label for="newTagName">Nome</label>
                <input id="newTagName" type="text" wire:model.defer="newTagName">
                @error('newTagName') <div class="error">{{ $message }}</div> @enderror

                <div class="actions" style="margin-top: 12px;">
                    <button type="button" wire:click="closeTagModal" class="btn btn-secondary">Cancelar</button>
                    <button type="button" wire:click="saveNewTag" class="btn btn-success">Adicionar</button>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        window.blogEditorUploadConfig = {
            uploadUrl: @js(route('admin.blog.images.upload')),
            draftToken: @js($draft_token),
            postId: @js($postId),
            csrfToken: @js(csrf_token())
        };

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

                    editor.setData(@this.get('content') || '');

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