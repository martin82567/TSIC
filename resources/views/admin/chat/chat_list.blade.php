@extends('layouts.admin')

@section('content')
<div class="db-inner-content">
<div class="db-box">
    <div class="heading-sec">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <?php if(empty($view_chat)){?>
                <?php if($type == 'mentor'){?>
                <h3>Mentors</h3>
                <?php }else if($type == 'mentee'){?>
                <h3>Mentees</h3>
                <?php }?>
                <?php }else {?>
                <?php if($type == 'mm'){
                        $show_type = "Mentor Mentee Chats";
                    }else if($type == 'ms'){
                        $show_type = "Mentor Staff Chats";
                    }else if($type == 'menteestaff'){
                        $show_type = "Mentee Staff Chats";
                    }
                ?>
                <h3><?php echo $show_type; ?></h3>
                <?php }?>
            </div>
            <?php 
            if(empty($view_chat)){
            ?>
            <div class="col-lg-4">
                <div class="search-sec">
                    <form action="{{url('/admin/chat')}}" id="search_form">
                        
                        <input type="hidden"  name="type" value="<?php echo $type; ?>">
                        <input type="hidden" name="view_chat" value="<?php echo $view_chat; ?>">
                        <input type="text" placeholder="Search" id="search" name="search" value="<?php echo !empty($search)?$search:''; ?>">                               
                        <input type="hidden"  id="sort1" name="sort1" value="<?php echo !empty($sort1)?$sort1:''; ?>">                               
                        <input type="hidden"  id="column_name1" name="column_name1" value="<?php echo !empty($column_name1)?$column_name1:''; ?>">      
                                                     
                    </form>
                </div>
            </div>
            <?php 
                
            }?>
        </div>
    </div>
    
    <?php if(!empty(session('success_message'))){ ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
            <h4 style="margin-bottom: 0"><i class="icon fa fa-check"></i>
        <?php 
            echo session('success_message'); 
            Session::forget('success_message');
        ?></h4>
        </div>
    <?php } ?>
    <?php if(!empty($is_super)){?>
    <div class="box-inner">
        <div class="form-section">               
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <!-- <div class="select"> -->
                            <select name="affiliate_id" id="affiliate_id" class="form-control select2">
                                <option value="">Choose a affiliate</option>
                                @if(!empty($affiliates))
                                @foreach($affiliates as $a)
                                <option value="{{$a->id}}">{{$a->name}}</option>
                                @endforeach
                                @endif
                            </select>
                        <!-- </div> -->
                    </div>
                </div>                    
            </div>                      
        </div>
    </div>
    <?php }?>
    <div class="box-inner" id="user_lists">
        <?php if(empty($view_chat)){?>
        <?php if($type == 'mentor'){?>
        <div class="listing-table">
            <div class="table-responsive text-center">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>
                                Mentor
                                <span>
                                    <?php if(empty($sort1)){ ?>
                                        <a href="javascript:void(0);" onclick="sort_data('asc','firstname');">
                                            <i class="fa fa-sort"></i>
                                        </a>
                                    <?php }else{ ?> 
                                        <?php if($sort1 == 'asc'){ ?> 
                                            <a href="javascript:void(0);" onclick="sort_data('desc','firstname');" >
                                                <i class="fa fa-sort-asc"></i>
                                            </a>
                                        <?php }else{ ?> 
                                            <a href="javascript:void(0);" onclick="sort_data('asc','firstname');" >
                                                <i class="fa fa-sort-desc"></i>
                                            </a>
                                        <?php } ?>
                                    <?php } ?>
                                </span>
                            </th>
                            <th>
                                <?php if(!empty($read_type)){ 
                                        echo 'Unread Chat'; 
                                        $text = "All";
                                        $read_url = url('/admin/chat?type=mentor&view_chat=0');
                                    }else{
                                        echo 'Chat'; 
                                        $text = "Unread";
                                        $read_url = url('/admin/chat?type=mentor&read_type=unread&view_chat=0');
                                    }
                                 ?>
                                <span>
                                    <a href="{{$read_url}}" class="btn btn-primary">{{$text}}</a>
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($mentor as $m)    
                    <?php 
                    $unread_chat_message_staff = unread_chat_message_staff('mentor','mentor_staff_chat_threads',Auth::user()->id,$m->id);
                    ?>                
                        <tr>
                            <td>{{ $m->firstname }} {{ $m->middlename }} {{ $m->lastname }}
                                <?php if(!empty($unread_chat_message_staff)){?>
                                <span class="badge bg-green chat-unread"><?php echo $unread_chat_message_staff; ?></span>
                                <?php }?>
                            </td>
                            <td><a href="{{ url('/admin/chat/get_mentor_staff_chatcode')}}?mentor_id=<?php echo Crypt::encrypt($m->id);?>&staff_id=<?php echo Crypt::encrypt(Auth::user()->id);?>" class="btn btn-primary">Go</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $mentor->links()}}
        </div>
        <?php }else if($type == 'mentee'){?>
        <div class="listing-table">
            <div class="table-responsive text-center">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>
                                Mentee
                                <?php if(empty($sort1)){ ?>
                                    <a href="javascript:void(0);" onclick="sort_data('asc','firstname');">
                                        <i class="fa fa-sort"></i>
                                    </a>
                                <?php }else{ ?> 
                                    <?php if($sort1 == 'asc'){ ?> 
                                        <a href="javascript:void(0);" onclick="sort_data('desc','firstname');" >
                                            <i class="fa fa-sort-asc"></i>
                                        </a>
                                    <?php }else{ ?> 
                                        <a href="javascript:void(0);" onclick="sort_data('asc','firstname');" >
                                            <i class="fa fa-sort-desc"></i>
                                        </a>
                                    <?php } ?>
                                <?php } ?>
                            </th>
                            <th>
                                <?php if(!empty($read_type)){ 
                                        echo 'Unread Chat'; 
                                        $text = "All";
                                        $read_url = url('/admin/chat?type=mentee');
                                    }else{
                                        echo 'Chat'; 
                                        $text = "Unread";
                                        $read_url = url('/admin/chat?type=mentee&read_type=unread');
                                    }
                                 ?>
                                <span>
                                    <a href="{{$read_url}}" class="btn btn-primary">{{$text}}</a>
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($mentee as $m) 

                    <?php 
                    $unread_chat_message_staff = unread_chat_message_staff('mentor','mentee_staff_chat_threads',Auth::user()->id,$m->id);
                    ?>                   
                        <tr>
                            <td>{{ $m->firstname }} {{ $m->middlename }} {{ $m->lastname }}
                                <?php if(!empty($unread_chat_message_staff)){?>
                                <span class="badge bg-green chat-unread"><?php echo $unread_chat_message_staff; ?></span>
                                <?php }?>
                            </td>
                            <td><a href="{{ url('/admin/chat/get_mentee_staff_chatcode')}}?mentee_id=<?php echo Crypt::encrypt($m->id);?>&staff_id=<?php echo Crypt::encrypt(Auth::user()->id);?>" class="btn btn-primary">Go</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $mentee->links()}}
        </div>
        <?php }?>
        <?php }else { ?>
        <div class="form-section">
            <?php if($type == 'mm'){?>
            <form method="" action="{{url('/admin/chat/threads')}}">
            <div class="row">                
                
                <input type="hidden" name="type" value="<?php echo $type; ?>">
                <input type="hidden" name="view_chat" value="<?php echo $view_chat; ?>">
                <div class="col-md-5">
                    <div class="form-group">
                        <!-- <div class="select"> -->
                            <select name="mentor_id" id="mentor_id_mentee" class="form-control select2 view_mentors">
                                <option value="">Choose a mentor</option>
                                @if(!empty($mentor))
                                @foreach($mentor as $m)
                                <option value="{{$m->id}}">{{$m->firstname}} {{$m->middlename}} {{$m->lastname}}</option>
                                @endforeach
                                @endif
                            </select>
                        <!-- </div> -->
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <!-- <div class="select"> -->
                            <select name="mentee_id" id="mentee_id" class="form-control select2 view_mentees">
                                <option value="">Choose a mentee</option>
                                
                            </select>
                        <!-- </div> -->
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <button class="btn btn-success" type="submit">Show</button>
                    </div>
                </div>                
            </div>
            </form>
            <?php }else if($type == 'ms'){?>
            <form method="" action="{{url('/admin/chat/threads')}}">
            <div class="row">                
                
                <input type="hidden" name="type" value="<?php echo $type; ?>">
                <input type="hidden" name="view_chat" value="<?php echo $view_chat; ?>">
                <div class="col-md-5">
                    <div class="form-group">
                        <!-- <div class="select"> -->
                            <select name="mentor_id" id="mentor_id_staff" class="form-control select2 view_mentors">
                                <option value="">Choose a mentor</option>
                                @if(!empty($mentor))
                                @foreach($mentor as $m)
                                <option value="{{$m->id}}">{{$m->firstname}} {{$m->middlename}} {{$m->lastname}}</option>
                                @endforeach
                                @endif
                            </select>
                        <!-- </div> -->
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <!-- <div class="select"> -->
                            <select name="staff_id" id="staff_id" class="form-control select2 view_staffs">
                                <option value="">Choose a staff</option>
                                @if(!empty($staff))
                                @foreach($staff as $s)
                                <option value="{{$s->id}}">{{$s->name}}</option>
                                @endforeach
                                @endif
                            </select>
                        <!-- </div> -->
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <button class="btn btn-success" type="submit">Show</button>
                    </div>
                </div>                
            </div>
            </form>
            <?php }else if($type == 'menteestaff'){?>
            <form method="" action="{{url('/admin/chat/threads')}}">
            <div class="row">                
                
                <input type="hidden" name="type" value="<?php echo $type; ?>">
                <input type="hidden" name="view_chat" value="<?php echo $view_chat; ?>">
                <div class="col-md-5">
                    <div class="form-group">
                        <!-- <div class="select"> -->
                            <select name="mentee_id" id="" class="form-control select2 view_mentees">
                                <option value="">Choose a mentee</option>
                                @if(!empty($mentee))
                                @foreach($mentee as $m)
                                <option value="{{$m->id}}">{{$m->firstname}} {{$m->middlename}} {{$m->lastname}}</option>
                                @endforeach
                                @endif
                            </select>
                        <!-- </div> -->
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <!-- <div class="select"> -->
                            <select name="staff_id" id="" class="form-control select2 view_staffs">
                                <option value="">Choose a staff</option>
                                @if(!empty($staff))
                                @foreach($staff as $s)
                                <option value="{{$s->id}}">{{$s->name}}</option>
                                @endforeach
                                @endif
                            </select>
                        <!-- </div> -->
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <button class="btn btn-success" type="submit">Show</button>
                    </div>
                </div>                
            </div>
            </form>
            <?php }?>
        </div>
        <?php }?>
    </div>
