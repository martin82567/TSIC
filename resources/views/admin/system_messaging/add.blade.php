@extends('layouts.admin') @section('content')

<style type="text/css">
    .card-body {
        background: #27528c;
    }

    .card-header {
        background: #00a7a8;
    }

    .db-container .form-section .btn-link {
        color: #fff;
        text-decoration: none;
    }

    .db-container .form-section .accordion .btn-link:hover {
        color: #fff;

    }


    p.arrow {
        float: right;
        margin-top: 8px;
    }

    p.arrow {
        position: absolute;
        top: 5px;
        right: 20px;
        font-size: 20px;
        color: white;
        -webkit-animation: minus 0.5s;
    }

    @keyframes minus {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    [aria-expanded="false"]>span.expanded,
    [aria-expanded="true"]>span.collapsed {
        display: none;
    }

</style>
<div class="db-inner-content">
    <form role="form" action="{{url('/admin/system-messaging/save')}}" method="post" enctype="multipart/form-data" id="submit_form">
        {{ csrf_field() }}
        <input type="hidden" id="id" name="id" value="<?php echo !empty($system_messaging->id)?$system_messaging->id:'';?>">        
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>System Messaging</h3>
                    </div>
                    <div class="col-lg-6">
                        <a href="{{ url('/admin/system-messaging/list') }}" class="back-btn"><i class="fa fa-arrow-left"></i></a>
                    </div>
                </div>
            </div>
            <div class="box-inner">
                <div class="form-section">
                    <div class="row">
                        <div class="col-sm-8">
                            <?php if(empty($system_messaging->id)){?>
                            <h3>Create</h3>
                            <?php }else{?>
                            <h3>Edit</h3>
                            <?php }?>
                        </div>                        
                    </div>                                    
                    <?php if(!empty(session('success_message'))){ ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                        <h4 style="margin-bottom: 0"><i class="icon fa fa-check"></i>
                            <?php 
                                echo session('success_message'); 
                                Session::forget('success_message');
                            ?>
                        </h4>
                    </div>
                    <?php } ?>
                    <?php if(!empty(session('error_message'))){ ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                        <h4 style="margin-bottom: 0"><i class="icon fa fa-warning"></i>
                            <?php 
                                echo session('error_message'); 
                                Session::forget('error_message');
                            ?>
                        </h4>
                    </div>
                    <?php } ?> @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                        <!--{{ implode('', $errors->all(':message')) }}-->
                        @foreach ($errors->all() as $error)
                        <h4>{{ $error }}</h4>
                        @endforeach
                    </div>
                    @endif           
                    
                    <div class="row">                                                
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Message <sup style="color: #ff6c6c;">*</sup></label>
                                <textarea class="form-control" name="message"  id="message"><?php echo !empty($system_messaging->message)?$system_messaging->message:''; ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row"> 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date &amp; Time<sup style="color: #ff6c6c;">*</sup></label>
                                <input type="text" class="form-control datetimepicker-input" id="datetimepicker5" data-toggle="datetimepicker" data-target="#datetimepicker5"  name="start_datetime">

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date &amp; Time<sup style="color: #ff6c6c;">*</sup></label>
                                <input type="text" class="form-control datetimepicker-input" id="datetimepicker6" data-toggle="datetimepicker" data-target="#datetimepicker6"  name="end_datetime">

                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">                        
                        <?php 
                            $app_ids = array();
                            if(!empty($system_messaging_appids)){
                                foreach($system_messaging_appids as $ids){
                                    $app_ids[] = $ids->app_id;
                                }
                            }
                            foreach($apps as $app){
                                $checked = "";
                                if(!empty($app_ids)){
                                    if (in_array($app->id, $app_ids)){
                                        $checked = "checked";
                                    }
                                }
                                
                            
                        ?>
                        <div class="col-lg-12">
                            <div class="check">
                                <label>
                                    <input type="checkbox" name="app_ids[]" class="app_ids" value="<?php echo $app->id;?>" <?php echo $checked; ?> ><?php echo $app->apps;?>                                     
                                </label>
                            </div>
                        </div>
                        <?php }?>
                    </div>
                      
                    <div class="box-footer">
                        <button  type="submit" id="submit_btn" onclick="return formsubmit();" class="btn btn-success">Save</button>
                        <a href="{{ url('/admin/system-messaging/list') }}" class="btn btn-danger">Cancel</a>                       
                    </div>

                </div>

            </div>
        </div>
    </form>
</div>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/js/tempusdominus-bootstrap-4.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css" />
<!-- <script type="text/javascript" src="https://momentjs.com/downloads/moment-timezone.min.js"></script> -->
<!-- <script type="text/javascript" src="https://momentjs.com/downloads/moment-timezone.js"></script> -->
<script type="text/javascript">

    $(document).ready(function() {        
        var id = $('#id').val();

        console.log(id);

        if(id != ''){
            $('#datetimepicker5').datetimepicker({
                changeMonth: true,
                changeYear: true,
                yearRange: '<?php echo (date('Y'));?>:<?php echo (date('Y')+5);?>',
                format: 'MM-DD-YYYY HH:mm',            
                defaultDate: "<?php echo !empty($system_messaging->start_datetime)?date('m-d-Y H:i', strtotime($system_messaging->start_datetime)):'';?>",
            });
            $('#datetimepicker6').datetimepicker({
                changeMonth: true,
                changeYear: true,
                yearRange: '<?php echo (date('Y'));?>:<?php echo (date('Y')+5);?>',
                format: 'MM-DD-YYYY HH:mm',            
                defaultDate: "<?php echo !empty($system_messaging->end_datetime)?date('m-d-Y H:i', strtotime($system_messaging->end_datetime)):'';?>",
            });
        }
        
    });


    




    $(function () {

        $('#datetimepicker5').datetimepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: '<?php echo (date('Y'));?>:<?php echo (date('Y')+5);?>',
            format: 'MM-DD-YYYY HH:mm',
            timeZone : 'America/New_York',
            useCurrent: false,
            minDate: new Date()            
        });
        $('#datetimepicker6').datetimepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: '<?php echo (date('Y'));?>:<?php echo (date('Y')+5);?>',
            format: 'MM-DD-YYYY HH:mm',
            timeZone : 'America/New_York',
            useCurrent: false,
            minDate: new Date()            
        });

        
    });

    function formsubmit() {
                
        var message = $('#message').val();
        var datetimepicker5 = $('#datetimepicker5').val();
        var datetimepicker6 = $('#datetimepicker6').val();

        var apps = $('#submit_form input:checked').length;

        console.log(apps);
        

        if(message == ''){
            swal('Message is required');
            return false;
        }        
        else if(datetimepicker5 == ''){
            swal('Please add start datetime');
            return false;
        }
        else if (datetimepicker6 == '') {
            swal('Please add end datetime');
            return false;
        }
        else if (apps == 0) {
            swal('Please add at least one app');
            return false;
        }

        // return true;

        $('#submit_btn').prop('disabled', true);
        $('#submit_form').submit();   
    }

</script>
@endsection
