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
    <form role="form" action="{{ route('admin.agency.meeting.save')}}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}

        <input type="hidden" id="id" name="id" value="<?php echo !empty($meeting_data->id)?$meeting_data->id:0;?>">
        
        <div class="db-box">

            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>Session</h3>
                    </div>
                    <div class="col-lg-6">
                        <a href="{{ url('/admin/agency/meeting') }}" class="back-btn"><i class="fa fa-arrow-left"></i></a>
                    </div>
                </div>
            </div>
            <div class="box-inner">
                <div class="form-section">
                    <div class="row">
                        <div class="col-sm-8">
                            <h3>
                                <?php if(empty($meeting_data->id)){?>Create<?php }else{?>Edit<?php }?> Session</h3>
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
                                <label>Title <sup style="color: #ff6c6c;">*</sup></label>
                                <input type="text" value="<?php echo !empty($meeting_data->title)?$meeting_data->title:'';?>" class="form-control" name="title" id="title" required="required">
                            </div>
                        </div>
                    </div>
                    <div class="row">                                                
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description <sup style="color: #ff6c6c;">*</sup></label>
                                <textarea class="form-control" name="description" required="required" id="description"><?php echo !empty($meeting_data->description)?$meeting_data->description:'';?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">                                                
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Choose a Mentors <sup style="color: #ff6c6c;">*</sup></label>
                                <div class="select">
	                                <select name="mentor_ids" id="mentor_ids" onchange="getMentee(this.value);">
	                                    <option>Select a mentor</option>
	                                    <?php if(!empty($mentors)){ foreach($mentors as $mn){?>
	                                    <option value="<?php echo $mn->id; ?>" <?php if(!empty($mentor_ids) && $mentor_ids->user_id == $mn->id){?> selected <?php }?>>
	                                        <?php echo $mn->firstname.' '.$mn->middlename.' '.$mn->lastname; ?>
	                                    </option>
	                                    <?php }}?>  
	                                </select>
	                            </div>                                                               
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Choose Mentees <sup style="color: #ff6c6c;">*</sup></label>
                                <div class="select">
	                                <select name="mentee_ids" id="mentee_ids" >
	                                    <option>Select a mentee</option>
	                                    
	                                </select>
	                            </div> 
                            </div>
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Schedule Date &amp; Time<sup style="color: #ff6c6c;">*</sup></label>
                                <input type="text" class="form-control datetimepicker-input" id="datetimepicker5" data-toggle="datetimepicker" data-target="#datetimepicker5"  name="schedule_time" <?php if(!$is_datetime_valid){?> readonly <?php }?>>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                    	<div class="col-md-12">
                            <div class="form-group">
                                <label>Choose School <sup style="color: #ff6c6c;">*</sup></label>
                                <div class="select">
                                    <select class="" id="school_id" name="school_id" required="required">
                                        <option value="">Choose a school</option>
                                        @if(!empty($school))
                                        @foreach($school as $s)
                                        <option value="{{$s->id}}" <?php if(isset($meeting_data->school_id) && ($meeting_data->school_id == $s->id)){?> selected <?php }?>>{{$s->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                                                
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Location Within School <sup style="color: #ff6c6c;">*</sup></label>
                                <textarea class="form-control" name="school_location" required="required" id="school_location" style="height: 40px;"><?php echo !empty($meeting_data->school_location)?$meeting_data->school_location:'';?></textarea>
                            </div>
                        </div>
                    </div>
                    
                      
                    <div class="box-footer">
                        <button id="" type="submit" onclick="return formsubmit();" class="btn btn-success">Save</button>
                        <a href="{{ url('/admin/agency/meeting') }}" class="btn btn-danger">Cancel</a>                       
                    </div>

                </div>

            </div>
        </div>
    </form>
</div>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/js/tempusdominus-bootstrap-4.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css" />
<!-- <script type="text/javascript" src="https://momentjs.com/downloads/moment-timezone.min.js"></script> -->
<script type="text/javascript" src="https://momentjs.com/downloads/moment-timezone.js"></script>
<script type="text/javascript">

    $(document).ready(function() {
        
        var id = $('#id').val();
        if(id != '0'){

        	var is_datetime_valid = '<?php echo $is_datetime_valid; ?>';
        	if(is_datetime_valid == ''){
        		$('#datetimepicker5').datetimepicker({
		            changeMonth: true,
		            changeYear: true,
		            yearRange: '<?php echo (date('Y'));?>:<?php echo (date('Y')+5);?>',
		            format: 'MM-DD-YYYY HH:mm',            
		            defaultDate: "<?php echo !empty($meeting_data->schedule_time)?date('m-d-Y H:i', strtotime($meeting_data->schedule_time)):'';?>",
		        });
        	}else{
        		$('#datetimepicker5').datetimepicker({
		            changeMonth: true,
		            changeYear: true,
		            yearRange: '<?php echo (date('Y'));?>:<?php echo (date('Y')+5);?>',
		            format: 'MM-DD-YYYY HH:mm',
		            minDate: new Date(),
		            defaultDate: "<?php echo !empty($meeting_data->schedule_time)?date('m-d-Y H:i', strtotime($meeting_data->schedule_time)):'';?>",
		        });
        	}
	        

	        var mentor_ids = $('#mentor_ids').val();

	        // console.log(mentor_ids);
	        // return true;

	        $.ajax({
	            url: "{{ url('/admin/agency/get_mentee_from_meeting_data') }}",
	            type: "POST",
	            data: {
	               id: id,
	               _token: "{{ csrf_token() }}"
	            },                
	            dataType: "json",
	            success: function(data) {                
	                var mentee = jQuery.parseJSON(JSON.stringify(data.mentee_ids)); 

	                console.log(mentee);
	                // return true;                
	                
	                var setData = "";
	                if(mentee.length > 0){
	                	
	                    for (var i = 0; i < mentee.length; i++) {
	                        // var mentees = mentee[i].user_id;
	                        // meArr.push(mentees);
	                        var firstname = mentee[i].firstname;
	                        var middlename = mentee[i].middlename;
                        	var lastname = mentee[i].lastname;
	                        
	                        setData += `<option value="`+mentee[i].user_id+`">`+firstname+` `+middlename+` `+lastname+`</option>`;
	                    }
	                }else{
	                	setData += `<option> No mentee found </option>`;
	                }

	                $('#mentee_ids').html(setData);
	                    
	                    

	            }
	        });
            
        }
    });

    function getMentee(e){
    	console.log(e);

    	$.ajax({
            url: "{{ url('/admin/agency/get_mentees_by_mentor') }}",
            type: "POST",
            data: {
               mentor_id: e,
               _token: "{{ csrf_token() }}"
            },                
            dataType: "json",
            success: function(data) {                
                var mentee = jQuery.parseJSON(JSON.stringify(data.mentee)); 

                console.log(mentee);
                // return true;                
                
                var setData = "";
                if(mentee.length > 0){
                	setData += `<option> Select a mentee </option>`;
                    for (var i = 0; i < mentee.length; i++) {
                        // var mentees = mentee[i].user_id;
                        // meArr.push(mentees);
                        var firstname = mentee[i].firstname;
                        var middlename = mentee[i].middlename;
                        var lastname = mentee[i].lastname;
                        
                        setData += `<option value="`+mentee[i].mentee_id+`">`+firstname+` `+middlename+` `+lastname+`</option>`;
                    }
                }else{
                	setData += `<option> No mentee found </option>`;
                }

                $('#mentee_ids').html(setData);
                    
                    

            }
        });


    }



    $(function () {

        $('#datetimepicker5').datetimepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: '<?php echo (date('Y'));?>:<?php echo (date('Y')+5);?>',
            format: 'MM-DD-YYYY HH:mm',
            // timeZone : 'America/New_York',
            minDate: new Date()
            // locale: 'de',
            
        });
    });

    function formsubmit() {
        var title = $('#title').val();
        var description = $('#description').val();        
        var mentor_id = $('#mentor_id').val();
        var mentee_ids = $('#mentee_ids').val();
        var school_id = $('#school_id').val();
        var school_location = $('#school_location').val();
        
        var datetimepicker5 = $('#datetimepicker5').val();

        if(title == ''){
            swal('Title is required');
            return false;
        }else if (description == '') {
            swal('Description is required');
            return false;
        }else if (mentor_id == '') {
            swal('Please add mentor');
            return false;
        }else if (mentee_ids == '') {
            swal('Please add mentees');
            return false;
        }else if(school_id == ''){
            swal('Please add school');
            return false;
        }else if (datetimepicker5 == '') {
            swal('Please add Schedule');
            return false;
        }else if(school_location == ''){
            swal('Please add school location');
            return false;
        }
        return true;
    }

</script>
@endsection
