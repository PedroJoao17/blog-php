# README — Módulo de Blog

## Visão geral

Este projeto implementa um **módulo de Blog** dentro de uma aplicação Laravel já existente, com foco em **arquitetura escalável**, **boas práticas de organização**, **separação de responsabilidades** e **compatibilidade com evolução futura para produção real**.

O módulo foi construído sobre:

* **PHP 8.0.2+**
* **Laravel 9.19**
* **Livewire 2**
* **MySQL**
* **Laravel Mix**
* **CKEditor 5**

A proposta não foi criar apenas uma prova de conceito visual, mas sim uma base reaproveitável para o projeto real, já contemplando:

* administração de postagens
* categorias
* tags
* upload e gerenciamento de mídia
* editor rico com suporte a imagens no conteúdo
* sanitização do HTML
* área pública de leitura
* estrutura preparada para manutenção e crescimento

---

## Objetivos do módulo

O módulo de blog foi desenhado para atender dois cenários principais:

### 1. Área administrativa

Permitir que usuários autenticados possam:

* listar postagens
* criar postagens
* editar postagens
* excluir postagens
* salvar rascunhos
* publicar conteúdos
* agendar publicações
* definir categoria
* associar múltiplas tags
* definir imagem destacada
* inserir imagens dentro do conteúdo

### 2. Área pública

Permitir que visitantes possam:

* visualizar a listagem de postagens publicadas
* abrir posts individuais por slug
* buscar postagens
* filtrar por categoria
* filtrar por tag
* navegar por conteúdo relacionado

---

## Funcionalidades implementadas

### Administração

#### Autenticação

A área administrativa utiliza autenticação via **Laravel Breeze**.

Está implementado:

* login
* logout
* proteção de rotas com middleware `auth`
* redirecionamento do dashboard para o painel do blog

#### Postagens

CRUD administrativo de postagens com:

* título
* slug
* resumo (`excerpt`)
* conteúdo em HTML (`content`)
* status (`draft` e `published`)
* data de publicação (`published_at`)
* categoria
* múltiplas tags
* imagem destacada
* imagens no conteúdo
* autoria (`author_id`)

Também há diferenciação de estado no admin:

* **Rascunho**
* **Agendado**
* **Publicado**

#### Categorias

CRUD completo de categorias:

* listar
* buscar
* criar
* editar
* excluir
* slug automático

#### Tags

CRUD completo de tags:

* listar
* buscar
* criar
* editar
* excluir
* slug automático

---

### Área pública

#### Listagem do blog

A listagem pública exibe apenas postagens realmente visíveis ao público, ou seja:

* status `published`
* `published_at` preenchido
* `published_at <= now()`

A listagem atualmente suporta:

* busca textual
* filtro por categoria
* filtro por tag
* combinação de filtros
* paginação
* imagem destacada
* resumo
* dados do autor
* categoria clicável
* tags clicáveis

#### Página individual do post

A página individual exibe:

* título
* data de publicação
* autor
* categoria
* tags
* imagem destacada
* resumo
* conteúdo HTML renderizado
* postagens relacionadas

---

## Editor rico

O módulo utiliza **CKEditor 5** no formulário administrativo de postagens.

### O que foi implementado

* substituição da textarea simples por editor visual
* integração com Livewire via `wire:ignore`
* sincronização do HTML com a propriedade `content`
* inicialização resiliente do editor no frontend administrativo
* suporte a upload de imagens no conteúdo

### Toolbar atual

A configuração do editor cobre:

* heading
* bold
* italic
* underline
* strikethrough
* link
* blockquote
* listas
* tabela
* upload de imagem
* undo / redo

---

## Mídia

A mídia do módulo foi pensada separando claramente:

* **caminho físico** do arquivo no disco
* **URL pública** utilizada no HTML e nas views

### Tipos de mídia tratados

#### 1. Imagem destacada

Usada como capa do post.

Armazenamento:

* diretório: `blog/posts/featured`
* disco: `public`

Persistência:

* campo `featured_image` em `blog_posts`
* registro completo em `blog_media`

#### 2. Imagens do conteúdo

Usadas dentro do corpo do post via CKEditor 5.

Armazenamento:

* diretório: `blog/posts/content`
* disco: `public`

Persistência:

* URL inserida no HTML do campo `content`
* registro em `blog_media` com `collection = content`

### Draft token

Como o usuário pode subir imagens no editor antes mesmo de salvar o post, o módulo usa um `draft_token`.

Esse token permite:

* registrar uploads temporários em `blog_media`
* devolver URL definitiva imediatamente ao CKEditor
* associar as mídias ao post real no momento do salvamento

### Gestão robusta de mídia

Foi implementado:

* associação de mídias temporárias ao post salvo
* sincronização entre o HTML do conteúdo e os registros de mídia
* remoção física e lógica de mídia excluída do conteúdo
* exclusão de mídias ao excluir o post
* limpeza de drafts abandonados por comando Artisan

