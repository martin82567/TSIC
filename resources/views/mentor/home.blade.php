@extends('layouts.apps')
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
        <div class="box-inner">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 mb-5">
                        <h5><span>Total Session Logged: <strong>{{$sessionLogCount}}</strong></span></h5>
                    </div>
                    <div class="col-md-12 mb-5">
                        <ul class="nav-session">
                            <li class="nav-item">
                                <a class="nav-link <?php if(empty($chat_time) || (!empty($chat_time) && $chat_time == 'today')){ echo 'active'; }?>" href="{{url('/mentor')}}?chat_time=today">Today </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php if(!empty($chat_time) && $chat_time == 'lastweek'){ echo 'active'; }?>" href="{{url('/mentor')}}?chat_time=lastweek">Last Week </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php if(!empty($chat_time) && $chat_time == 'lastmonth'){ echo 'active'; }?>" href="{{url('/mentor')}}?chat_time=lastmonth">Last Month  </a>
                            </li>
                        </ul>
                        <div class="box-inner-admin default-table">
                            <h4>Chats</h4>
                            <div class="listing-table dashboard-table">
                                <div class="table-responsive text-center">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Mentee</th>
                                                <th>Message</th>
                                                <th>Sent By</th>
                                                <th>Date & Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                if(!empty($chats)){
                                                    foreach($chats as $c){

                                                        if($c->from_where == 'mentor'){
                                                            $mentee_id = $c->receiver_id;
                                                        }else{
                                                            $mentee_id = $c->sender_id;
                                                        }

                                                        $mentee_data = DB::table('mentee')->select('firstname','lastname')->where('id',$mentee_id)->first();

                                                        $mentee_firstname = !empty($mentee_data->firstname)?$mentee_data->firstname:'';
                                                        $mentee_lastname = !empty($mentee_data->lastname)?$mentee_data->lastname:'';
                                                        $mentee_name = $mentee_firstname.' '.$mentee_lastname;


                                            ?>
                                            <tr>
                                                <td><?php echo $mentee_name; ?></td>
                                                <td><?php echo $c->message;?></td>
                                                <td><?php echo ucwords($c->from_where);?></td>
                                                <td><?php echo get_standard_datetime($c->created_date);?></td>
                                            </tr>
                                            <?php
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6 mb-5">
                        <div class="box-inner-admin">
                            <h4>Upcoming Sessions</h4>
                            <div class="listing-table dashboard-table">
                                <div class="table-responsive text-center">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Mentee</th>
                                                <th>Date & Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($upcoming_meeting)){
                                                    foreach($upcoming_meeting as $m){

                                                    $mentee_firstname = !empty($m->firstname)?$m->firstname:'';
                                                    $mentee_lastname = !empty($m->lastname)?$m->lastname:'';
                                            ?>
                                            <tr>
                                                <td><?php echo !empty($m->title)?$m->title:''; ?></td>
                                                <td><?php echo $mentee_firstname.' '.$mentee_lastname; ?></td>
                                                <td><?php echo get_standard_datetime($m->schedule_time);?></td>
                                            </tr>
                                            <?php } }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-5">
                        <div class="box-inner-admin">
                            <h4>Recently Logged Sessions</h4>
                            <div class="listing-table dashboard-table">
                                <div class="table-responsive text-center">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Note</th>
                                                <th>Mentee</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($recently_logged_sessions)){
                                                    foreach($recently_logged_sessions as $s){

                                                    $mentee_firstname = !empty($s->firstname)?$s->firstname:'';
                                                    $mentee_lastname = !empty($s->lastname)?$s->lastname:'';
                                            ?>
                                            <tr>
                                                <td><?php echo !empty($s->name)?$s->name:''; ?></td>
                                                <td><?php echo $mentee_firstname.' '.$mentee_lastname; ?></td>
                                                <td><?php echo date('m-d-Y', strtotime($s->schedule_date));?></td>
                                            </tr>
                                            <?php } }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if(!empty($upcoming_meeting)) {
    $m = $upcoming_meeting[0];
    $mentee_firstname = !empty($m->firstname)?$m->firstname:'';
    $mentee_lastname = !empty($m->lastname)?$m->lastname:'';
?>
    <div class="modal fade" id="upcomingSessionPop" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Reminder! Upcoming session</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body my-4 text">
                    <h6>Reminder! You have an upcoming session at <?php echo get_standard_datetime($m->schedule_time); ?> with <?php echo $mentee_firstname.' '.$mentee_lastname; ?> </h6>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-success" data-dismiss="modal">Ok</a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if(!empty($past_meeting)){ ?>
    <div class="modal fade" id="logSessionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Reminder to log session</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body my-4 text">
                    <h6>You had a scheduled session at <?php echo get_standard_datetime($past_meeting[0]->schedule_time); ?>. Don't forget to log the session. </h6>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-info" href="{{ url('/mentor/sessionlog/add') }}">Log Now</a>
                    <a class="btn btn-outline-success" data-dismiss="modal">Already done</a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<style type="text/css">

</style>

<script type="text/javascript">
    window.addEventListener('load', function() {
        <?php if(!empty($upcoming_meeting)){ ?>
            $("#upcomingSessionPop").modal({backdrop: "static"});
        <?php } ?>
        <?php if(!empty($past_meeting)){ ?>
            $("#logSessionModal").modal({backdrop: "static"});
        <?php } ?>
    })
</script>
@endsection
