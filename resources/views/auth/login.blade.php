@extends('layouts.appusers')
@section('content')
    <div class="login-sec-appuser">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 col-centered">
                    <div class="alert alert-danger" id="system_messages_alert"  role="alert" style="display: none">
                        <ul id="system_messages">

                        </ul>

                    </div>
                    <div class="inner">
                        <div class="login-form">
                            <div class="inr">
                                <?php if(!empty(session('message'))){ ?>
                                <div class="alert alert-<?php echo session('msg_class'); ?>" role="alert">
                                    <?php echo session('message'); Session::forget('message'); ?>

                                </div>
                                <?php } ?>
                                <h3><span><center>LOGIN</center></span></h3>
                                <form class="" method="POST" action="{{ route('login.submit') }}" autocomplete="off">
                                    {{ csrf_field() }}
                                    <div class="single-field has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                                        <i class="fa fa-user"></i>
                                        <input id="email" type="text" placeholder="Email" class="form-control" name="email" value="{{ old('email') }}" autocomplete="off">
                                        <span class="help-block email-div">
                                        <strong class="email-help-block"></strong>
                                    </span>
                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div  id="passwordField" class="single-field has-feedback {{ $errors->has('password') ? ' has-error' : '' }}" style="display: {{ old('email') ? 'block' : 'none'}}">
                                        <i class="fa fa-lock"></i>
                                        <input id="password" type="password" placeholder="Password" class="form-control" value="{{ old('password') }}" name="password" autocomplete="off">
                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <?php if(!empty($waiver_statement)){?>
                                    <div class="div_disclaimer" style="display: none">
                                        <label class="check">
                                            <input type="checkbox" name="waiver_statement_id"  value="<?php echo $waiver_statement->id; ?>" {{ old('waiver_statement_id') ? 'checked' : '' }}><?php echo $waiver_statement->statement; ?> <a href="<?php echo $waiver_statement->url; ?>" target="_blank">click here</a> </label>
                                        @if ($errors->has('waiver_statement_id'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('waiver_statement_id') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                    <?php }?>
                                    <input type="hidden" name="is_waiver_checked" id="is_waiver_checked" value="{{ old('is_waiver_checked') }}">

                                    <div class="text-center pt-4">
                                        <button id="next-button" type="button" onclick="showLogin()" class="next-button"  style="display: {{ !old('email') ? 'block' : 'none'}}">NEXT</button>
                                        <button id="login-button" type="submit" class="login-button"  style="display: {{ old('email') ? 'block' : 'none'}}">LOGIN</button>
                                    </div>
                                </form>
                                <div class="forget-pass">
                                    <a href="{{url('/forget-password')}}">Forgot Password ?</a>
                                </div>
                            </div>

                        </div>
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
    <script type="text/javascript">
        $( document ).ready(function() {
            var is_waiver_checked = $('#is_waiver_checked').val();
            <?php if(old('email')) { ?>
            document.getElementById("passwordField").style.display = "block";
            document.getElementById("next-button").style.display = "none";
            document.getElementById('email').readOnly = true;
            <?php } else { ?>
            document.getElementById("login-button").style.display = "none";
            document.getElementById("login-button").disabled = true;
            document.getElementById("next-button").disabled = true;
            <?php } ?>
            if(is_waiver_checked === '1'){
                $('.div_disclaimer').hide();
            }else{
                $('.div_disclaimer').show();
            }
        });
        $('#email').on('keyup', function(e){
            let email;
            email = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if($(this).val().match(email)){
                $('#waiver_statement_id').remove();
                let system_message = '';
                $.ajax({
                    type: "POST",
                    url: "{{ url('/check_waiver') }}",
                    data: {
                        "email": $(this).val(),
                        "_token": "{{ csrf_token() }}",
                    },
                    dataType: "json",
                    success: function(data) {
                        if(data.is_waiver === true){
                            $('.div_disclaimer').show();
                            $('.email-help-block').hide();
                            document.getElementById("next-button").disabled = false;
                        }else{
                            if(data.message === 'Not found'){
                                document.getElementById("next-button").disabled = true;
                                $('.email-help-block').show();
                                $('.email-help-block').text("Email does not exist.")
                                document.getElementById("login-button").style.display = "none";
                                $('.div_disclaimer').show();
                                $('#is_waiver_checked').val('0');
                                $('.email-div').show();
                                if($('#email').length === 0) {
                                    $('.email-div').hide();
                                }

                            }else{
                                $('.email-help-block').hide();
                                $('.email-div').hide();
                                $('.div_disclaimer').hide();
                                $('#is_waiver_checked').val('1');
                                if(data.message === 'Waiver acknowledged'){
                                    if(data.system_messaging !== 'undefined' && (data.system_messaging).length > 0) {
                                        var systemMessaging = data.system_messaging;
                                        systemMessaging.forEach(messageFunction);
                                        document.getElementById('system_messages_alert').style.display = "block";
                                        document.getElementById("system_messages").innerHTML = system_message;
                                    }
                                    document.getElementById("next-button").disabled = false;
                                    $('#is_waiver_checked').append('<input type="hidden" name="waiver_statement_id" id="waiver_statement_id" value="'+data.waiver_statement_id+'">');
                                    if(e.keyCode === 13 && document.getElementById("next-button").disabled === false) {
                                        e.preventDefault();
                                        document.getElementById("next-button").style.display = "none";
                                        document.getElementById("login-button").style.display = "block";
                                        document.getElementById("passwordField").style.display = "block";
                                        document.getElementById('email').readOnly = true;
                                        document.getElementById("login-button").disabled = false;
                                        return false;
                                    }
                                }
                            }

                        }
                    }
                });
                function messageFunction(data){
                    console.log(data);
                    system_message += "<li><strong>"+data.message+" (Expires on "+data.end_datetime+")</strong></li>";
                }
            }else{
                $('.div_disclaimer').show();
                $('#waiver_statement_id').remove();
            }

        });
        function showLogin(){
            document.getElementById("login-button").style.display = "block";
            document.getElementById("next-button").style.display = "none";
            document.getElementById("passwordField").style.display = "block";
            document.getElementById('email').readOnly = true;
            document.getElementById("login-button").disabled = false;

        }
    </script>
    <style scoped>
        button.login-button:disabled{
            opacity: 0.6 !important;
            cursor: not-allowed !important;
        }
        button.login-button {
            margin: auto !important;
        }

        button.next-button:disabled{
            opacity: 0.6 !important;
            cursor: not-allowed !important;
        }
        .email-help-block{
            color:#000000;
        }
        button.next-button {
            width: 100%;
            max-width: 300px;
            height: 50px;
            font-family: 'Open Sans', sans-serif;
            font-weight: 600;
            font-size: 17px;
            background: #f07622;
            color: #fff;
            box-shadow: 0 9px 13px rgb(255 78 0 / 20%);
            border-radius: 5px;
            border: none;
            margin: auto;
        }
    </style>
@endsection
