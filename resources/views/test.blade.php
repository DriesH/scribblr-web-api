<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <style media="screen">
        img {
            position: relative;
            width: 500px;
            display: block;
        }

        .overlaag {
            position: absolute;
            z-index: 5;
            background-color: rgba({{$color[0]}},{{$color[1]}},{{$color[2]}}, 0.3);
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            display: block;

        }

        .container-ofzo {
            position: relative;
            display: block;
            width: 500px;
        }

        .kleurtjesssss {
            background-color: rgb({{$color[0]}},{{$color[1]}},{{$color[2]}});
            width: 500px;
            height: 500px;
        }

    </style>
</head>
<body>

<div class="container-ofzo">
    <span class="overlaag"></span>
    <img src="/test.jpg">
</div>

<div class="kleurtjesssss">

</div>

</body>
</html>
