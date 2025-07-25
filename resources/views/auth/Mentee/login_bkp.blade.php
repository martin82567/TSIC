@extends('layouts.appusers')
@section('content')     
<div class="login-sec-appuser">
    <div class="container">
        <div class="row">
            <div class="col-xl-6 col-centered">
                <?php                
                if(!empty($system_messaging)){

                ?>
                <div class="alert alert-danger" role="alert">
                    <ul>
                        <?php foreach($system_messaging as $message){     ?>
                        <li><strong><?php echo $message->message;?> (Expires on <?php echo date('m/d/y', strtotime($message->end_datetime));?>)</strong></li>
                        <?php }?>
                    </ul>
                    
                </div>
                <?php 
                    
                }
                ?>
                <div class="inner">
                    <div class="login-form">
                        <div class="inr">
                            <?php if(!empty(session('message'))){ ?>
                            <div class="alert alert-<?php echo session('msg_class'); ?>" role="alert">
                                <?php echo session('message'); Session::forget('message'); ?>
                            </div>
                            <?php } ?>
                            <h3><span><center>MENTEE LOGIN</center></span></h3>
                            <form class="" method="POST" action="{{ route('mentee.login.submit') }}">
                                {{ csrf_field() }}
                                <div class="single-field has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                                    <i class="fa fa-user"></i>
                                    <input id="email" type="email" placeholder="Email or User Name" class="form-control" name="email" value="{{ old('email') }}" required autofocus> 
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span> 
                                    @endif
                                </div>
                                <div class="single-field has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                                    <i class="fa fa-lock"></i>
                                    <input id="password" type="password" placeholder="Password" class="form-control" name="password" required> 
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span> 
                                    @endif
                                </div>
                                <?php if(!empty($waiver_statement)){?>
                                <div class="div_disclaimer">
                                    <label for="1" class="check">
                                    <input type="checkbox" name="waiver_statement_id" required value="<?php echo $waiver_statement->id; ?>"><?php echo $waiver_statement->statement; ?> <a href="<?php echo $waiver_statement->url; ?>" target="_blank">click here</a> </label>
                                    @if ($errors->has('waiver_statement_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('waiver_statement_id') }}</strong>
                                    </span> 
                                    @endif
                                </div> 
                                <?php }?>
                                <div class="text-center pt-4">
                                    <button type="submit" class="login-button">LOGIN</button>
                                </div>                                        
                            </form>
                            <div class="forget-pass">
                                <a href="{{url('/mentee/forget-password')}}">Forgot Password ?</a>
                            </div>
                        </div>
                    </div>​
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 