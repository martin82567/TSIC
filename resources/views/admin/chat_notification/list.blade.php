@extends('layouts.admin')
@section('content')
<div class="db-inner-content">
<div class="db-box">
    <div class="heading-sec">
        <div class="row align-items-center">
            <div class="col-md-6">
               <h3><?php echo ($type == 'reviewed')?'Archived':ucwords($type);?> Keyword Notification</h3>
            </div>
            
        </div>
    </div>
    
    <div class="alert alert-success alert-dismissible" id="alert_review" style="display: none;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
        <h4 style="margin-bottom: 0"><i class="icon fa fa-check"></i></h4>
    </div>
    
    <div class="box-inner">
    	<?php if($type == 'unreviewed'){?>
        <form action="{{ url('admin/chat/keyword-notification-unreviewed') }}" id="search_form">
        <?php }else{ ?>
        <form action="{{ url('admin/chat/keyword-notification-reviewed') }}" id="search_form">
        <?php }?>
            <input type="hidden" name="sort" id="sort" value="<?php if(!empty($sort_needed)){ echo $sort;  } ?>">
            <input type="hidden" name="column" id="column" value="<?php if(!empty($sort_needed)){ echo $column;  } ?>">
        </form>
        <?php if($type == 'unreviewed'){?>
        <div class="">                              
            <button id="make_rev" class="btn btn-success">Archive</button>
        </div>
        <?php }?>
        <div class="listing-table">
            <div class="table-responsive text-center">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <?php if($type == 'unreviewed'){?>
                            <th>
                                <input type="checkbox" id="selectall" onclick="selectAll(this)">
                            </th>
                            <?php }?>
                            <th>Chat Type</th>
                            <th>Sender User</th>
                            <th>Sender Name</th>
                            <th>Receiver User</th>
                            <th>Receiver Name</th>
                            <th>Message</th>                            
                            <th>Keywords</th>                            
                            <th>Status</th>                            
                            <th>Date&Time
                                <?php //if($type == 'reviewed'){?>
                                <span>
                                    <?php if(!empty($column) && $column != 'date' || empty($sort_needed)){ ?>
                                        <a href="javascript:void(0);" onclick="sort_data('<?php echo $sort; ?>','date');">
                                            <i class="fa fa-sort"></i>
                                        </a>
                                    <?php }else{ ?> 
                                        <?php if($sort == 'asc'){ ?> 
                                            <a href="javascript:void(0);" onclick="sort_data('desc','date');" >
                                                <i class="fa fa-sort-asc"></i>
                                            </a>
                                        <?php }else{ ?> 
                                            <a href="javascript:void(0);" onclick="sort_data('asc','date');" >
                                                <i class="fa fa-sort-desc"></i>
                                            </a>
                                        <?php } ?>
                                    <?php } ?>
                                </span>
                                <?php //}?>
                            </th>
                            
                        </tr>
                    </thead>
                    <tbody>
                    
                    <?php 
                    if(!empty($data)){
                    	foreach ($data as $d){   

                    		if($d->chat_type == 'mentee-staff'){
                    			if($d->from_where == 'mentee'){
                    				$sender_user = "Mentee";
                    				$sender_data = get_single_data_id('mentee',$d->sender_id);
                    				$sender_name = $sender_data->firstname.' '.$sender_data->middlename.' '.$sender_data->lastname;

                    				$receiver_user = "Staff";
                    				$receiver_data = get_single_data_id('admins',$d->receiver_id);
                    				$receiver_name = $receiver_data->name;
                    			}else{
                    				$sender_user = "Staff";
                    				$sender_data = get_single_data_id('admins',$d->sender_id);
                    				$sender_name = $sender_data->name;

                    				$receiver_user = "Mentee";
                    				$receiver_data = get_single_data_id('mentee',$d->receiver_id);
                    				$receiver_name = $receiver_data->firstname.' '.$receiver_data->middlename.' '.$receiver_data->lastname;

                    			}

                    		}else if($d->chat_type == 'mentor-mentee'){
                    			if($d->from_where == 'mentor'){
                    				$sender_user = "Mentor";
                    				$sender_data = get_single_data_id('mentor',$d->sender_id);
                    				$sender_name = $sender_data->firstname.' '.$sender_data->middlename.' '.$sender_data->lastname;

                    				$receiver_user = "Mentee";
                    				$receiver_data = get_single_data_id('mentee',$d->receiver_id);
                    				$receiver_name = $receiver_data->firstname.' '.$receiver_data->middlename.' '.$receiver_data->lastname;
                    			}else{
                    				$sender_user = "Mentee";
                    				$sender_data = get_single_data_id('mentee',$d->sender_id);
                    				$sender_name = $sender_data->firstname.' '.$sender_data->middlename.' '.$sender_data->lastname;

                    				$receiver_user = "Mentor";
                    				$receiver_data = get_single_data_id('mentor',$d->receiver_id);
                    				$receiver_name = $receiver_data->firstname.' '.$receiver_data->middlename.' '.$receiver_data->lastname;

                    			}

                    		}else if($d->chat_type == 'mentor-staff'){
                    			if($d->from_where == 'mentor'){
                    				$sender_user = "Mentor";
	                				$sender_data = get_single_data_id('mentor',$d->sender_id);
	                				$sender_name = $sender_data->firstname.' '.$sender_data->middlename.' '.$sender_data->lastname;

	                				$receiver_user = "Staff";
                    				$receiver_data = get_single_data_id('admins',$d->receiver_id);
                    				$receiver_name = $receiver_data->name;
                    			}else{
                    				$sender_user = "Staff";
                    				$sender_data = get_single_data_id('admins',$d->sender_id);
                    				$sender_name = $sender_data->name;

                    				$receiver_user = "Mentor";
                    				$receiver_data = get_single_data_id('mentor',$d->receiver_id);
                    				$receiver_name = $receiver_data->firstname.' '.$receiver_data->middlename.' '.$receiver_data->lastname;
                    			}                    			
                    		}

                            if(Auth::user()->type == 1){
                                $is_flagged = $d->is_super_admin_flagged;
                            }else if(Auth::user()->type == 2){
                                $is_flagged = $d->is_affiliate_flagged;
                            }else if(Auth::user()->type == 3){
                                if(Auth::user()->parent_id != 1){
                                    $is_flagged = $d->is_lead_staff_flagged;
                                }else{
                                    $is_flagged = $d->is_super_staff_flagged;
                                }
                                
                            }


                    ?>

                    <tr>  
                        <?php if($type == 'unreviewed'){?>
                        <td><input type="checkbox" name="rev[]" value="<?php echo  $d->id ; ?>"></td>
                        <?php }?>                   
                        <td><?php echo  $d->chat_type ; ?></td>                          
                        <td><?php echo  $sender_user ; ?></td>
                        <td><?php echo  $sender_name ; ?></td>
                        <td><?php echo  $receiver_user ; ?></td>
                        <td><?php echo  $receiver_name ; ?></td>
                        <td><?php echo  (strlen($d->message)>100)?substr($d->message,0,100).'...':$d->message ; ?></td>                        
                        <td><?php echo (strlen($d->keywords)>100)?substr($d->keywords,0,100).'...':$d->keywords ; ?></td>                        
                        <td>
                            
                            <span id="flag_span_{{$d->id}}"><?php echo  !empty($is_flagged)?"Flagged":"" ; ?></span>
                            <?php if(empty($is_flagged) && $type == 'unreviewed'){?>
                            
                            <a href="javascript:void(0);" onclick="make_flagged('<?php echo $d->id; ?>');" class="btn btn-success" id="flag_btn_{{$d->id}}">Flag</a>
                            <?php }?>    
                        </td>
                        
                        <td><?php echo  get_standard_datetime($d->created_at) ; ?></td>
                        
                    </tr>
                    <?php 
                    	}
                    }?>
                    </tbody>
                </table>
                {{$data->links()}}
            </div>
        </div>
    </div>

