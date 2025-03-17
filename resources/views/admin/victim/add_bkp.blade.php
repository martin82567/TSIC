@extends('layouts.admin')
@section('content')
<div class="db-inner-content">
    <form role="form" action="{{ route('admin.mentee.save')}}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" id="id" name="id" value="<?php echo !empty($victim->id)?$victim->id:0;?>">
        <input type="hidden" id="user_type" name="user_type" value="<?php echo Auth::user()->type; ?>">
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>
                            <?php echo !empty($victim->firstname)?$victim->firstname:'';?>
                            <?php echo !empty($victim->middlename)?$victim->middlename:'';?>
                            <?php echo !empty($victim->lastname)?$victim->lastname:'';?></h3>
                    </div>
                    <div class="col-lg-6">
                        <a href="{{ url('/admin/mentee') }}" class="back-btn"><i class="fa fa-arrow-left"></i></a>
                    </div>
                </div>
            </div>
            <div class="box-inner">
                <div class="form-section">
                    <div class="row">
                        <div class="col-sm-8">
                            <h3>
                                <?php if(!empty($victim->id)){?>Edit <?php }else{?>Create<?php }?> Mentee
                            </h3>
                        </div>                        
                    </div>
                    <div class="accordion" id="accordionExample">
                        <div class="card">
                            <div class="card-header" id="headingOne">
                                <a class="card-link collapsed btn-link" data-toggle="collapse" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <h5 class="">
                                        Details
                                    </h5>
                                    <span class="collapsed"><p class="arrow"><i class="fa fa-caret-up"></i></p></span>
                                    <span class="expanded"><p class="arrow"><i class="fa fa-caret-down"></i></p></span>
                                </a>
                            </div>

                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                <div class="card-body">
                                    <div id="first_div"> 
                                        <div class="">
                                            <?php 
                                            if(Auth::user()->type == 1){
                                                $age = 1;
                                                $par = 0;
                                            }else if(Auth::user()->type == 2){
                                                $age = 0;
                                                $par = Auth::user()->id;
                                            }else if(Auth::user()->type == 3){
                                                $parent_id = Auth::user()->parent_id;
                                                $admin_parent = DB::table('admins')->where('id',$parent_id)->first();
                                                if($admin_parent->type == 1){
                                                    $age = 1;
                                                    $par = 0;
                                                }else{
                                                    $age = 0;
                                                    $par = $parent_id;
                                                }
                                            }
                                            ?>                                 
                                            <input type="hidden" id="age" name="age" value="<?php echo $age; ?>">
                                            <div class="row">
                                                <?php if(!empty($age) && empty($par)){ ?>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Affiliate <sup style="color: #ff6c6c;">*</sup></label>
                                                        <input type="text" readonly="readonly" value="{{$affiliate_name}}" name="admin_id">
                                                        <input type="hidden" name="admin_id_hid" value="{{$victim->assigned_by}}">
                                                    </div>
                                                </div>
                                                <?php }else if(empty($age) && !empty($par)){ ?>
                                                <input type="hidden" id="admin_id" name="admin_id_hid" value="<?php echo $par; ?>">
                                                <?php }?>
                                                
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>School <sup style="color: #ff6c6c;">*</sup></label>
                                                        <input type="text" readonly name="school_id" id="school_id" value="{{$school_name}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="check">
                                                        <label>
                                                            <input type="checkbox" name="is_chat_video" value="1" <?php if((isset($victim->is_chat_video) && ($victim->is_chat_video == 1)) ){ ?> checked=""<?php } ?>>Allow Video Chat                                        
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="check">
                                                        <label>
                                                            <input type="checkbox" name="platform_status" value="1" <?php if((isset($victim->platform_status) && ($victim->platform_status == 1)) ){ ?> checked=""<?php } ?>>Platform Status                                        
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>                                            
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Mentors</label>
                                                        <ul>
                                                            <?php if(!empty($mentors)){
                                                                $i = 1;
                                                                foreach($mentors as $m){
                                                                    if(!empty($m->is_primary)){
                                                                        $primary = "Primary";
                                                                    }else{
                                                                        $primary = "Co-mentor";
                                                                    }

                                                                    $video_chat_duration = $settings->video_chat_duration;
                                                                    $video_chat_duration = (60*$video_chat_duration);

                                                                    $used_chat_duration = !empty($m->remaining_time)?convert_sec_to_min($video_chat_duration - $m->remaining_time):'';
                                                                    ?>
                                                            <li style="font-size: 15px; color: #000; display: block;">
                                                                <?php echo $i.' . '. $m->firstname.' '.$m->middlename.' '.$m->lastname;?> <strong><?php echo ' ( '.$primary.' ) ';?></strong>
                                                                
                                                                <div class="chat_duration">
                                                                    <p class="chat_title">Weekly Video Chat</p>
                                                                    

                                                                    <table class="table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th scope="col">#</th>
                                                                                <th scope="col">Week</th>
                                                                                <th scope="col">Remaining</th>
                                                                                <th scope="col">Used</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php 
                                                                            $video_chat_week = $m->video_chat_week;

                                                                            $i = 1;
                                                                            if(!empty($video_chat_week)){
                                                                                foreach($video_chat_week as $w){

                                                                                    $week = date('m/d/Y', strtotime($w->start_date)).' - '.date('m/d/Y', strtotime($w->end_date));
                                                                                    $remaining = convert_sec_to_min($w->remaining);
                                                                                    $used = convert_sec_to_min($w->used);


                                                                            ?>
                                                                            <tr>
                                                                                <td><?php echo $i; ?></td>
                                                                                <td><?php echo $week;?></td>
                                                                                <td><?php echo $remaining;?></td>
                                                                                <td><?php echo $used;?></td>
                                                                            </tr>
                                                                            <?php  $i++;    
                                                                                }
                                                                            }?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                                                                                    
                                                            </li>

                                                            
                                                            <?php $i++;
                                                             }}else{?>
                                                            <li style="font-size: 15px; color: #000; display: block;"> - No Mentors - </li>
                                                            <?php }?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--/*buttons*/ -->
                                            <?php if(!empty($victim->id)){?>
                                            <div class="mentee-btn">                       
                                                <div class="row">
                                                    <div class="col-xl-7">
                                                        <div class="row">               
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#assign_task_<?php echo $victim->id; ?>">Assign Assignment</button>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <button type="button" class="btn btn-success" onclick="view_gtc('<?php echo $victim->id; ?>','task')">View Assignment</button>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#assign_goal_<?php echo $victim->id; ?>">Assign Goal</button>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <button type="button" class="btn btn-success" onclick="view_gtc('<?php echo $victim->id; ?>','goal')">View Goal</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="headingTwo">
                                <a class="card-link collapsed btn-link" data-toggle="collapse" href="#collapseTwo" <?php if(!empty($victim->id)){?> aria-expanded="false" <?php }else{?> aria-expanded="true" <?php }?> aria-controls="collapseTwo"> 
                                <h5 class=""> Profile </h5>
                                <span class="collapsed"><p class="arrow"><i class="fa fa-caret-up"></i></p></span>
                                <span class="expanded"><p class="arrow"><i class="fa fa-caret-down"></i></p></span></a>
                            </div>
                            <div id="collapseTwo" class="collapse <?php if(!empty($victim->id)){?> <?php }else{?> show <?php }?>" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                <div class="card-body">
                                    <div id="nextdiv">
                                        <div class="">
                                            <div class="row mb-3">
                                                <div class="col-xl-6 col-md-6">
                                                    <div class="single-inp">
                                                        <label>Email <sup>*</sup></label>
                                                        <div class="inp">
                                                            <input type="email" id="email" name="email" value="<?php echo !empty($victim->email)?$victim->email:'';?>" <?php echo !empty($victim->email)?"readonly":'';?> required >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6 col-md-6">
                                                    <div class="single-inp">
                                                        <label>Password</label>
                                                        <div class="inp">
                                                            <input type="password" id="password" name="password" <?php if(empty($victim->password)){ ?> required 
                                                            <?php } ?> minlength="6">
                                                        </div>
                                                    </div>
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
                        <a href="{{ url('/admin/mentee') }}" class="btn btn-danger">Cancel</a>
                       
                    </div>

                </div>

            </div>
        </div>
    </form>
</div>

<?php if(!empty($victim->id)){?>
<div class="modal modal-table" id="view_note_<?php echo $victim->id; ?>" style="display: none; padding-right: 15px;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="color:black;">
                <h4 class="modal-title">List Notes</h4>
                <button type="button" class="close" data-dismiss="modal"><img src="<?php echo url('/assets/images/close.png');?>"></button>
            </div>
            <div class="modal-body" style="color:black;">
                <div class="row">
                    <div class="col-md-12" id="sh_note">

                    </div>
                </div>

            </div>
            <div class="modal-footer" style="color:black;">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="assign_task_<?php echo $victim->id; ?>" style="display: none; padding-right: 15px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="color:black;">
                <h4 class="modal-title">Assign Task</h4>
                <button type="button" class="close" data-dismiss="modal"><img src="<?php echo url('/assets/images/close.png');?>"></button>
            </div>
            <form method="post" action="{{ url('/admin/mentee/assign_task') }}">
                <div class="modal-body" style="color:black;">
                    <input type="hidden" id="" name="mentee_id" value="<?php echo $victim->id; ?>"> {{ csrf_field() }}

                    <label>Assign Task</label>
                    <div class="select" style="height:100%;border:none;">
                        <select class="selectboxmulti" name="tasks[]" multiple="multiple" required="required" style="width: 100%">
                <?php if(!empty($task)){ ?> 
                <?php $count = 0; foreach($task as $tsk){ ?> 
                    <option value="<?php echo $tsk->id; ?>" <?php if(!empty($assign_tasks)){ foreach($assign_tasks as $victm){ if($victm->goaltask_id == $tsk->id){ ?> selected="selected"
                            <?php } } } ?> ><?php echo $tsk->name; ?></option>
                    <?php } ?>
                <?php } ?>
                    </select>
                    </div>

                </div>
                <div class="modal-footer" style="color:black;">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" id="assign_goal_<?php echo $victim->id; ?>" style="display: none; padding-right: 15px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="color:black;">
                <h4 class="modal-title">Assign Goal</h4>
                <button type="button" class="close" data-dismiss="modal"><img src="<?php echo url('/assets/images/close.png');?>"></button>
            </div>
            <form method="post" action="{{ url('/admin/mentee/assign_goal') }}">
                <div class="modal-body" style="color:black;">
                    <input type="hidden" id="" name="mentee_id" value="<?php echo $victim->id; ?>"> {{ csrf_field() }}

                    <label>Assign Goal</label>
                    <div class="select" style="height:100%;border:none;">
                        <select class="selectboxmulti" name="goals[]" multiple="multiple" required="required" style="width: 100%">
                <?php if(!empty($goal)){ ?> 
                <?php $count = 0; foreach($goal as $gl){ ?> 
                    <option value="<?php echo $gl->id; ?>" <?php if(!empty($assign_goals)){ foreach($assign_goals as $victm){ if($victm->goaltask_id == $gl->id){ ?> selected="selected"
                            <?php } } } ?> ><?php echo $gl->name; ?></option>
                    <?php } ?>
                <?php } ?>
                    </select>
                    </div>

                </div>
                <div class="modal-footer" style="color:black;">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" id="view_gtc_<?php echo $victim->id; ?>" style="display: none; padding-right: 15px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="color:black;">
                <h4 class="modal-title" id="gtc-title"></h4>
                <button type="button" class="close" data-dismiss="modal"><img src="<?php echo url('/assets/images/close.png');?>"></button>
            </div>
            <div class="modal-body" style="color:black;">
                <div class="row">
                    <div class="col-md-12" id="show_gtc">

                    </div>
                </div>

            </div>
            <div class="modal-footer" style="color:black;">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<script>
    $(document).ready(function() {        

    }); 

    function view_gtc(e, f) {

        if (f == 'task') {
            var title = 'View Tasks';
        } else if (f == 'goal') {
            var title = 'View Goal';
        } else if (f == 'challenge') {
            var title = 'View Challenge';
        } else {
            var title = 'View Tasks';
        }

        $('#gtc-title').text(title);
        $('#view_gtc_' + e).modal().show();
        $('#show_gtc').html('');

        var CSRF_TOKEN = '<?php echo csrf_token(); ?>';
        $.ajax({
            url: "{{ url('/admin/mentee/view_assign_gtc') }}",
            type: "POST",
            data: {
                id: e,
                type: f,
                _token: CSRF_TOKEN
            },
            dataType: "html",
            success: function(data) {
                $('#show_gtc').html(data);
            }
        });
    }
</script>
@endsection
