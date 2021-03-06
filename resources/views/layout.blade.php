<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Panel</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600&display=swap" rel="stylesheet">
    <link href="{{ mix('app.css', 'vendor/panel') }}" rel="stylesheet" type="text/css">
</head>
<body>
	<div id="app"></div>

    <script>
        window.panel = {!! $panelVariables !!};
    </script>
    <script type="text/javascript" src="{{ mix('app.js', 'vendor/panel') }}"></script>
</body>
</html>