</div>
</div>
<script type="text/javascript">
    function sort_data(sort_val,column) {
        $('#sort').val(sort_val);
        $('#column').val(column);
        $('#search_form').submit();
    }


    function selectAll(source) {
        checkboxes = document.getElementsByName('rev[]');
        for(var i in checkboxes)
            checkboxes[i].checked = source.checked;


        console.log(checkboxes);

    }

    $("#make_rev").click(function(){  

        $("#make_rev").attr("disabled", true); 
        var revs = [];
        $("input[name='rev[]']:checked").each(function(){            
            revs.push($(this).val());
        });



        console.log(revs);

        if(revs.length != 0){
            $.ajax({
               url: "{{url('/admin/chat/make-reviewed-notification')}}",
               type: "POST",
               data: {
                   ids: revs,
                   user_id: '<?php echo Auth::user()->id; ?>',
                   user_type: '<?php echo Auth::user()->type; ?>',
                   "_token":"{{ csrf_token() }}"
               },
               dataType: "json",
               success: function(data) {
                    console.log(data);
                    if(data.status == true){                        
                        window.location.href = "{{url('/admin/chat/keyword-notification-unreviewed')}}";
                    
                    }
                }
            });
        }else{
            return alert('Please choose any notification');
        }                    
    });

    function make_flagged(i)
    {
        $('#flag_btn_'+i).attr("disabled", true);
        $.ajax({
           url: "{{url('/admin/chat/make-flagged-notification')}}",
           type: "POST",
           data: {
               id: i,
               user_id: '<?php echo Auth::user()->id; ?>',
               user_type: '<?php echo Auth::user()->type; ?>',
               "_token":"{{ csrf_token() }}"
           },
           dataType: "json",
           success: function(data) {
                console.log(data);
                if(data.status == true){                        
                    $('#flag_span_'+i).html('Flagged');
                    $('#flag_btn_'+i).hide();
                
                }
            }
        });
        
    }

</script>
@endsection
