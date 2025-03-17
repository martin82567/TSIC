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
                            <h3><span><center>MENTEE FORGET PASSWORD</center></span></h3>
                            <form class="" method="POST" action="{{ url('/mentee/submit_forget_password') }}">
                                {{ csrf_field() }}
                                <div class="single-field has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                                    <i class="fa fa-user"></i>
                                    <input id="email" type="email" placeholder="Email" class="form-control" name="email" value="{{ old('email') }}" required autofocus> @if ($errors->has('email'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span> @endif
                                </div>                                    
                                <div class="text-center pt-4">
                                    <button type="submit" class="login-button">SUBMIT AND RESET</button>
                                </div>                                        
                            </form>
                            <div class="forget-pass">
                                <a href="{{url('/mentee/login')}}">Login</a>
                            </div>
                        </div>
                    </div>​
                    <div class="login_btn_div">                        
                        <div class="app_btn">
                            <a href="https://play.google.com/store/apps/details?id=com.tsic&hl=en_CA" target="_blank"><img src="{{url('/public/android_btn.png')}}"></a>
                            <a href="https://apps.apple.com/us/app/tsic/id1476056526" target="_blank"><img src="{{url('/public/app_btn.png')}}"></a>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 