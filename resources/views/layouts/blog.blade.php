<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Blog' }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f5f5;
            color: #222;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 0 auto;
        }

        header {
            background: #1f2937;
            color: #fff;
            padding: 20px 0;
            margin-bottom: 30px;
        }

        header h1 {
            margin: 0;
            font-size: 28px;
        }

        header a {
            color: #fff;
            text-decoration: none;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
        }

        .post-title {
            margin-top: 0;
            margin-bottom: 8px;
        }

        .post-title a {
            color: #111827;
            text-decoration: none;
        }

        .meta {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .excerpt {
            line-height: 1.6;
        }

        .featured-image {
            width: 100%;
            max-height: 420px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .content {
            line-height: 1.8;
        }

        .content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .content table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .content table th,
        .content table td {
            border: 1px solid #d1d5db;
            padding: 10px;
            text-align: left;
        }

        .content blockquote {
            border-left: 4px solid #d1d5db;
            padding-left: 16px;
            color: #4b5563;
            margin: 20px 0;
        }

        .content h2,
        .content h3,
        .content h4 {
            margin-top: 28px;
            margin-bottom: 12px;
        }

        .content p,
        .content ul,
        .content ol {
            margin-bottom: 16px;
        }

        .pagination {
            margin-top: 20px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #2563eb;
            text-decoration: none;
        }

        .header-nav {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .header-link {
            color: #fff;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            transition: background 0.2s ease;
        }

        .header-link:hover {
            background: rgba(255, 255, 255, 0.12);
        }
    </style>
    @livewireStyles
</head>

<body>
    <header>
        <div class="container"
            style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap;">
            <h1><a href="{{ route('blog.index') }}">Blog</a></h1>

            <nav class="header-nav">
                <a href="{{ route('blog.index') }}" class="header-link">Início</a>
                <a href="{{ route('admin.blog.posts.index') }}" class="header-link">Posts</a>
            </nav>
        </div>
    </header>

    <main class="container">
        @yield('content')
    </main>

    @livewireScripts
</body>

</html>