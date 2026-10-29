<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🧧</text></svg>">
    <title>Gachapon Management</title>

    @vite(['resources/css/app.css', 'resources/js/management.js'])
</head>
<body>
    <div
        id="gachapon-management"
        data-token="{{ $managementToken }}"
    ></div>
</body>
</html>
```