@extends('layouts.admin') 
@section('content')

<div class="db-inner-content">
    <form role="form" action="{{ url('/admin/save_settings')}}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" id="id" name="id" value="1">
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <h3>Settings</h3>                        
                        <?php if(!empty(session('success_message'))){ ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                            <h4 style="margin-bottom: 0"><i class="icon fa fa-check"></i>
                                <?php echo session('success_message'); Session::forget('success_message'); ?>
                            </h4>
                        </div>
                        <?php } ?>
                        <?php if(!empty(session('error_message'))){ ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                            <h4 style="margin-bottom: 0"><i class="icon fa fa-warning"></i>
                                <?php echo session('error_message'); Session::forget('error_message'); ?>
                            </h4>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="box-inner">
                <div class="form-section">                    
                    <h3>General Settings</h3>
                    <div class="card">                        
                        <div class="card-body">
                            <div class="">
                                <div class="row mb-3">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Site Name</label>
                                            <input class="form-control" name="site_name" type="text" value="<?php echo !empty($settings->site_name)?$settings->site_name:'';?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Site Email</label>
                                            <input class="form-control" name="site_email" type="text" value="<?php echo !empty($settings->site_email)?$settings->site_email:'';?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Site Phone No</label>
                                            <input class="form-control" name="site_phone_no" type="text" value="<?php echo !empty($settings->site_phone_no)?$settings->site_phone_no:'';?>">
                                        </div>
                                    </div> 
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Video Chat Duration <small>(In minutes)</small></label>
                                            <input class="form-control" name="video_chat_duration" type="text" maxlength="3" value="<?php echo !empty($settings->video_chat_duration)?$settings->video_chat_duration:'';?>" onkeypress="return isNumber(event)" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Video Chat Start Time</label>
                                            <input class="form-control" id="video_chat_start_time" name="video_chat_start_time" type="time" value="<?php echo $settings->video_chat_start_time; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Video Chat End Time</label>
                                            <input class="form-control" id="video_chat_end_time" name="video_chat_end_time" type="time"  value="<?php echo $settings->video_chat_end_time; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Twilio Account SID</label>
                                            <input class="form-control" name="twilio_account_sid" type="password" id="twilio_account_sid" value="<?php echo !empty($settings->twilio_account_sid)?$settings->twilio_account_sid:'';?>" required>
                                            <i class="fa fa-eye-slash" id="icon_twilio_account_sid" onclick="showhide('twilio_account_sid');" style="color:#000"></i>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Twilio Auth Token</label>
                                            <input class="form-control" name="twilio_auth_token" type="password" id="twilio_auth_token" value="<?php echo !empty($settings->twilio_auth_token)?$settings->twilio_auth_token:'';?>" required>
                                            <i class="fa fa-eye-slash" id="icon_twilio_auth_token" onclick="showhide('twilio_auth_token');" style="color:#000"></i>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Twilio API Key SID</label>
                                            <input class="form-control" name="twilio_apiKeySid" id="twilio_apiKeySid" type="password" value="<?php echo !empty($settings->twilio_apiKeySid)?$settings->twilio_apiKeySid:'';?>" required>
                                            <i class="fa fa-eye-slash" id="icon_twilio_apiKeySid" onclick="showhide('twilio_apiKeySid');" style="color:#000"></i>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Twilio API Key Secret</label>
                                            <input class="form-control" name="twilio_apiKeySecret" type="password" id="twilio_apiKeySecret" value="<?php echo !empty($settings->twilio_apiKeySecret)?$settings->twilio_apiKeySecret:'';?>" required>
                                            <i class="fa fa-eye-slash" id="icon_twilio_apiKeySecret" onclick="showhide('twilio_apiKeySecret');" style="color:#000"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Mentor FAQ</label>
                                            <input class="form-control" name="mentor_faq" type="text" value="<?php echo !empty($settings->mentor_faq)?$settings->mentor_faq:'';?>" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Mentee FAQ</label>
                                            <input class="form-control" name="mentee_faq" type="text" value="<?php echo !empty($settings->mentee_faq)?$settings->mentee_faq:'';?>" required>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>                            
                        </div>                        
                    </div> 
                    <h3>Waiver Settings</h3>  
                    <div class="card">                        
                        <div class="card-body">
                            <div class="">
                                <input type="hidden" name="" value="<?php echo !empty($settings->waiver_statement)?$settings->waiver_statement:'';?>" id="oldStatement">
                                <input type="hidden" name="" value="<?php echo !empty($settings->waiver_url)?$settings->waiver_url:'';?>" id="oldUrl">
                                <input type="hidden" name="is_waiver_reset" id="is_waiver_reset">
                                <div class="row mb-3">                                    
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Waiver Statement</label>
                                            <textarea class="form-control waiver-text" id="waiver_statement" required="required" rows="4" cols="50" name="waiver_statement"><?php echo !empty($settings->waiver_statement)?$settings->waiver_statement:'';?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Waiver URL</label>
                                            <input type="url" class="form-control waiver-text" name="waiver_url" value="<?php echo !empty($settings->waiver_url)?$settings->waiver_url:'';?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <a href="javascript:void(0)" class="btn btn-success btn-sm" id="reset_waiver_btn" onclick="return reset_waiver();">Reset Waiver Acceptance</a>
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                        </div>                        
                    </div>                  
                </div>
            </div>
            <br/>
            <div class="box-footer">
                <button id="submit_id" type="submit"  class="btn btn-success">Save</button>
            </div>
        </div>
    </form>
</div>

<script  type="text/javascript">

        
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    $( document ).ready(function() {
        $('#reset_waiver_btn').hide();
        
    });
    
    $(".waiver-text").on('input',function(e){
        $('#reset_waiver_btn').show();
        
    });

    function reset_waiver() {
        // body...
        $('#is_waiver_reset').val(1);
        $('form').submit();
    }

    function showhide(t) {
        // console.log(t);
        var x = document.getElementById(t);

        // console.log('icon_'+t);
                        
        if (x.type === "password") {
            x.type = "text";
            $('#icon_'+t).attr('class', 'fa fa-eye');
        } else {
            x.type = "password";
            $('#icon_'+t).attr('class', 'fa fa-eye-slash');
        }
    }
    

</script>


@endsection
