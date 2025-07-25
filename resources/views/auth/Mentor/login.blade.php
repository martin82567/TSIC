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
                            <h3><span><center>MENTOR LOGIN</center></span></h3>
                            <form class="" method="POST" action="{{ route('mentor.login.submit') }}" autocomplete="off">
                                {{ csrf_field() }}
                                <div class="single-field has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                                    <i class="fa fa-user"></i>
                                    <input id="email" type="text" placeholder="Email" class="form-control" name="email" value="{{ old('email') }}" autocomplete="nope"> 
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span> 
                                    @endif
                                </div>
                                <div class="single-field has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                                    <i class="fa fa-lock"></i>
                                    <input id="password" type="password" placeholder="Password" class="form-control" name="password" autocomplete="nope"> 
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span> 
                                    @endif
                                </div>
                                <?php if(!empty($waiver_statement)){?>
                                <div class="div_disclaimer">
                                    <label class="check">
                                    <input type="checkbox" name="waiver_statement_id"  value="<?php echo $waiver_statement->id; ?>"><?php echo $waiver_statement->statement; ?> <a href="<?php echo $waiver_statement->url; ?>" target="_blank">click here</a> </label>
                                    @if ($errors->has('waiver_statement_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('waiver_statement_id') }}</strong>
                                    </span> 
                                    @endif
                                </div>                                 
                                <?php }?>
                                <input type="hidden" name="is_waiver_checked" id="is_waiver_checked" value="{{ old('is_waiver_checked') }}">
                                
                                <div class="text-center pt-4">
                                    <button type="submit" class="login-button">LOGIN</button>
                                </div>                                        
                            </form>
                            <div class="forget-pass">
                                <a href="{{url('/mentor/forget-password')}}">Forgot Password ?</a>
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
<script type="text/javascript">
	$( document ).ready(function() {
	    var is_waiver_checked = $('#is_waiver_checked').val();
	    if(is_waiver_checked == '1'){
	    	$('.div_disclaimer').hide();
	    }else{
	    	$('.div_disclaimer').show();
	    }
	});
    $('#email').on('keyup', function(e){
                
        if($(this).val().length > 10){
            // console.log('Total Length:- ' + $(this).val().length);
            
            $('.div_disclaimer').show();
            // $('#waiver_statement_id').val(0);
            $('#waiver_statement_id').remove();
            $.ajax({
                type: "POST",
                url: "{{ url('/mentor/check_waiver') }}",
                data: {               
                    "email": $(this).val(),
                    "_token": "{{ csrf_token() }}",
                },              
                dataType: "json",
                success: function(data) { 
                	          
                    if(data.is_waiver == true){
                        $('.div_disclaimer').show();
                    }else{
                    	if(data.message == 'Not found'){
                    		$('.div_disclaimer').show();
                    		$('#is_waiver_checked').val('0');
                    	}else{
                    		$('.div_disclaimer').hide();
                    		$('#is_waiver_checked').val('1');
                    		if(data.message == 'Waiver acknowledged'){
	                    		// $('#waiver_statement_id').val(data.waiver_statement_id);
	                    		$('#is_waiver_checked').append('<input type="hidden" name="waiver_statement_id" id="waiver_statement_id" value="'+data.waiver_statement_id+'">');
	                    	}
                    	}

                    	
                        
                    }
                }
            });
        }else{
            $('.div_disclaimer').show();
            $('#waiver_statement_id').remove();
        }

    });
</script>
@endsection    