<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ecommarce</title>
    @vite('spa/src/main.js')
    <link rel="stylesheet" href="http://localhost:5173/spa/src/styles/main.css">
</head>
<body>
    <div id="app"></div>
</body>
</html>