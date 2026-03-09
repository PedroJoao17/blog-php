<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Blog' }}</title>
    @livewireStyles
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f4f6;
            color: #111827;
            margin: 0;
        }

        header {
            background: #111827;
            color: white;
            padding: 18px 0;
            margin-bottom: 24px;
        }

        .container {
            width: 92%;
            max-width: 1100px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 24px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
            margin-bottom: 20px;
        }

        .row {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .col {
            flex: 1;
            min-width: 260px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-sizing: border-box;
            margin-bottom: 14px;
        }

        textarea {
            min-height: 160px;
            resize: vertical;
        }

        .btn {
            display: inline-block;
            padding: 10px 14px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #111827;
        }

        .btn-success {
            background: #059669;
            color: white;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            text-align: left;
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .text-muted {
            color: #6b7280;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-draft {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-published {
            background: #d1fae5;
            color: #065f46;
        }

        .error {
            color: #b91c1c;
            font-size: 13px;
            margin-top: -8px;
            margin-bottom: 10px;
        }

        .flash {
            background: #d1fae5;
            color: #065f46;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 16px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        a {
            color: #2563eb;
        }
    </style>
</head>

<body>
    <header>
        <div class="container topbar">
            <strong>Admin do Blog</strong>
            <div>
                <a href="{{ route('blog.index') }}" style="color:white;">Ver blog público</a>
            </div>
        </div>
    </header>

    <main class="container">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    @livewireScripts
</body>

</html>