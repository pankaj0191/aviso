@extends('spa-modules.layouts.login')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-10 col-xs-offset-1 col-sm-12 col-sm-offset-0  col-md-12 col-md-offset-0 col-lg-12 col-lg-offset-0 login">
            <div class="row login-card">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 login-form">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
                            <div class="logo">
                                <!-- Branding Image -->
                                <img src="https://app-prooflo.nyc3.digitaloceanspaces.com/assets/img/prooflo.png">
                            </div>
        
                            <div class="login-title">Login</div>
        
                            <div class="login-content">Let’s start to manage your work as a…..</div>

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <strong>{{__('Whoops!')}}</strong> {{__('Something went wrong!')}}
                                    <br><br>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif


                        @if (session('warning'))
                            <div class="alert alert-danger" style="text-align: center">
                                {{ session('warning') }}
                            </div>
                            @endif
        
                            @if (session('success'))
                            <div class="alert alert-success" style="text-align: center">
                                {{ session('success') }}
                            </div>
                            @endif
        
                            <form class="form-horizontal" role="form" method="POST" action="/login">
                                {{ csrf_field() }}
        
                                <!-- E-Mail Address -->
                                <div class="form-group">
                                    <label class="col-md-12 label-form">Email</label>
        
                                    <div class="col-md-12">
                                        <input type="email" class="form-control prooflo-input" name="email" value="{{ old('email') }}"
                                            autofocus>
                                    </div>
                                </div>
        
                                <!-- Password -->
                                <div class="form-group">
                                    <label class="col-md-12 label-form">Password</label>
        
                                    <div class="col-md-12">
                                        <input type="password" class="form-control prooflo-input" name="password">
                                    </div>
                                </div>
        
                                <!-- Remember Me -->
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> Remember Me
                                    </label>
                                </div>
        
                                <!-- Login Button -->
                                <button type="submit" class="btn btn-primary btn-block btn-login">
                                    <i class="fa m-r-xs fa-sign-in"></i>Login
                                </button>
                                <a class="btn btn-link prooflo-text" href="{{ url('/password/reset') }}">Forgot your
                                    password?</a>
                                    <div class="registration-link">
                                        <a class="btn btn-link prooflo-text" href="{{ url('/register') }}">I don’t have an account</a>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="hidden-xs hidden-sm col-md-6 col-lg-6 login-image">
                    <div class="image">
                        <svg id="undraw_secure_login_pdn4" xmlns="http://www.w3.org/2000/svg" width="356" height="247"
                            viewBox="0 0 356 247">
                            <ellipse id="Ellipse_17" data-name="Ellipse 17" cx="70.943" cy="5.388" rx="70.943"
                                ry="5.388" transform="translate(0 236.224)" fill="#e6e6e6" />
                            <path id="Path_77" data-name="Path 77"
                                d="M324.272,250.206c12.346-5.251,27.724-1.924,42.741.385.14-2.8,1.834-6.2.059-8.151-2.156-2.37-1.957-4.861-.66-7.365,3.317-6.405-1.435-13.22-6.234-18.887a10.628,10.628,0,0,0-8.87-3.723l-8.889.635a10.646,10.646,0,0,0-9.453,7.609h0c-2.123,2.887-3.259,5.766-2.543,8.629-3.179,2.173-3.713,4.8-2.285,7.752a4.456,4.456,0,0,1-.055,5.436,25.1,25.1,0,0,0-3.731,7.411Z"
                                transform="translate(-270.056 -195.238)" fill="#2f2e41" />
                            <path id="Path_78" data-name="Path 78"
                                d="M658.569,630.987H406.937a8.437,8.437,0,0,1-8.437-8.437h0q136.825-15.9,268.506,0h0A8.437,8.437,0,0,1,658.569,630.987Z"
                                transform="translate(-311.006 -417.558)" fill="#2f2e41" />
                            <path id="Path_79" data-name="Path 79"
                                d="M667.006,413.546l-268.506-.5,31.119-52.361.149-.248v-115.4a10.665,10.665,0,0,1,10.667-10.666H623.584a10.665,10.665,0,0,1,10.666,10.667V361.234Z"
                                transform="translate(-311.006 -207.327)" fill="#3f3d56" />
                            <path id="Path_80" data-name="Path 80"
                                d="M487.087,250.957a3.478,3.478,0,0,0-3.474,3.474V359.65a3.478,3.478,0,0,0,3.474,3.474H672.212a3.478,3.478,0,0,0,3.474-3.474V254.431a3.478,3.478,0,0,0-3.474-3.474Z"
                                transform="translate(-357.903 -216.463)" fill="#fff" />
                            <path id="Path_81" data-name="Path 81"
                                d="M479.484,531.718a1.493,1.493,0,0,0-1.353.867l-9.589,20.845a1.489,1.489,0,0,0,1.353,2.111H671.541a1.489,1.489,0,0,0,1.331-2.155l-10.423-20.845a1.481,1.481,0,0,0-1.331-.823Z"
                                transform="translate(-349.524 -371.685)" fill="#2f2e41" />
                            <circle id="Ellipse_18" data-name="Ellipse 18" cx="2.233" cy="2.233" r="2.233"
                                transform="translate(218.793 28.29)" fill="#fff" />
                            <path id="Path_82" data-name="Path 82"
                                d="M646.921,593.619a1.491,1.491,0,0,0-1.438,1.1l-2.405,8.934a1.489,1.489,0,0,0,1.438,1.876h45.742a1.489,1.489,0,0,0,1.407-1.976l-3.092-8.934a1.49,1.49,0,0,0-1.407-1Z"
                                transform="translate(-445.717 -405.851)" fill="#2f2e41" />
                            <path id="Path_83" data-name="Path 83" d="M468.937,337.135v1.985H264.306l.154-.248v-1.737Z"
                                transform="translate(-145.693 -186.21)" fill="#2f2e41" />
                            <circle id="Ellipse_19" data-name="Ellipse 19" cx="34.742" cy="34.742" r="34.742"
                                transform="translate(282.794 0)" fill="#80b4a0" />
                            <path id="Path_84" data-name="Path 84"
                                d="M905.129,259.034H876.343a1.987,1.987,0,0,1-1.985-1.985V239.678a1.987,1.987,0,0,1,1.985-1.985h28.786a1.987,1.987,0,0,1,1.985,1.985v17.371a1.987,1.987,0,0,1-1.985,1.985Zm-28.786-19.356v17.371h28.788V239.678Z"
                                transform="translate(-573.2 -209.154)" fill="#fff" />
                            <path id="Path_85" data-name="Path 85"
                                d="M908.805,220.188H890.938v-7.941c0-5.752,3.757-9.926,8.934-9.926s8.934,4.175,8.934,9.926ZM892.923,218.2h13.9v-5.956c0-4.676-2.857-7.941-6.948-7.941s-6.948,3.266-6.948,7.941Z"
                                transform="translate(-582.336 -189.665)" fill="#fff" />
                            <circle id="Ellipse_20" data-name="Ellipse 20" cx="1.985" cy="1.985" r="1.985"
                                transform="translate(315.55 36.727)" fill="#fff" />
                            <path id="Path_86" data-name="Path 86"
                                d="M678,371.23H534.929a2.653,2.653,0,0,1-2.649-2.649v-35.49a2.653,2.653,0,0,1,2.649-2.649H678a2.653,2.653,0,0,1,2.649,2.649v35.49A2.653,2.653,0,0,1,678,371.23ZM534.929,331.5a1.591,1.591,0,0,0-1.59,1.59v35.49a1.591,1.591,0,0,0,1.59,1.59H678a1.591,1.591,0,0,0,1.59-1.59v-35.49A1.591,1.591,0,0,0,678,331.5Z"
                                transform="translate(-384.7 -260.506)" fill="#e6e6e6" />
                            <circle id="Ellipse_21" data-name="Ellipse 21" cx="10.793" cy="10.793" r="10.793"
                                transform="translate(161.681 78.17)" fill="#e6e6e6" />
                            <path id="Path_87" data-name="Path 87"
                                d="M641,356.805a1.8,1.8,0,1,0,0,3.6h84.8a1.8,1.8,0,0,0,0-3.6Z"
                                transform="translate(-443.612 -275.03)" fill="#e6e6e6" />
                            <path id="Path_88" data-name="Path 88"
                                d="M641,380.842a1.8,1.8,0,1,0,0,3.6h36.489a1.8,1.8,0,0,0,0-3.6Z"
                                transform="translate(-443.605 -288.307)" fill="#e6e6e6" />
                            <path id="Path_89" data-name="Path 89"
                                d="M434.156,363.54,407.708,377.6l-.335-11.717a78.328,78.328,0,0,0,24.439-8.035l2.766-6.834a4.633,4.633,0,0,1,7.874-1.2h0a4.633,4.633,0,0,1-.408,6.317Z"
                                transform="translate(-315.851 -270.261)" fill="#ffb8b8" />
                            <path id="Path_90" data-name="Path 90"
                                d="M322.874,541.508h0a5.2,5.2,0,0,1,.66-4.2l5.81-8.916a10.153,10.153,0,0,1,13.172-3.475h0c-2.442,4.156-2.1,7.8.843,10.971a52.817,52.817,0,0,0-12.543,8.551,5.2,5.2,0,0,1-7.941-2.927Z"
                                transform="translate(-269.183 -367.292)" fill="#2f2e41" />
                            <path id="Path_91" data-name="Path 91"
                                d="M409.889,489.372h0a9.969,9.969,0,0,1-8.473,4.84l-38.6.292-1.674-9.709,17.074-5.022-14.4-11.048,15.735-18.413,28.579,26.637A9.969,9.969,0,0,1,409.889,489.372Z"
                                transform="translate(-290.376 -326.752)" fill="#2f2e41" />
                            <path id="Path_92" data-name="Path 92"
                                d="M330.322,522.9h-9.039c-8.116-55.631-16.377-111.418,8.035-132.24l28.791,4.687-3.683,24.439L338.356,438.2Z"
                                transform="translate(-263.89 -294.172)" fill="#2f2e41" />
                            <path id="Path_93" data-name="Path 93"
                                d="M346.287,693.619h0a5.2,5.2,0,0,1-4.2-.659l-9.815-1.319a10.153,10.153,0,0,1-3.476-13.171h0c4.156,2.441,7.8,2.1,10.971-.844,2.239,4.52,5.927,4.245,9.45,8.053a5.2,5.2,0,0,1-2.926,7.941Z"
                                transform="translate(-271.92 -452.285)" fill="#2f2e41" />
                            <path id="Path_94" data-name="Path 94"
                                d="M378.63,279.555l-16.739-4.017c2.78-5.691,3.009-12.023,1.674-18.748l11.383-.335A87.415,87.415,0,0,0,378.63,279.555Z"
                                transform="translate(-290.786 -219.492)" fill="#ffb8b8" />
                            <path id="Path_95" data-name="Path 95"
                                d="M370.957,349.559c-12.185,8.3-20.8.284-27.366-12.089.913-7.571-.565-16.632-3.3-26.476a18.022,18.022,0,0,1,11-21.733h0l14.4,6.026c12.224,9.964,14.63,20.756,10.044,32.139Z"
                                transform="translate(-278.511 -237.774)" fill="#80b4a0" />
                            <path id="Path_96" data-name="Path 96"
                                d="M294.458,309.7l-13.391,7.03,23.77,14.061,3.307,8.158a4.327,4.327,0,0,1-2.6,5.716h0a4.327,4.327,0,0,1-5.73-3.837l-.334-5.686-30.211-9.97a7.064,7.064,0,0,1-4.433-4.316h0a7.064,7.064,0,0,1,2.65-8.218l24.293-16.666Z"
                                transform="translate(-237.065 -241.467)" fill="#ffb8b8" />
                            <path id="Path_97" data-name="Path 97"
                                d="M338.961,306.7c-5.585-2.448-10.664.211-17.074,2.343l-1-17.744c6.365-3.393,12.434-4.307,18.078-1.674Z"
                                transform="translate(-268.177 -237.094)" fill="#80b4a0" />
                            <circle id="Ellipse_22" data-name="Ellipse 22" cx="10.687" cy="10.687" r="10.687"
                                transform="translate(66.765 24.377)" fill="#ffb8b8" />
                            <path id="Path_98" data-name="Path 98"
                                d="M375.3,237.456c-10.581,1.036-18.644-.694-23.8-5.622v-3.968h22.9Z"
                                transform="translate(-285.059 -203.74)" fill="#2f2e41" />
                        </svg>
                    </div>
                    <div class="content">
                        <div class="content-head">Prooflo is the world’s simplest creative project management tool.
                        </div>
                        <div class="content-body">It’s so easy your grandmother can use it.</div>
                        <div class="content-foot">Easy to manage creative projects, client review, and teams.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('style')
    <style lang="scss">
        .login {
            padding-top: 24px;
            padding-bottom: 24px;
        }

        .login-card {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            border-radius: 25px;
        }

        @media (max-width: 768px) {
            .login-card {
                border-radius: 16px;
            }
        }

        .login-form {
            padding: 24px 0;
            position: relative;
        }

        @media (max-width: 768px) {
            .login-form {
                padding: 0px 0px 16px 0px !important;
                position: relative;
            }
        }

        .login-form .logo {
            text-align: left;
            padding-top: 24px;
            padding-bottom: 24px;
        }

        
        .login-form .logo img {
            height: 42px;
        }

        @media (max-width: 768px) {
            .login-form .logo {
                text-align: center
            }
            .login-form .logo img {
                height: 64px;
            }
        }

        .login-form .alert {
            padding: 8px 16px;
            margin-bottom: 22px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .login-form .alert-danger {
            color: #a94442;
            background-color: #fff5f5;
            border-color: #ebccd1;
        }

        .login-form .login-title {
            font-size: 18px;
            color: #718096;
            font-weight: 500;
            text-align: left;
            padding-bottom: 12px;
        }

        .login-form .login-content {
            font-size: 12px;
            color: #A0AEC0;
            text-align: left;
            padding-bottom: 24px;
        }

        .login-form .label-form {
            text-align: left;
            font-weight: 300;
            color: #718096;
            margin-bottom: 8px;
        }

        .login-form .form-group {
            margin-bottom: 16px;
        }

        .login-form .form-group .prooflo-input {
            border: 1px solid #E2E8F0;
            box-shadow: none;
            background-color: #F7FAFC;
        }

        .login-form .form-group .prooflo-input:focus {
            border: 1px solid #80B4A0;
            background-color: #fff;
        }

        .login-form .checkbox {
            margin-bottom: 24px;
        }

        .login-form .btn-login {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 4px;
        }

        .login-form .btn-login:hover {
            background-color: #3C896C !important;
        }

        .login-form .btn-login:focus {
            outline: none;
        }

        .login-form .prooflo-text {
            font-weight: 400;
            padding: 12px 0 0 0;
            text-transform: inherit;
            width: 100%;
        }

        .login-form .registration-link .prooflo-text {
            font-weight: 400;
            padding: 12px 0 0 0;
            text-transform: inherit;
            width: 100%;
            font-size: 12px;
        }

        .login-card .login-image {
            text-align: center;
            background-color: #EAF4F0;
            border-top-right-radius: 25px;
            border-bottom-right-radius: 25px;
        }

        .login-card .login-image .image {
            padding-top: 120px;
            padding-bottom: 24px;
        }

        .login-card .login-image .content {
            padding-bottom: 108px;
        }

        .login-card .login-image .content .content-head {
            color: #3C896C;
            width: 240px;
            margin: auto;
            padding-bottom: 12px;
            font-size: 12px;
        }

        .login-card .login-image .content .content-body {
            color: #58585A;
            font-weight: 500;
            font-size: 18px;
            padding-bottom: 24px;
        }

        .login-card .login-image .content .content-foot {
            color: #718096;
            width: 270px;
            margin: 0 auto;
            font-size: 12px;
        }

        .with-navbar {
            padding: 0;
        }
    </style>
    @endsection