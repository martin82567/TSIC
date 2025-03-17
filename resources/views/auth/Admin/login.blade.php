<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Stock in Children</title>
    <!--Css-->
    <link rel="stylesheet" type="text/css" href="<?php echo url('assets/'); ?>/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo url('assets/'); ?>/css/font-awesome.min.css">
    <!--Custom CSS-->
    <link rel="stylesheet" href="<?php echo url('assets/'); ?>/css/style.css" type="text/css">
    <link rel="stylesheet" href="<?php echo url('assets/'); ?>/css/media.css" type="text/css">
    <!--jQuery-->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
</head>

<body>
    <div class="login-sec clearfix">
        <div class="circle"><img src="<?php echo url('assets/'); ?>/images/yellow_circle.png" alt=""></div>
        <div class="row m-0">
         
            <div class="col-lg-6 order-lg-2 p-0">
                <div class="inner">
                    <div class="login-form">
                        <div class="login-logo">
                            <img src="<?php echo url('assets/'); ?>/images/logo_login.png" style="width: 250px;" alt="">
                        </div>
                        <div class="inr">

                            <h3><span>LOGIN</span></h3>
                            <form class="" method="POST" action="{{ route('admin.login.submit') }}">
                                {{ csrf_field() }}
                                <div class="single-field has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                                    <i class="fa fa-user"></i>
                                    <input id="email" type="email" placeholder="Email or User Name" class="form-control" name="email" value="{{ old('email') }}" required autofocus> @if ($errors->has('email'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span> @endif
                                </div>
                                <div class="single-field has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                                    <i class="fa fa-lock"></i>
                                    <input id="password" type="password" placeholder="Password" class="form-control" name="password" required> @if ($errors->has('password'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span> @endif
                                </div>
                                <div class="text-center pt-4">
                                    <button type="submit" class="login-button">LOGIN</button>
                                </div>
                                <!--<div class="forget-pass">
                            <a href="#">Forgot Password ?</a>
                        </div>-->
                            </form>
                        </div>
                    </div>

                </div>
            </div>
            
               <div class="col-lg-6 p-0">
                <div class="left">
                    <div class="image">
                        <img src="<?php echo url('assets/'); ?>/images/login_bg.png" alt="">
                    </div>
                    <p class="copyright">Copyright © 2007-
                        <?php echo date('Y'); ?>
                        <?php echo env('APP_COMPANY_NAME');?>.</p>
                </div>
            </div>

        </div>
    </div>
    <!--Js-->
    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/bootstrap.bundle.min.js"></script>
    <!--Custom JS-->
    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/script.js"></script>
    
</body>

</html>
