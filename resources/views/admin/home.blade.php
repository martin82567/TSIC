@extends('layouts.admin')

@section('content')
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h3>Dashboard</h3>
                </div>
            </div>
        </div>
        <?php
        if(Auth::user()->type == 3){
            $user_access = DB::table('user_access')->where('user_id',Auth::user()->id)->first();
            // $access_victim = $user_access->access_victim;
            $access_goal_task_challenge = $user_access->access_goal_task_challenge;
            $access_meeting = $user_access->access_meeting;

        }else{
            // $access_victim = 1;
            $access_goal_task_challenge = 1;
            $access_meeting = 1;
        }

        ?>
        <div class="box-inner">
                <div class="container-fluid">

                    <div class="row">
                        <?php if(!empty($access_meeting)){?>
                        <div class="col-md-6 mb-5">
                            <ul class="nav-session">
                                <li class="nav-item">
                                    <a class="nav-link <?php if(empty($session_type) || (!empty($session_type) && $session_type == 'today')){ echo 'active'; }?>" href="{{url('/admin')}}?session_type=today">Today
                                    <?php if(empty($session_type) || (!empty($session_type) && $session_type == 'today')){ echo '<span>('.count($session_log).')</span>'; }?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php if(!empty($session_type) && $session_type == 'lastweek'){ echo 'active'; }?>" href="{{url('/admin')}}?session_type=lastweek">Last Week
                                    <?php if(!empty($session_type) && $session_type == 'lastweek'){ echo '<span>('.count($session_log).')</span>'; }?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php if(!empty($session_type) && $session_type == 'lastmonth'){ echo 'active'; }?>" href="{{url('/admin')}}?session_type=lastmonth">Last Month
                                    <?php if(!empty($session_type) && $session_type == 'lastmonth'){ echo '<span>('.count($session_log).')</span>'; }?>
                                    </a>
                                </li>
                            </ul>

                      		<div class="box-inner-admin default-table">
                                <h4>Logged Sessions</h4>
                                <div class="listing-table dashboard-table">
                                    <div class="table-responsive text-center">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Mentor</th>
                                                    <th>Mentee</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            	<?php if(!empty($session_log)){
                                            		foreach($session_log as $sl){
                                            	?>
                                            	<tr>
                                            		<td><?php echo strlen($sl->name) > 50 ? substr($sl->name,0,50)."..." : $sl->name; ?></td>
                                            		<td><?php echo $sl->mentor_firstname.' '.$sl->mentor_lastname;?></td>
                                            		<td><?php echo $sl->mentee_firstname.' '.$sl->mentee_lastname;?></td>
                                            		<td><?php echo date('m-d-Y', strtotime($sl->schedule_date));?></td>
                                            	</tr>
                                            	<?php }}else{?>

                                                <tr>
                                                    <td colspan="4">No session logged</td>
                                                </tr>
                                                <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php }?>

                        <div class="col-md-6 mb-5">
                            <ul class="nav-session">
                                <li class="nav-item">
                                    <a class="nav-link <?php if(empty($chat_time) || (!empty($chat_time) && $chat_time == 'today')){ echo 'active'; }?>" href="{{url('/admin')}}?chat_time=today&session_type={{$session_type}}">Today
                                    <?php if(empty($chat_time) || (!empty($chat_time) && $chat_time == 'today')){ echo '<span>('.count($chat_data).')</span>'; }?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php if(!empty($chat_time) && $chat_time == 'lastweek'){ echo 'active'; }?>" href="{{url('/admin')}}?chat_time=lastweek&session_type={{$session_type}}">Last Week
                                    <?php if(!empty($chat_time) && $chat_time == 'lastweek'){ echo '<span>('.count($chat_data).')</span>'; }?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php if(!empty($chat_time) && $chat_time == 'lastmonth'){ echo 'active'; }?>" href="{{url('/admin')}}?chat_time=lastmonth&session_type={{$session_type}}">Last Month
                                    <?php if(!empty($chat_time) && $chat_time == 'lastmonth'){ echo '<span>('.count($chat_data).')</span>'; }?>
                                    </a>
                                </li>
                            </ul>

                            <div class="box-inner-admin default-table">
                                <h4>Mentor-Mentee Chat</h4>
                                <div class="listing-table dashboard-table">
                                    <div class="table-responsive text-center">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Message</th>
                                                    <th>Mentor</th>
                                                    <th>Mentee</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(!empty($chat_data)){
                                                    foreach($chat_data as $ch){

                                                        if($ch->from_where == 'mentor'){
                                                            $mentor_id = $ch->sender_id;
                                                            $mentee_id = $ch->receiver_id;
                                                        }else if($ch->from_where == 'mentee'){
                                                            $mentor_id = $ch->receiver_id;
                                                            $mentee_id = $ch->sender_id;
                                                        }

                                                        $mentor_data = DB::table('mentor')->select('id','firstname','lastname')->where('id',$mentor_id)->first();
                                                        $mentee_data = DB::table('mentee')->select('id','firstname','lastname')->where('id',$mentee_id)->first();

                                                        $mentor_name = $mentor_data->firstname. ' '.$mentor_data->lastname;
                                                        $mentee_name = $mentee_data->firstname. ' '.$mentee_data->lastname;


                                                ?>
                                                <tr>
                                                    <td><?php echo strlen($ch->message) > 50 ? substr($ch->message,0,50)."..." : $ch->message; ?></td>
                                                    <td><?php echo $mentor_name;?></td>
                                                    <td><?php echo $mentee_name;?></td>
                                                    <td><?php echo get_standard_datetime_with_timezone($ch->created_date);?></td>
                                                </tr>
                                                <?php }}else{?>

                                                <tr>
                                                    <td colspan="4">No session logged</td>
                                                </tr>
                                                <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-5">

                            <div class="box-inner-admin default-table">
                                <h4>Mentor-Mentee Chat Count</h4>
                                <div class="listing-table dashboard-table">
                                    <div class="table-responsive text-center">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Today</th>
                                                    <th>Last Week</th>
                                                    <th>Last Month</th>
                                                    <th>Last Fiscal Year</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><?php echo $chat_count['today'];?></td>
                                                    <td><?php echo $chat_count['lastweek'];?></td>
                                                    <td><?php echo $chat_count['lastmonth'];?></td>
                                                    <td><?php echo $chat_count['lastyear'];?></td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <?php if(!empty($access_goal_task_challenge)){?>
                    <div class="row">
                        <div class="col-md-6 mb-5">
                            <div class="box-inner-admin">
                            <h4>Goal Leadboard</h4>
                            <div class="listing-table dashboard-table">
                                <div class="table-responsive text-center">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Mentee</th>
                                                <th>Goal</th>
                                                <th>Completed Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($goal_leadboard)){
                                                foreach($goal_leadboard as $gl){
                                            ?>
                                            <tr>

                                                <td><?php echo $gl->victim_firstname.' '.$gl->victim_middlename.' '.$gl->victim_lastname;?></td>
                                                <td><?php echo $gl->goaltask_name;?></td>
                                                <td><?php echo get_standard_datetime($gl->complated_time);?></td>
                                                <?php $glid = \Crypt::encrypt($gl->victim_id);?>
                                                <td><a href="{{url('/admin/mentee/add')}}/<?php echo $glid;?>"><i class="fa fa-eye"></i></a></td>
                                            </tr>
                                            <?php }}else{

                                                    echo '<tr><td colspan="4">No records found</td></tr>';


                                            }?>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-5">
                            <div class="box-inner-admin">
                            <h4>Task Leadboard</h4>
                            <div class="listing-table dashboard-table">
                                <div class="table-responsive text-center">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Mentee</th>
                                                <th>Task</th>
                                                <th>Completed Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($task_leadboard)){
                                                foreach($task_leadboard as $tl){
                                            ?>
                                            <tr>
                                                <td><?php echo $tl->victim_firstname.' '.$tl->victim_middlename.' '.$tl->victim_lastname;?></td>
                                                <td><?php echo $tl->goaltask_name;?></td>
                                                <td><?php echo get_standard_datetime($tl->complated_time);?></td>
                                                <?php $tlid = \Crypt::encrypt($tl->victim_id);?>
                                                <td><a href="{{url('/admin/mentee/add')}}/<?php echo $tlid;?>"><i class="fa fa-eye"></i></a></td>
                                            </tr>
                                            <?php }}else{

                                                    echo '<tr><td colspan="4">No records found</td></tr>';


                                            }?>
                                        </tbody>
                                    </table>
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
<style type="text/css">
	.nav-session {
	    border-bottom: 0;
	    background: #F8A830;
	    border-top-left-radius: 15px;
	    border-top-right-radius: 15px;
	    overflow: hidden;
	    display: block;
	    border-bottom: 1px solid #F7AF31;
	}
	.nav-session .nav-link:focus,
	 .nav-session .nav-link:hover {
	    border-color: transparent;
	}
	.nav-session .nav-item {
	    margin-bottom: -1px;
	    display: inline-block;
	    width: 33.33%;
	    float: left;
	}
	.nav-session .nav-link{
	    color: #000;
	    text-align: center;
	}
	.nav-session .nav-item.show .nav-link,
	 .nav-session .nav-link.active {
	    color: #fff;
	    background-color: #F4980D;
	    border-color: transparent;
	}
	.box-inner-admin {
	    border-top-left-radius: 0;
	    border-top-right-radius: 0;
	}
</style>
@endsection
