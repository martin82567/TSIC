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
    <form role="form" action="{{ route('admin.agency.meeting.save')}}" method="post" enctype="multipart/form-data" id="submit_form">
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
                                <label>Agenda Name <sup style="color: #ff6c6c;">*</sup></label>
                                <input type="text" value="<?php echo !empty($meeting_data->title)?$meeting_data->title:'Mentor Session';?>" placeholder="Mentor Session" class="form-control" name="title" id="title" >
                            </div>
                        </div>
                    </div>
                    <div class="row">                                                
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description </label>
                                <textarea class="form-control" name="description"  id="description"><?php echo !empty($meeting_data->description)?$meeting_data->description:'';?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">                                                
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Choose a Mentor <sup style="color: #ff6c6c;">*</sup></label>
                                <div class="select">
	                                <select name="mentor_ids" id="mentor_ids" onchange="getMentee(this.value);">
	                                    <option value="">Select a mentor</option>
	                                    <?php if(!empty($mentors)){ foreach($mentors as $mn){?>
	                                    <option value="<?php echo $mn->id; ?>" <?php if(!empty($meeting_data) && $meeting_data->mentor_id == $mn->id){?> selected <?php }?>>
	                                        <?php echo $mn->firstname.' '.$mn->middlename.' '.$mn->lastname; ?>
	                                    </option>
	                                    <?php }}?>  
	                                </select>
	                            </div>                                                               
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Choose a Mentee <sup style="color: #ff6c6c;">*</sup></label>
                                <div class="select">
	                                <select name="mentee_ids" id="mentee_ids" >
	                                    
	                                </select>
	                            </div> 
                            </div>
                        </div>
                    </div>

                    <div class="row"> 
                    	<div class="col-md-12">
                            <div class="form-group">
                                <label>Session Location <sup style="color: #ff6c6c;">*</sup></label>
                                <div class="select">
                                    <select class="" id="school_id" name="school_id" >
                                        
                                    </select>
                                </div>
                                <input type="hidden" name="school_type" id="school_type">
                            </div>
                        </div>
                    </div>

                    <div class="row"> 
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Session Method Location <sup style="color: #ff6c6c;">*</sup></label>
                                <div class="select">
                                    <select class="" id="session_method_location_id" name="session_method_location_id" >
                                        <option value="">Select an option</option>
                                        <?php if(!empty($session_method_location)){ foreach($session_method_location as $ml){?>
                                        <option value="<?php echo $ml->id; ?>" <?php if(!empty($meeting_data->session_method_location_id) && ($meeting_data->session_method_location_id == $ml->id) ){?> selected <?php }?>>
                                            <?php echo $ml->method_value; ?>
                                        </option>
                                        <?php }}?> 
                                    </select>
                                </div>
                                                                
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Session Space </label>
                                <textarea class="form-control" name="school_location"  id="school_location" style="height: 40px;"><?php echo !empty($meeting_data->school_location)?$meeting_data->school_location:'';?></textarea>
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
                      
                    <div class="box-footer">
                        <button  type="submit" id="submit_btn" onclick="return formsubmit();" class="btn btn-success">Save</button>
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
<!-- <script type="text/javascript" src="https://momentjs.com/downloads/moment-timezone.js"></script> -->
<script type="text/javascript">

    $(document).ready(function() {
        
        var id = $('#id').val();
        if(id != '0'){

        	var is_datetime_valid = '<?php echo $is_datetime_valid; ?>';
            console.log(is_datetime_valid);
        	if(is_datetime_valid == ''){
        		$('#datetimepicker5').datetimepicker({
		            changeMonth: true,
		            changeYear: true,
		            yearRange: '<?php echo (date('Y'));?>:<?php echo (date('Y')+5);?>',
		            format: 'MM-DD-YYYY hh:mm A',
                    ampm: true, // FOR AM/PM FORMAT      
		            defaultDate: "<?php echo !empty($meeting_data->schedule_time)?date('m-d-Y H:i', strtotime($meeting_data->schedule_time)):'';?>",
		        });
        	}else{
        		$('#datetimepicker5').datetimepicker({
		            changeMonth: true,
		            changeYear: true,
		            yearRange: '<?php echo (date('Y'));?>:<?php echo (date('Y')+5);?>',
		            format: 'MM-DD-YYYY hh:mm A',
                    ampm: true, // FOR AM/PM FORMAT
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
                    var school_name = jQuery.parseJSON(JSON.stringify(data.school_name)); 
                    var school_type = data.mentee_ids[0].school_type;
                    // var school_type = jQuery.parseJSON(JSON.stringify(data.school_type)); 
                    var school_id = jQuery.parseJSON(JSON.stringify(data.school_id)); 
	                var is_school = jQuery.parseJSON(JSON.stringify(data.is_school)); 

                    console.log(is_school);
                    console.log(mentee);
                    console.log(school_type);
	                console.log(school_id);

                    if(school_type != ''){
                        $('#school_type').val(school_type);
                    }
	                // return true;                
	                
                    var setData = "";
	                var setSchoolData = "";
	                if(mentee.length > 0){
	                	
	                    for (var i = 0; i < mentee.length; i++) {
	                        // var mentees = mentee[i].user_id;
	                        // meArr.push(mentees);
	                        var firstname = mentee[i].firstname;
	                        var middlename = mentee[i].middlename;
                        	var lastname = mentee[i].lastname;
	                        
                            setData += `<option value="`+mentee[i].user_id+`">`+firstname+` `+middlename+` `+lastname+`</option>`;

                            var school_selected = "";
                            var office_selected = "";

                            if(is_school == 1){
                                school_selected = "selected";
                            }else{
                                if(school_type == 'Affiliate Office'){
                                    office_selected = "selected";
                                }else if(school_type == 'Virtual Session'){
                                    office_selected = "selected";
                                }

                                // console.log(school_type);
                                
                            }

                            
                            if(school_name != ''){
                                setSchoolData += `<option value="`+school_id+`" `+school_selected+` data-school-name="`+school_name+`" >`+school_name+`</option>`;
                            }
                            
	                        setSchoolData += `<option value="0"  `+office_selected+` data-school-name="Affiliate Office">Affiliate Office</option>`;
                            setSchoolData += `<option value="0"  `+office_selected+` data-school-name="Virtual Session">Virtual Session</option>`;
	                    }
	                }else{
                        setData += `<option> No mentee found </option>`;
	                	setSchoolData += `<option> No session location </option>`;
	                }

                    $('#mentee_ids').html(setData);
	                $('#school_id').html(setSchoolData);
	                    
	                    

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
                var setDataSchool = "";
                if(mentee.length > 0){
                	setData += `<option value=""> Select a mentee </option>`;
                    for (var i = 0; i < mentee.length; i++) {
                        // var mentees = mentee[i].user_id;
                        // meArr.push(mentees);
                        var firstname = mentee[i].firstname;
                        var middlename = mentee[i].middlename;
                        var lastname = mentee[i].lastname;
                        var school_id = mentee[i].school_id;
                        var school_name = mentee[i].school_name;
                        
                        setData += `<option value="`+mentee[i].mentee_id+`" data-school-id="`+school_id+`" data-school-name="`+school_name+`">`+firstname+` `+middlename+` `+lastname+`</option>`;
                    }
                }else{
                	setData += `<option value=""> No mentee found </option>`;
                }

                $('#mentee_ids').html(setData);
                $('#school_id').html(setDataSchool);
                    
                    

            }
        });


    }

    $(function() {
      $('#mentee_ids').change(function() {
        var mentee_school_id = $(this).find('option:selected').attr('data-school-id');
        var mentee_school_name = $(this).find('option:selected').attr('data-school-name');
        // $("#money").val(mentee_school_id);
        console.log(mentee_school_id);
        console.log(mentee_school_name);
        var setData = "";
        setData += `<option value="`+mentee_school_id+`" data-school-name="`+mentee_school_name+`">`+mentee_school_name+`</option>`;
        setData += `<option value="0" data-school-name='Affiliate Office'>Affiliate Office</option>`;
        setData += `<option value="0" data-school-name='Virtual Session'>Virtual Session</option>`;

        $('#school_id').html(setData);

      })
    });




    $(function () {

        $('#datetimepicker5').datetimepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: '<?php echo (date('Y'));?>:<?php echo (date('Y')+5);?>',
            format: 'MM-DD-YYYY hh:mm A',
            ampm: true, // FOR AM/PM FORMAT
            // timeZone : 'America/New_York',
            minDate: new Date()
            // locale: 'de',
            
        });
    });

    $('#school_id').on('change', function() {
        // alert( this.value );
        var mentee_school_id = $(this).find('option:selected').attr('value');
        var mentee_school_name = $(this).find('option:selected').attr('data-school-name');

        console.log(mentee_school_id);
        console.log(mentee_school_name);

        if(mentee_school_id == 0){
            $('#school_type').val(mentee_school_name);
        }else{
            $('#school_type').val('');
        }

    });

    function formsubmit() {
        var title = $('#title').val();
        var description = $('#description').val();        
        var mentor_id = $('#mentor_ids').val();
        var mentee_ids = $('#mentee_ids').val();
        var school_id = $('#school_id').val();
        var session_method_location_id = $('#session_method_location_id').val();
        var school_location = $('#school_location').val();
        
        var datetimepicker5 = $('#datetimepicker5').val();

        console.log(title);
        console.log(mentor_id);
        console.log(mentee_ids);

        if(title == ''){
            swal('Agenda is required');
            return false;
        }
        // else if (description == '') {
        //     swal('Description is required');
        //     return false;
        // }
        else if (mentor_id == '') {
            swal('Please add mentor');
            return false;
        }else if (mentee_ids == '') {
            swal('Please add mentee');
            return false;
        }
        else if(session_method_location_id == ''){
            swal('Please add method location');
            return false;
        }
        else if (datetimepicker5 == '') {
            swal('Please add Schedule');
            return false;
        }
        // else if(school_location == ''){
        //     swal('Please add school location');
        //     return false;
        // }
        // return true;

        $('#submit_btn').prop('disabled', true);
        $('#submit_form').submit();   
    }

</script>
@endsection