### Comando de limpeza

Comando disponível:

```bash
php artisan blog:media:cleanup-drafts
```

Função:

* remove uploads de conteúdo nunca associados a um post
* apaga do banco e do disco

---

## Sanitização do HTML

Como o conteúdo do blog é salvo em HTML, foi implementada sanitização no backend usando **mews/purifier**.

### Objetivo

Garantir que:

* o HTML válido do editor seja preservado
* scripts maliciosos não entrem no banco
* atributos inseguros, como `onclick`, sejam removidos

### Estrutura adotada

* perfil customizado `blog_post_content` em `config/purifier.php`
* service dedicado `HtmlContentSanitizer`
* sanitização aplicada antes do persist no `PostService`

### O que é permitido

Exemplos de elementos preservados:

* `p`
* `strong`
* `em`
* `ul`, `ol`, `li`
* `blockquote`
* `h2`, `h3`, `h4`
* `a`
* `img`
* `table`
* `thead`, `tbody`, `tr`, `th`, `td`

### O que é bloqueado

Exemplos:

* `<script>`
* atributos inline inseguros
* HTML fora da whitelist definida

---

## Organização interna da arquitetura

O módulo foi evoluído para sair de uma abordagem concentrada em componentes Livewire e migrar para uma estrutura baseada em services.

### Services existentes

#### `MediaService`

Responsável por:

* armazenar imagem destacada
* remover imagem destacada
* armazenar imagem de conteúdo
* apagar arquivo físico e registro de mídia
* associar uploads temporários ao post
* sincronizar mídias com o HTML salvo
* limpar drafts órfãos

#### `HtmlContentSanitizer`

Responsável por:

* limpar o HTML do conteúdo antes de salvar
* aplicar o perfil `blog_post_content`

#### `PostService`

Responsável por:

* normalizar dados do post
* sanitizar conteúdo
* aplicar regra de publicação
* criar/editar post
* sincronizar tags
* vincular mídias temporárias
* sincronizar mídias do conteúdo
* tratar imagem destacada
* excluir post com suas mídias

### Resultado arquitetural

Com isso:

* `PostForm` ficou mais fino
* `PostIndex` ficou mais fino
* `BlogImageUploadController` ficou mais fino
* regras de negócio ficaram centralizadas
* a manutenção futura ficou mais simples

---

## Estrutura do banco de dados

### Tabela `blog_posts`

Campos principais:

* `id`
* `author_id`
* `category_id`
* `title`
* `slug`
* `excerpt`
* `content`
* `featured_image`
* `status`
* `published_at`
* `created_at`
* `updated_at`

### Tabela `blog_categories`

Campos principais:

* `id`
* `name`
* `slug`
* `created_at`
* `updated_at`

### Tabela `blog_tags`

Campos principais:

* `id`
* `name`
* `slug`
* `created_at`
* `updated_at`

### Tabela `blog_post_tag`

Tabela pivô para relacionamento muitos-para-muitos entre posts e tags.

### Tabela `blog_media`

Campos principais:

* `id`
* `attachable_type`
* `attachable_id`
* `collection`
* `disk`
* `directory`
* `filename`
* `path`
* `url`
* `mime_type`
* `extension`
* `size`
* `uploaded_by`
* `draft_token`
* `created_at`
* `updated_at`

---

## Fluxos principais

### Fluxo de criação de post

1. usuário autenticado acessa o formulário
2. informa título, slug, resumo, categoria e tags
3. escreve conteúdo no CKEditor
4. sobe imagem destacada, se quiser
5. sobe imagens no conteúdo, se quiser
6. imagens de conteúdo são registradas em `blog_media` com `draft_token`
7. ao salvar, o conteúdo é sanitizado
8. o post é persistido
9. tags são sincronizadas
10. mídias temporárias são vinculadas ao `post_id`
11. mídias não presentes no HTML final são removidas

### Fluxo de edição de post

1. sistema carrega o post
2. carrega categoria, tags, conteúdo e imagem destacada
3. usuário altera conteúdo
4. novas imagens podem ser enviadas
5. ao salvar, o HTML é sanitizado
6. o sistema sincroniza tags
7. o sistema sincroniza mídia com o conteúdo final
8. mídias removidas do HTML são apagadas

### Fluxo de exclusão de post

1. usuário exclui o post no admin
2. `PostService` remove mídias relacionadas
3. arquivos físicos são apagados
4. registros de mídia são apagados
5. post é removido

---

## Publicação e visibilidade

O módulo diferencia claramente os estados de publicação.

### Rascunho

* `status = draft`
* não aparece no frontend

### Agendado

* `status = published`
* `published_at` em data futura
* não aparece no frontend
* aparece no admin como **Agendado**

### Publicado

