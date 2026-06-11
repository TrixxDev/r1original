<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — R1 Riepu Serviss</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="admin">
<header class="admin-header">
    <a class="admin-header__brand" href="{{ route('admin.pieraksts') }}">
        <img src="{{ asset('img/r1-riepas-logo-1515661637.jpg') }}" alt="R1">
        <span>Admin</span>
    </a>
    <nav class="admin-header__nav">
        <a href="{{ route('admin.pieraksts') }}" class="{{ request()->routeIs('admin.pieraksts*') ? 'active' : '' }}">Pieraksts</a>
        <a href="{{ route('pieraksts') }}" target="_blank">Publiskā lapa ↗</a>
    </nav>
</header>

<main class="admin-main">
    @yield('content')
</main>
</body>
</html>
