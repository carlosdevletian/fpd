<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="stylesheet" href="{{ URL::to('css/FancyProductDesigner-all.min.css') }}">

         <!-- Scripts -->
        <script>
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
            ]); ?>
        </script>

        <title>FPD</title>
    </head>
    <body>
        <div>Colores: {{ count($palette) }}</div>

        @foreach($colors as $color)
            <div style="background-color: {{ $color['color'] }}; width: 50px; height: 50px; border: 1px solid black; display: inline-block"></div>
            <p style="display: inline-block">{{ number_format(($color['quantity']/$total)*100, 2) }}%</p>
        @endforeach

        <image src="{{ route('image') }}" style="border: 1px solid black"></image>

        


    </body>
</html>