* `status = published`
* `published_at <= now()`
* aparece no frontend
* aparece no admin como **Publicado**

### Helpers implementados

No model `Post`:

* `isScheduled()`
* `isPubliclyVisible()`

No formulário administrativo:

* visibilidade do botão “Ver pública” só para post realmente público
* indicação visual do estado atual

---

## Rotas principais

### Rotas públicas

* `GET /blog`
* `GET /blog/{slug}`

### Rotas administrativas

* `GET /admin/blog/posts`
* `GET /admin/blog/posts/create`
* `GET /admin/blog/posts/{post}/edit`
* `GET /admin/blog/categories`
* `GET /admin/blog/categories/create`
* `GET /admin/blog/categories/{category}/edit`
* `GET /admin/blog/tags`
* `GET /admin/blog/tags/create`
* `GET /admin/blog/tags/{tag}/edit`
* `POST /admin/blog/images/upload`

### Rotas de autenticação

Geradas pelo Breeze:

* `/login`
* `/logout`
* `/register`
* demais rotas de autenticação padrão do Breeze

---

## Frontend público

### Funcionalidades implementadas

* listagem pública paginada
* busca por termo
* filtro por categoria
* filtro por tag
* combinação de filtros
* categoria clicável
* tags clicáveis
* postagens relacionadas
* imagem destacada nas listagens
* refinamento da página do post

### Critério de exibição pública

A listagem e a página individual só retornam posts que passaram no escopo `published()`.

---

## Segurança

### Itens já cobertos

* autenticação no admin
* sanitização do HTML antes de persistir
* proteção contra conteúdo HTML inseguro
* upload de imagens validado no backend
* separação entre mídia pública e lógica do editor
* exclusão de arquivos e registros órfãos

### Boas práticas já adotadas

* uso de services para regra crítica
* `wire:ignore` no editor rico
* gravação de URLs públicas no HTML
* não exposição de caminhos internos do servidor

---

## Instalação e setup

### Dependências principais

Instalar dependências PHP:

```bash
composer install
```

Instalar dependências JS:

```bash
npm install
```

Compilar assets:

```bash
npm run dev
```

### Banco de dados

Criar banco MySQL e configurar `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

Rodar migrations:

```bash
php artisan migrate
```

### Storage

Criar link simbólico para arquivos públicos:

```bash
php artisan storage:link
```

### Login

Usuário administrador criado via Tinker ou seed.

---

## Comandos úteis

### Servidor local

```bash
php artisan serve
```

### Limpar cache

```bash
php artisan optimize:clear
```

### Limpeza de drafts de mídia

```bash
php artisan blog:media:cleanup-drafts
```

### Tinker

```bash
php artisan tinker
```

---

## O que o projeto cobre até aqui

O módulo cobre atualmente:

* autenticação administrativa
* CRUD de posts
* CRUD de categorias
* CRUD de tags
* editor rico com CKEditor 5
* imagem destacada
* imagens no conteúdo
* gestão de mídia temporária e definitiva
* sanitização de HTML
* listagem pública
* detalhe do post
* busca pública
* filtros por categoria e tag
* posts relacionados
* organização interna em services

Em outras palavras, o blog já possui uma base funcional e arquitetural forte, próxima de um mini CMS editorial.

---

## O que ainda pode evoluir

Embora o núcleo esteja muito consistente, ainda existem melhorias que podem ser implementadas futuramente:

### SEO avançado

* `meta_title`
* `meta_description`
* Open Graph completo
* canonical por post

### Revisão editorial

* status adicionais
* workflow de aprovação
* histórico de revisão

### Biblioteca de mídia

* painel de mídias reutilizáveis
* busca por arquivos
* visualização centralizada

### Melhorias públicas adicionais

* páginas dedicadas de categoria
* páginas dedicadas de tag
* breadcrumbs
* destaque de posts recentes ou populares

### Testes automatizados

* testes de criação e edição de posts
* testes de upload
* testes de sanitização
* testes de filtros públicos
* testes de limpeza de mídia

---

## Estado atual do módulo

O módulo já pode ser considerado uma base real de projeto, não apenas uma demo temporária.

Ele foi construído com foco em:

* compatibilidade com Laravel 9.19 e PHP 8.0.2+
* organização desacoplada
* manutenção futura
* expansão gradual sem refatoração estrutural grande

---

## Conclusão

Este módulo de blog já entrega uma base sólida para produção em um sistema Laravel com Livewire, cobrindo o ciclo editorial completo desde a criação do conteúdo até sua publicação pública, com editor rico, gerenciamento de mídia, categorização, tags, sanitização e separação clara de responsabilidades na arquitetura.

A estrutura foi desenhada para continuar crescendo com segurança, sem jogar fora o que já foi implementado, e serve tanto como base funcional imediata quanto como fundação para evoluções futuras.
