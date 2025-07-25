<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <style>
        /* NOTE use custom font from public folder */
        @font-face {
            font-family: 'Bitcount';
            src: url("{{ asset('fonts/Bitcount-VariableFont_CRSV,ELSH,ELXP,slnt,wght.ttf') }}") format('truetype');       
        }
 
        @font-face {
            font-family: 'DancingScript';
            src: url("{{ asset('fonts/DancingScript-VariableFont_wght.ttf') }}") format('truetype');
        }
    </style>
    <title>Karaoke</title>
</head>
<body>
    <div id="app"></div>
    <script src="{{asset('js/app.js')}}"></script>
</body>
</html>