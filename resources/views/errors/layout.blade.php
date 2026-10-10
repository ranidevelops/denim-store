<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Indigo &amp; Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f7f8fa; }
        .code { font-size: 6rem; font-weight: 700; color: #1f3a5f; line-height: 1; }
        .btn-denim { background: #1f3a5f; border-color: #1f3a5f; color: #fff; }
        .btn-denim:hover { background: #14263f; border-color: #14263f; color: #fff; }
    </style>
</head>
{{-- Standalone on purpose: no database, session or other layout dependencies, so it still renders during outages. --}}
<body class="d-flex align-items-center min-vh-100">
    <main class="container text-center py-5">
        <div class="code">@yield('code')</div>
        <h1 class="h3 mt-3">@yield('heading')</h1>
        <p class="text-muted mb-4">@yield('message')</p>
        <a href="{{ url('/') }}" class="btn btn-denim">Back to the shop</a>
    </main>
</body>
</html>