</div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var is_super = '<?php echo $is_super; ?>';
        console.log(is_super);
        
    });
    $(function() {
        $(".select2").select2();
    });

    $('#affiliate_id').on('change', function(){
        
        var affiliate_id = $('#affiliate_id').val();
        $.ajax({
           url: "{{ url('/admin/chat/get_data_from_affiliate') }}",
           type: "POST",
           data: {               
               "affiliate_id": affiliate_id,
               "_token": "{{ csrf_token() }}",
           },
           dataType: "json",
           success: function(data) {
                var mentor = jQuery.parseJSON(JSON.stringify(data.mentor));
                var mentee = jQuery.parseJSON(JSON.stringify(data.mentee));
                var staff = jQuery.parseJSON(JSON.stringify(data.staff));

                console.log(mentor);
                console.log(mentee);
                console.log(staff);

                var mentor_data = "";
                mentor_data += `<option value="" >Choose a mentor</option>`; 
                if(mentor.length != 0){
                    for (var i = 0; i < mentor.length; i++) {                    
                        mentor_data += `<option value="`+mentor[i].id+`" >`+mentor[i].firstname+` `+mentor[i].lastname+`</option>`;                        
                    }
                }else{
                    mentor_data += `<option value="" >No mentor found</option>`;
                }  
                $(".view_mentors").html(mentor_data);

                var mentee_data = "";
                mentee_data += `<option value="" >Choose a mentee</option>`; 
                if(mentee.length != 0){
                    for (var i = 0; i < mentee.length; i++) {                    
                        mentee_data += `<option value="`+mentee[i].id+`" >`+mentee[i].firstname+` `+mentee[i].lastname+`</option>`;                        
                    }
                }else{
                    mentee_data += `<option value="" >No mentee found</option>`;
                }  
                $(".view_mentees").html(mentee_data);

                var staff_data = "";
                staff_data += `<option value="" >Choose a staff</option>`; 
                if(staff.length != 0){
                    for (var i = 0; i < staff.length; i++) {                    
                        staff_data += `<option value="`+staff[i].id+`" >`+staff[i].name+`</option>`;                        
                    }
                }else{
                    staff_data += `<option value="" >No staff found</option>`;
                }  
                $(".view_staffs").html(staff_data);


                console.log(data);
               // $('#view_all_related_tips').html(data);
           }
       });
    });

    $('#mentor_id_mentee').on('change', function(){
        var select_type = "mentee";
        var mentor_id_mentee = $('#mentor_id_mentee').val();
        $.ajax({
           url: "{{ url('/admin/chat/get_data_from_mentor') }}",
           type: "POST",
           data: {
               "select_type": select_type,
               "mentor_id": mentor_id_mentee,
               "_token": "{{ csrf_token() }}",
           },
           dataType: "json",
           success: function(data) {
                var b = jQuery.parseJSON(JSON.stringify(data.val));
                var mentee_data = "";
                mentee_data += `<option value="" >Choose a mentee</option>`; 
                if(b.length != 0){
                    for (var i = 0; i < b.length; i++) {                    
                        mentee_data += `<option value="`+b[i].id+`" >`+b[i].firstname+` `+b[i].middlename+` `+b[i].lastname+`</option>`;                        
                    }
                }else{
                    mentee_data += `<option value="" >No mentee found</option>`;
                }                   
                
                $("#mentee_id").html(mentee_data);
                console.log(data);
               // $('#view_all_related_tips').html(data);
           }
       });
    });


    /*$('#mentor_id_staff').on('change', function(){
        var select_type = "staff";
        var mentor_id_staff = $('#mentor_id_staff').val();
        $.ajax({
           url: "{{ url('/admin/chat/get_data_from_mentor') }}",
           type: "POST",
           data: {
               "select_type": select_type,
               "mentor_id": mentor_id_staff,
               "_token": "{{ csrf_token() }}"
           },
           dataType: "json",
           success: function(data) {
            var b = jQuery.parseJSON(JSON.stringify(data.val));
            var staff_data = "";
            staff_data += `<option value="" >Choose a staff</option>`; 
            if(b.length != 0){
                for (var i = 0; i < b.length; i++) {                    
                    staff_data += `<option value="`+b[i].id+`" >`+b[i].name+`</option>`;                        
                }
            }else{
                staff_data += `<option value="" >No staff found</option>`;
            }                   
            
            $("#staff_id").html(staff_data);

            console.log(data);
               // $('#view_all_related_tips').html(data);
           }
       });
    });*/
    function sort_data(sort_val,value){
        
        $('#sort1').val(sort_val);
        $('#column_name1').val(value);
        
        $('#search_form').submit();
    }
</script>
@endsection
