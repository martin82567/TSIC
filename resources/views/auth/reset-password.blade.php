@extends('layouts.appusers')
@section('content')
<div class="login-sec-appuser">
    <div class="container">
        <div class="row">
            <div class="col-xl-6 col-centered">
                <div class="inner">
                    <div class="login-form">
                        <div class="inr">
                            <?php if(!empty(session('message'))){ ?>
                            <div class="alert alert-<?php echo session('msg_class'); ?>" role="alert">
                                <?php echo session('message'); Session::forget('message'); ?>

                            </div>
                            <?php } ?>
                            <h3><span><center>RESET PASSWORD</center></span></h3>
                            <form class="" method="POST" action="{{ url('/submit_reset_password') }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="email" value="<?php echo urldecode($email); ?>">
                                <div class="single-field has-feedback">
                                    <i class="fa fa-lock"></i>
                                    <input id="otp" type="password" placeholder="Enter OTP" class="form-control" name="activation_code" required>
                                </div>
                                <div class="single-field has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                                    <i class="fa fa-lock"></i>
                                    <input id="password" type="password" placeholder="Enter New Password" class="form-control" name="password" required> @if ($errors->has('password'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span> @endif
                                </div>
                                <div class="text-center pt-4">
                                    <button type="submit" class="login-button">SUBMIT AND RESET</button>
                                </div>
                            </form>
                            <div class="forget-pass">
                                <a href="{{url('/login')}}">Login</a>
                            </div>
                            <strong><center>-or-</center></strong>
                            <div class="forget-pass">
                                <a href="{{ url('/forget-password') }}" onclick="event.preventDefault();
                                                 document.getElementById('resend-form').submit();">Resend OTP</a>
                                <form id="resend-form" action="{{ url('/submit_forget_password') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                    <input type="email" name="email" value="<?php echo urldecode($email); ?>">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
