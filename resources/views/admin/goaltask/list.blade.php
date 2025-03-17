@extends('layouts.admin') @section('content')
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <?php if(Request::segment(2) == 'goaltask'){?>
                    <h3>Active Goals / Assignment </h3>
                    <?php }else{?>
                    <h3>Inactive Goals / Assignment </h3>
                    <?php }?>
                </div>
                <div class="col-md-6">
                    <div class="search-sec">
                        <form action="{{ route('admin.goaltask') }}">
                            <input type="text" placeholder="Search" id="search" name="search">

                        </form>
                    </div>
                    <div class="text-right">
                        <?php $a = \Crypt::encrypt(0);?>
                        <a class="btn btn-success btn-sm" href="<?php echo url('admin/goaltask/add/'.$a); ?>">Add Goal / Assignment </a>
                    </div>
                </div>
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
        <div class="box-inner">

            <?php $agency_staff = 0;
        if(Auth::user()->type == 1){
            $agency_staff = 1;
        }else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
            $agency_staff = 1;
        }
        ?>
            <div class="listing-table">
                <div class="table-responsive text-center">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <?php if(!empty($agency_staff)){?>
                                <th>Creator</th>
                                <th>Agency</th>
                                <?php }?>
                                <th>Created From</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($goaltask_arr as $data)

                            <?php
                            if($data->type == "goal"){
                                $assigndata = DB::table('assign_goal')->where('goaltask_id',$data->id)->get();
                            }else if($data->type == "task"){
                                $assigndata = DB::table('assign_task')->where('goaltask_id',$data->id)->get();
                            }else{
                                $assigndata = DB::table('assign_challenge')->where('goaltask_id',$data->id)->get();
                            }

                            $assign_data_arr = array();
                            $assign_all_data_arr = array();
                            if(!empty($assigndata)){
                                $count = 0;
                                foreach($assigndata as $assgn){
                                    $assign_data_arr[] = $assgn->victim_id;
                                    $assign_all_data_arr[$count]['status'] = $assgn->status;
                                    $assign_all_data_arr[$count]['begin_time'] = $assgn->begin_time;
                                    $assign_all_data_arr[$count]['complated_time'] = $assgn->complated_time;
                                    $assign_all_data_arr[$count]['note'] = $assgn->note;
                                    $count++;
                                }
                            }

                            $assign_users = array();
                        
                            if(!empty($agency_staff)){                            
                                if(!empty($data->created_by)){
                                    $agency_name_val = DB::table('admins')->select('name')->where('id',$data->created_by)->first();
                                    $agency_name = $agency_name_val->name;
                                }else{
                                    $agency_name = '';                                      
                                }
                                
                            } 

                            $staff_name = '';

                            if(!empty($data->staff_id)){
                                if($data->user_type == 'mentor'){
                                    $staff_id_val = DB::table('mentor')->select('firstname','lastname')->where('id',$data->staff_id)->first();
                                    $staff_id = $staff_id_val->firstname.' '.$staff_id_val->lastname;
                                }else{
                                    $staff_id_val = DB::table('admins')->select('name')->where('id',$data->staff_id)->first();
                                    
                                    $staff_id = !empty($staff_id_val->name)?$staff_id_val->name:'';
                                }                            
                            }else{
                                $staff_id = '';                                      
                            }

                            if($data->created_by != $data->staff_id && !empty($data->staff_id)){
                                $agency_name = $staff_id;
                            }

                            $created_by = DB::table('admins')->where('id', $data->created_by)->first();
                            $created_by_name = $created_by->name;

                            ?>
                            <tr>
                                <?php if(!empty($agency_staff)){?>
                                <td>
                                    <?php echo $agency_name; ?>
                                </td>
                                <td>{{ $created_by_name }}</td>
                                <?php }?>
                                <td><?php if($data->user_type == ''){ echo 'Web(Affiliate)' ;}else{ echo 'App(Mentor)';}?></td>
                                <td><?php echo (strlen($data->name) > 50)?substr($data->name,0,50).'...':$data->name; ?></td>
                                <td>
                                    <?php if($data->type == "task"){ ?>
                                    <i class="fa fa-tasks" aria-hidden="true" title="Task"></i>
                                    <?php }else if($data->type == "challenge"){ ?>
                                    <i class="fa fa-trophy" title="Challenge"></i>
                                    <?php }else{ ?>
                                    <i class="fa fa-bullseye" title="Goal"></i>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if(empty($data->status)){ ?>
                                    <span id="span_<?php echo $data->id; ?>">Inactive</span>
                                    <?php }else if($data->status == 1){?>
                                    <span id="span_<?php echo $data->id; ?>">Active</span>
                                    <?php }else if($data->status == 2){?>
                                    <span id="span_<?php echo $data->id; ?>">Begin</span>
                                    <?php }else{?>
                                    <span id="span_<?php echo $data->id; ?>">Completed</span>
                                    <?php }?>
                                </td>

                                <td>
                                    <button title="Assign Mentee" type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#assign_<?php echo $data->id; ?>"><i class="fa fa-tasks"></i></button>

                                    <!-- The Modal -->
                                    <div class="modal" id="assign_<?php echo $data->id; ?>">
                                        <div class="modal-dialog modal-dialog-centered">

                                            <div class="modal-content">

                                                <!-- Modal Header -->
                                                <div class="modal-header" style="color:black;">
                                                    <h4 class="modal-title">Assign Mentee</h4>
                                                    <button type="button" class="close" data-dismiss="modal"><img src="<?php echo url('/assets/images/close.png');?>"></button>
                                                </div>

                                                <!-- Modal body -->
                                                <div class="modal-body">
                                                    <form id="form_id_<?php echo $data->id; ?>">
                                                        <input type="hidden" id="goaltask_id" name="goaltask_id" value="<?php echo $data->id; ?>" />
                                                        <label>Select Mentee</label>
                                                        <select class="selectboxmulti" name="victims[]" multiple="multiple" style="width: 75%">
                                                        <?php

                                                            
                                                            $victims_list = DB::table('mentee')->select('mentee.*')->where('mentee.status',1)->where('mentee.assigned_by',$data->created_by)->orderBy('mentee.id', 'desc')->get()->toarray();               

                                                            if(!empty($victims_list)){
                                                                foreach($victims_list as $p){
                                                                    $vid[] = $p->id;
                                                                }
                                                            }
                                                            
                                                        ?>
                                                        <?php if(!empty($victims_list)){ ?> 

                                                            <?php $count = 0; foreach($victims_list as $vic){ ?> 
                                                            <option value="<?php echo $vic->id; ?>" <?php if (!in_array($vic->id, $vid)) { ?> style="display:none;" <?php } ?>  <?php if(!empty($assign_data_arr) && in_array($vic->id,$assign_data_arr)){ 
                                                                    $assign_users[$count]['name'] = $vic->firstname.' '.$vic->middlename.' '.$vic->lastname;
                                                                    $assign_users[$count]['id'] = $vic->id;
                                                                $assign_users[$count]['status'] = $assign_all_data_arr[$count]['status'];
                                                                $assign_users[$count]['begin_time'] = $assign_all_data_arr[$count]['begin_time'];
                                                                $assign_users[$count]['complated_time'] = $assign_all_data_arr[$count]['complated_time'];
                                                                $assign_users[$count]['note'] = $assign_all_data_arr[$count]['note'];$count++;  ?>selected="" <?php } ?>><?php echo $vic->firstname.' '.$vic->middlename.' '.$vic->lastname ?></option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                        </select>
                                                    </form>
                                                    <div class="listing-table">
                                                        <div class="table-responsive text-left">
                                                            <table class="table table-hover" id="assign_goal_table_<?php echo $data->id; ?>">
                                                                
                                                                <tbody>
                                                                   <?php if(!empty($assign_users)){ 
                                                                    ?>
                                                                    <?php $count = 1; foreach($assign_users as $ass_u){ ?>
                                                                    <tr <?php if (!in_array($ass_u[ 'id'], $vid)) { ?> style="display:none;"
                                                                        <?php } ?>>
                                                                        <th scope="row">
                                                                            <?php echo $count; ?>
                                                                        </th>
                                                                        <td>
                                                                            <?php echo $ass_u['name']; ?>
                                                                        </td>
                                                                    </tr>
                                                                    <?php $count++; } ?>
                                                                    <?php } ?>
                                                                </tbody>
                                                                
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Modal footer -->
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-success" onclick="assign_mentee('<?php echo $data->id; ?>');">Save</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>


                                    <button title="Report" type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#view_assign_<?php echo $data->id; ?>"><i class="fa fa-bar-chart"></i>
                                    </button>

                                    <!-- The Modal -->
                                    <div class="modal" id="view_assign_<?php echo $data->id; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">

                                                <!-- Modal Header -->
                                                <div class="modal-header" style="color:black;">
                                                    <h4 class="modal-title">View Mentee Report</h4>
                                                    <button type="button" class="close" data-dismiss="modal"><img src="<?php echo url('/assets/images/close.png');?>"></button>
                                                </div>

                                                <!-- Modal body -->
                                                <div class="modal-body" style="color:black;">
                                                    <div class="listing-table">
                                                        <div class="table-responsive text-center">
                                                            <table class="table table-hover">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">#</th>
                                                                        <th scope="col">Mentee Name</th>
                                                                        <th scope="col">Status</th>
                                                                        <th scope="col">Begin Time</th>
                                                                        <th scope="col">End Time</th>
                                                                        <th scope="col">Note</th>
                                                                        <th scope="col">Uploaded Files</th>
                                                                    </tr>
                                                                </thead>
                                                                <?php if(!empty($assign_users)){ 
                                                                    // echo '<pre>'; print_r($assign_users);
                                                                ?>

                                                                <?php $count = 1; foreach($assign_users as $ass_u){ ?>
                                                                <tbody>
                                                                    <tr>
                                                                        <td scope="row">
                                                                            <?php echo $count; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $ass_u['name']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php if($ass_u['status'] == 0){ echo "Not Started";}else if($ass_u['status'] == 1){ echo "Begin"; }else{ echo "Completed"; } ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php 
                                                                    if($ass_u['begin_time'] != '0000-00-00 00:00:00' || $ass_u['begin_time'] == ''){
                                                                        $begin_time = DateTime::createFromFormat("Y-m-d H:i:s" , $ass_u['begin_time']);
                                                                        echo $begin_time->format('m-d-Y H:i:s');

                                                                    }
                                                                    
                                                                    
                                                                ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php 
                                                                    if($ass_u['complated_time'] != '0000-00-00 00:00:00' || $ass_u['complated_time'] == ''){
                                                                        $complated_time = DateTime::createFromFormat("Y-m-d H:i:s" , $ass_u['complated_time']);
                                                                        echo $complated_time->format('m-d-Y H:i:s');
                                                                   
                                                                        // echo $ass_u['complated_time']; 
                                                                    }
                                                                    ?>
                                                                        </td>
                                                                        <td>
                                                                            <a href="javascript:void(0);" class="btn btn-primary" onclick="shownotes('<?php echo $data->id;?>','<?php echo $ass_u['id'] ?>');"><i class="fa fa-eye"></i></a>

                                                                        </td>
                                                                        <td>
                                                                            <a href="javascript:void(0);" class="btn btn-primary" onclick="showuploadedfiles('<?php echo $data->id;?>','<?php echo $ass_u['id'] ?>');"><i class="fa fa-eye"></i></a>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                                <?php $count++; } ?>
                                                                <?php } ?>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Modal footer -->
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <?php $gid = \Crypt::encrypt($data->id);?>
                                    <a title="Edit" class="btn btn-success btn-sm" href="<?php echo url('admin/goaltask/add/'.$gid); ?>"><i class="fa fa-pencil"></i></a>
                                    <?php if(!empty($data->status)){  ?>
                                    <a title="Make Inactive" href="<?php echo url('admin/goaltask/changestatus/'.$data->id.'/'.Request::segment(2)); ?>" id="a_<?php echo $data->id; ?>" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                                    <?php }else{ ?>
                                    <a title="Make Active" href="<?php echo url('admin/goaltask/changestatus/'.$data->id.'/'.Request::segment(2)); ?>" id="a_<?php echo $data->id; ?>" class="btn btn-success btn-sm"><i class="fa fa-check"></i></a>
                                    <?php } ?>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $goaltask_arr->links() }}
                </div>
            </div>
        </div>

        <div class="modal" id="modal_shownotes">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">View Notes</h4>
                        <a id="close_notes_cross" class="close" data-goaltask="" onclick="noteclose(this.getAttribute('data-goaltask'));"><img src="<?php echo url('/assets/images/close.png');?>"></a>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body" id="note_div" style="color:black;">

                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <a id="close_notes" class="btn btn-danger" data-goaltask="" onclick="noteclose(this.getAttribute('data-goaltask'));">Close</a>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal" id="modal_showuploadedfiles">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header" style="color:black;">
                        <h4 class="modal-title">View Uploaded Files</h4>
                        <a id="close_upload_cross" class="close" data-goaltask="" onclick="fileclose(this.getAttribute('data-goaltask'));"><img src="<?php echo url('/assets/images/close.png');?>"></a>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body" id="uploadedfiles_div" style="color:black;">

                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <a id="close_files" class="btn btn-danger" data-goaltask="" onclick="fileclose(this.getAttribute('data-goaltask'));">Close</a>
                    </div>

                </div>
            </div>
        </div>

        <script type="text/javascript">
            function shownotes(i, e) {
                $('#view_assign_' + i).modal('hide');
                $('#modal_shownotes').modal('show');

                $('#close_notes_cross').attr('data-goaltask', i);
                $('#close_notes').attr('data-goaltask', i);

                $.ajax({
                    url: "{{ url('/admin/goaltask/view_notes') }}",
                    type: "POST",
                    data: {
                        goaltask_id: i,
                        victim_id: e,
                    },
                    dataType: "html",
                    success: function(data) {
                        $('#note_div').html(data);
                    }
                });
            }

            function noteclose(i) {
                $('#modal_shownotes').modal('hide');
                $('#view_assign_' + i).modal('show');
            }

            function showuploadedfiles(i, e) {
                $('#view_assign_' + i).modal('hide');
                $('#modal_showuploadedfiles').modal('show');

                $('#close_upload_cross').attr('data-goaltask', i);
                $('#close_files').attr('data-goaltask', i);

                $.ajax({
                    url: "{{ url('/admin/goaltask/view_uploaded_files') }}",
                    type: "POST",
                    data: {
                        goaltask_id: i,
                        victim_id: e,
                    },
                    dataType: "html",
                    success: function(data) {
                        $('#uploadedfiles_div').html(data);
                    }
                });
            }

            function fileclose(i) {
                $('#modal_showuploadedfiles').modal('hide');
                $('#view_assign_' + i).modal('show');
            }



            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            function statuschange(id) {
                $('#box_overlay').show();
                $.ajax({
                    url: '<?php echo url(' / '); ?>/admin/goaltask/changestatusajax',
                    type: "post",
                    dataType: 'json',
                    data: {
                        'id': id,
                        '_token': CSRF_TOKEN
                    },
                    success: function(data) {

                        if (data.success == true) {
                            var span_id = "span_" + id;
                            var a_id = "a_" + id;

                            var span_id_val = $('#' + span_id).html();
                            var a_id_val = $('#' + a_id).html();

                            if (data.message == "Inactive") {
                                $('#' + a_id).html("Make Active");
                                $('#' + a_id).removeClass("btn-danger");
                                $('#' + a_id).addClass("btn-success");
                                $('#' + span_id).html("Inactive");
                                $('#' + span_id).removeClass("bg-green");
                                $('#' + span_id).addClass("bg-red");
                                // $('#status').text('Inactive');
                            } else {
                                $('#' + a_id).html("Make Inactive");
                                $('#' + a_id).removeClass("btn-success");
                                $('#' + a_id).addClass("btn-danger");
                                $('#' + span_id).html("Active");
                                $('#' + span_id).removeClass("bg-red");
                                $('#' + span_id).addClass("bg-green");
                                // $('#status').text('Inactive');
                            }

                        } else {

                        }
                        $('#box_overlay').hide();
                    }
                });
            }

        </script>
    </div>
</div>
@endsection
