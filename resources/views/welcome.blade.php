<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    {{ $user->name }}
                </div>

                <div class="links">
                    <a href="">{{ $user->created_at->toDateString() }}</a><br>
                    <a href="">{{ $user->created_at->toFormattedDateString() }}</a><br>
                    <a href="">{{ $user->created_at->toTimeString() }}</a><br>
                    <a href="">{{ $user->created_at->toDateTimeString() }}</a><br>
                    <a href="">{{ $user->created_at->toDayDateTimeString() }}</a><br>
                    <a href="">{{ $user->created_at->toAtomString() }}</a><br>
                    <a href="">{{ $user->created_at->toIso8601ZuluString() }}</a><br>
                    <a href="">{{ $user->created_at->diffForHumans() }}</a><br>
                    <a href="">{{ $user->created_at->formatLocalized('%A %d %B %Y') }}</a><br>

                    <a href="">{{ $user->updated_at->format('l jS \\of F Y h:i:s A') }}</a><br>

                </div>
            </div>
        </div>
    </body>
</html>