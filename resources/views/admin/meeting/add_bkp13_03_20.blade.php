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
                        <h3>
                            Meeting</h3>
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
                                <?php if(empty($meeting_data->id)){?>Create<?php }else{?>Edit<?php }?> Meeting</h3>
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
                                <select class="selectboxmultimentor" id="mentor_ids" name="mentor_ids[]" required="required" multiple="multiple" style="width: 100%">
                                    <?php if(!empty($mentors)){ foreach($mentors as $mn){?>
                                    <option value="<?php echo $mn->id; ?>" <?php if(!empty($meeting_data->mentor_id) && $meeting_data->mentor_id == $mn->id){?> selected <?php }?>>
                                        <?php echo $mn->firstname.' '.$mn->middlename.' '.$mn->lastname; ?>
                                    </option>
                                    <?php }}?>                                                 
                                </select>                                
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Choose Mentees <sup style="color: #ff6c6c;">*</sup></label>
                                <select class="selectboxmultimentee" id="mentee_ids" name="mentee_ids[]" required="required"  multiple="multiple" style="width: 100%">
                                    <?php if(!empty($mentees)){ foreach($mentees as $me){?>
                                        <option value="<?php echo $me->id; ?>" <?php if(!empty($meeting_data->mentor_id) && $meeting_data->mentor_id == $me->id){?> selected <?php }?>>
                                            <?php echo $me->firstname.' '.$me->middlename.' '.$me->lastname; ?>
                                        </option>
                                        <?php }}?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">                                                
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Schedule Date &amp; Time<sup style="color: #ff6c6c;">*</sup></label>
                                <input type="text" class="form-control datetimepicker-input" id="datetimepicker5" data-toggle="datetimepicker" data-target="#datetimepicker5"  name="schedule_time" value="<?php //echo !empty($meeting_data->schedule_time)?$meeting_data->schedule_time:'';?>">

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
<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script> -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/js/tempusdominus-bootstrap-4.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css" />
<script>


    

    $(document).ready(function() {
        $('.selectboxmultimentor').select2();
        $('.selectboxmultimentee').select2();

        var id = $('#id').val();
        if(id != ''){


	        $('#datetimepicker5').datetimepicker({
	            changeMonth: true,
	            changeYear: true,
	            yearRange: '<?php echo (date('Y'));?>:<?php echo (date('Y')+5);?>',
	            format: 'YYYY-MM-DD HH:mm:ss ',
	            minDate: new Date(),
	            defaultDate: "<?php echo !empty($meeting_data->schedule_time)?$meeting_data->schedule_time:'';?>",
	        });
		    
            
            console.log('Hi');
            $('.selectboxmultimentor').select2().val([]).trigger("change");
            $('.selectboxmultimentee').select2().val([]).trigger("change");
            var mrArr = [];
            var meArr = [];
            $.ajax({
                url: "{{ url('/admin/agency/get_mentee_from_meeting_data') }}",
                type: "POST",
                data: {
                   id: id,
                   _token: "{{ csrf_token() }}"
                },                
                dataType: "json",
                success: function(data) {
                    var mentor_ids = jQuery.parseJSON(JSON.stringify(data.mentor_ids)); 
                    var mentee_ids = jQuery.parseJSON(JSON.stringify(data.mentee_ids)); 
                    console.log(mentor_ids);
                    console.log(mentee_ids);

                    
                        if(mentor_ids.length > 0){
                            for (var i = 0; i < mentor_ids.length; i++) {
                                var mentors = mentor_ids[i].user_id;
                                mrArr.push(mentors);
                            }
                        }

                        if(mentee_ids.length > 0){
                            for (var i = 0; i < mentee_ids.length; i++) {
                                var mentees = mentee_ids[i].user_id;
                                meArr.push(mentees);
                            }
                        }
                        
                        $('.selectboxmultimentor').select2().val(mrArr).trigger("change");
                        $('.selectboxmultimentee').select2().val(meArr).trigger("change");
                    

                }
            });
        }
    });

    $(function () {
        $('#datetimepicker5').datetimepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: '<?php echo (date('Y'));?>:<?php echo (date('Y')+5);?>',
            format: 'YYYY-MM-DD HH:mm:ss ',
            minDate: new Date()
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



    
    // $('#is_active').click(function() {
    //     if ($(this).is(':checked'))
    //         $('#check_title').text('Active');
    //     else
    //         $('#check_title').text('Inactive');
    // });

</script>
@endsection
