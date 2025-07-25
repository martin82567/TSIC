@extends('layouts.apps')
@section('content')

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
    <form role="form" action="{{url('/mentor/sessionlog/save')}}" method="post" enctype="multipart/form-data" id="submit_form">
        {{ csrf_field() }}
        <input type="hidden" id="id" name="id" value="">
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>Log a Session</h3>
                    </div>
                    <div class="col-lg-6">
                        <a href="{{ url('/mentor/sessionlog/list') }}" class="back-btn"><i class="fa fa-arrow-left"></i></a>
                    </div>
                </div>
            </div>
            <div class="box-inner">
                <div class="form-section">
                    <div class="row">
                        <div class="col-sm-8">
                            <h3>
                                Log a Session
                            </h3>
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
                    <?php } ?>
                    @if ($errors->any())
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
                                <label>Note <sup style="color: #ff6c6c;">*</sup></label>
                                <textarea class="form-control" name="name" id="name" maxlength="1024"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Choose a Mentee <sup style="color: #ff6c6c;">*</sup></label>
                                <div class="select">
	                                <select name="mentee_id" id="mentee_id">
                                        <option value="">Select a mentee</option>
	                                    <?php if(!empty($mentee_list)){
                                            foreach($mentee_list as $m){?>
                                        <option value="<?php echo $m->id; ?>"><?php echo $m->firstname.' '.$m->lastname;?></option>
                                        <?php }}?>
	                                </select>
	                            </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="check">
                                    <label for="no_show">No Show?</label>
                                    <label>
                                        <input type="checkbox" class="" name="no_show" value="1" onclick="noShowValueChanged(this)">Mentee did not show up?
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date <sup style="color: #ff6c6c;">*</sup></label>
                                <!-- <input type="text" class="form-control datetimepicker-input" id="datetimepicker5" data-toggle="datetimepicker" data-target="#datetimepicker5"  name="schedule_date" value=""> -->

                                <input id="datepicker" readonly  name="schedule_date" >

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Session Duration <sup style="color: #ff6c6c;">*</sup></label>
                                <div class="select">
                                    <select class="" id="time_duration" name="time_duration">
                                        <option value="0">Select duration</option>
                                        <option value="30">30 minutes</option>
                                        <option value="35">35 minutes</option>
                                        <option value="40">40 minutes</option>
                                        <option value="45">45 minutes</option>
                                        <option value="50">50 minutes</option>
                                        <option value="55">55 minutes</option>
                                        <option value="60">60 minutes</option>
                                        <option value="65">65 minutes</option>
                                        <option value="70">70 minutes</option>
                                        <option value="75">75 minutes</option>
                                        <option value="80">80 minutes</option>
                                        <option value="85">85 minutes</option>
                                        <option value="90">90 minutes</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Session Method/Location <sup style="color: #ff6c6c;">*</sup></label>
                                <div class="select">
                                    <select class="" id="session_method_location_id" name="session_method_location_id" <?php if(!empty($meeting_requests)){?> disabled <?php }?>>
                                        <option value="">Select an option</option>
                                        <?php if(!empty($session_method_location)){ foreach($session_method_location as $ml){?>
                                        <option value="<?php echo $ml->id; ?>" <?php if(isset($meeting_data->session_method_location_id) || !empty($meeting_data->session_method_location_id) && ($meeting_data->session_method_location_id == $ml->id)){?> selected <?php }?>>
                                            <?php echo $ml->method_value; ?>
                                        </option>
                                        <?php }}?>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Session Type<sup style="color: #ff6c6c;">*</sup></label>
                                <div class="select">
                                    <select class="" id="type" name="type">
                                        <option value="">Select an option</option>
                                        <option value="1">Group</option>
                                        <option value="2">Individual</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button  type="submit" id="submit_btn" onclick="return formsubmit();" class="btn btn-success">Save</button>
                        <a href="{{ url('/mentor/sessionlog/list') }}" class="btn btn-danger">Cancel</a>
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

<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>

<script type="text/javascript">

    // $(function () {
    //     $('#datetimepicker5').datetimepicker({
    //         changeMonth: true,
    //         changeYear: true,
    //         format: 'MM-DD-YYYY',
    //         // timeZone : 'America/New_York',
    //         maxDate: new Date()

    //     });
    // });

    $('#datepicker').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'mm-dd-yyyy',
        // format: 'yyyy-mm-dd',
        disableDates:  function (date) {
            const currentDate = new Date();
            return date < currentDate ? true : false;
        }
    });

    function noShowValueChanged(cb) {
        document.getElementById('time_duration').disabled = cb.checked
    }

    function formsubmit() {
        var name = $('#name').val();
        var mentee_id = $('#mentee_id').val();
        var session_method_location_id = $('#session_method_location_id').val();
        var type = $('#type').val();
        var time_duration = $('#time_duration').val();
        var school_location = $('#school_location').val();

        var datepicker = $('#datepicker').val();
        // var datetimepicker5 = $('#datetimepicker5').val();

        if(name == ''){
            swal('Add a title');
            return false;
        }
        else if (mentee_id == '') {
            swal('Please add mentee');
            return false;
        }
        else if (datepicker == '') {
            swal('Please add date');
            return false;
        }
        else if (time_duration == '') {
            swal('Please add duration');
            return false;
        }
        else if(session_method_location_id == ''){
            swal('Please add method location');
            return false;
        }
        else if(type == ''){
            swal('Please add session type');
            return false;
        }


        $('#submit_btn').prop('disabled', true);
        $('#submit_form').submit();
    }

</script>
@endsection
