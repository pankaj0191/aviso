<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name'))</title>
    
    <meta property="og:url" content="{{ url()->full() }}" />
    <meta property="og:title" content="@yield('title', config('app.name'))" />
    <meta property="og:description" content="@yield('description', config('app.name'))" />
    <meta property="og:image" content="@yield('image', asset('img/prooflo_og.jpg'))" />
    {{-- <meta property="og:type" content="article" /> --}}

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="{{ asset('assets/css/settings.css') }}">

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-139572196-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-139572196-1');
    </script>

    <style>
        body, html {
            font-family: "Open Sans", Helvetica, Arial, sans-serif;
            background: url('<?php echo env('APP_SPACES_PREFIX'); ?>/assets/img/spark-bg.png');
            background-repeat: repeat;
            background-size: 300px 200px;
            height: 100%;
            margin: 0;
        }

        .full-height {
            min-height: 100%;
        }

        .flex-column {
            display: flex;
            flex-direction: column;
        }

        .flex-fill {
            flex: 1;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .flex-fill.flex-center {
            padding: 0 15px;
        }
        .flex-fill.flex-center img {
            width: 100%;
        }

        @media (max-width: 750px) {
            body, html {
                background: none;
            }
            .full-height.flex-column {
                flex-direction: column-reverse;
                align-items: center;
                justify-content: center;
            }
            .flex-fill.flex-center {
                flex: unset;
                padding: 0 15px;
                margin-bottom: 100px;
            }
        }


        .text-center {
            text-align: center;
        }

        .links {
            padding: 1em;
            text-align: right;
        }

        .links a {
            text-decoration: none;
        }

        .links button {
            background-color: #3097D1;
            border: 0;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            font-family: 'Open Sans';
            font-size: 14px;
            font-weight: 600;
            padding: 15px;
            text-transform: uppercase;
            width: 100px;
        }

        .register-status {
            color: #80B4A0;
            display: block;
            border: 2px solid #80B4A0;
            padding: 10px;
            border-radius: 10px;
        }

        .bg-white {
            background-color:#fff;
        }
        .msg-box {
            /* box-shadow: 0 4px 8px 0 rgba(0,0,0,0.12), 0 2px 4px 0 rgba(0,0,0,0.08); */
            color:#8795A1;
        }
        .msg-box h1 {
            color:#606F7B;
            margin-bottom: 0px;
        }
    </style>
</head>
<body>
    <div class="full-height flex-column">
        @if (session('message'))
            @php
                $message = json_decode(session('message'));                
            @endphp
            <div class="flex-fill flex-center flex-column bg-white">
                <div class="msg-box">
                    @if ($message->title)
                        <h1 class="text-center">{{ $message->title }}</h1>  
                    @endif
                    @if ($message->body)
                        <p class="text-center">{{ $message->body }}</p>
                    @endif
                    <div class="links" style="text-align:center;">
                        <a href="/projects">
                            <button class="brn btn-primary" style="width:auto;">
                                Show Projects
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <nav class="links">
                <a href="/login" style="margin-right: 15px;">
                    <button class="brn btn-primary">
                        Login
                    </button>
                </a>

                <a href="/register">
                    <button class="brn btn-primary">
                        Register
                    </button>
                </a>
            </nav>

                
            <div class="flex-fill flex-center">
                <h1 class="text-center">
                    <img src="{{env('APP_SPACES_PREFIX')}}/assets/img/prooflo-big.png">
                </h1>
            </div>
        @endif
    </div>
</body>
</html>
