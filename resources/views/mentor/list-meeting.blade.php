@extends('layouts.apps')
@section('content')
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h3>@if($type == 'requested') Scheduling @elseif($type == 'upcoming') Confirmed @elseif($type == 'past') Completed @endif Sessions</h3>
                </div>

                <div class="col-lg-6">
                    <div class="search-sec" >

                    </div>

                    <div class="text-right">
                        <?php $a = \Crypt::encrypt(0);?>
                        <a class="btn btn-success btn-sm" href="{{ url('/mentor/meeting/add')}}?id={{$a}}">Add Session</a>
                        <?php if($view == 'list'){?>
                            <a class="btn btn-success btn-sm" href="{{ url('/mentor/meeting/list?type='. $type . '&view=calendar')}}">Calendar View</a>
                        <?php }?>
                        <?php if($view == 'calendar'){?>
                            <a class="btn btn-success btn-sm" href="{{ url('/mentor/meeting/list?type='. $type . '&view=list')}}">List View</a>
                        <?php }?>
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
        <?php if($view == 'calendar'){?>
        <div class="box-inner">
            <div class="panel-body" >
                <div id="calendar"></div>
                <div class="modal fade modal-md m-auto" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="title" style="color: #000000"></h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <h5 class="mt-3"><strong>Session Title :- </strong><span class="title"></span></h5>
                                <h5 class="mt-2"><strong>Scheduled On :- </strong><span class="schedule"></span></h5>
                                <h5 class="mt-2"><strong>Mentee :- </strong><span class="name"></span></h5>
                                <h5 class="mt-2"><strong>Description :- </strong><span class="description"></span></h5>
                                <h5 class="mt-2"><strong>Place :- </strong><span class="place"></span></h5>
                                <h5 class="mt-2"><strong>Method :- </strong><span class="method"></span></h5>
                                <h5 class="mt-2"><strong>Requested Notes :- </strong></h5>
                                <div class="notes_div">
                                    <textarea class="notes" rows="4" cols="82" disabled></textarea>
                                </div>
                            </div>
                            <div class="modal-footer mt-3">
                                <span class="edit_div"><a class="btn btn-danger edit"><span class="edit-text"></span></a></span>
                                <span class="reschedule_div"><a class="btn btn-success reschedule"><span class="reschedule-text"></span></a></span>
                                <span class="deny_div"><a class="btn btn-success deny"><span class="deny-text"></span></a></span>
                                <span class="cancel_div"><a class="btn btn-danger cancel"><span class="cancel-text"></span></a></span>
                                <span class="call_div"><a class="btn btn-success call"><i class="fa fa-video-camera" aria-hidden="true"></i></a></span>
                                <span class="delete_div"><a class="btn btn-danger delete"><span class="delete-text"></span></a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php }?>
        <?php if($view == 'list'){?>
        <div class="box-inner">
            <div class="listing-table">
                <div class="table-responsive text-center">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Created From</th>
                            <th>Mentee</th>
                            <?php if($type == 'requested'){?>
                            <th>Requested Notes</th>
                            <?php }?>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Schedule</th>
                            <th>Place</th>
                            <th>Method</th>
                            <?php if($type == 'requested'){?>
                            <th>Action</th>
                            <?php }?>
                            <?php if($type == 'upcoming'){?>
                            <th>Action</th>
                            <?php }?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(!empty($meetings)){
                        foreach($meetings as $d){
                        $is_datetime_valid = true;
                        if($d->schedule_time < date('Y-m-d H:i:s')){
                            $is_datetime_valid = false;
                        }

                        $status_name = "Pending";
                        if(empty($d->status)){
                            $status_name = "Pending";
                        }else if($d->status == 1){
                            $status_name = "Accepted";
                        }else if($d->status == 3){
                            $status_name = "Cancelled";
                        }

                        ?>
                        <tr>
                            <td><?php echo ucwords(str_replace("_"," ",$d->created_from));?></td>
                            <td><?php echo $d->firstname.' '.$d->lastname ;?></td>
                            <?php if($type == 'requested'){?>
                            <td><?php echo (strlen($d->note)>20)?substr($d->note,0,20).'...':$d->note ;?></td>
                            <?php }?>
                            <td><?php echo (strlen($d->title)>20)?substr($d->title,0,20).'...':$d->title; ?></td>
                            <td><?php echo (strlen($d->description)>50)?substr($d->description,0,50).'...':$d->description; ?></td>
                            <td><?php echo get_standard_datetime($d->schedule_time); ?></td>
                            <td><?php echo (!empty($d->address))?substr($d->address,0,20).'...':$d->school_type; ?></td>
                            <td><?php echo !empty($d->method_value)?$d->method_value:'';?></td>
                            <?php if($type == 'requested'){?>
                            <td>
                                <?php $mid = \Crypt::encrypt($d->id);?>
                                <?php
                                if($d->status != 3){
                                ?>
                                <?php if(empty($d->note)){?>
                                <a class="btn btn-success" href="{{ url('/mentor/meeting/add')}}?id={{$mid}}">Edit</a>
                                <?php }else{?>
                                <a class="btn btn-success" href="{{ url('/mentor/meeting/add')}}?id={{$mid}}">Reschedule</a>
                                <a class="btn btn-success" href="{{ url('/mentor/meeting/cancel')}}?id={{$mid}} }}">Cancel</a>
                                <a class="btn btn-success" href="{{ url('/mentor/meeting/deny')}}?id={{$mid}} }}">Deny</a>
                                <?php }?>
                                <?php
                                }else{
                                ?>
                                <span><?php echo $status_name;?></span>
                                <?php }?>
                            </td>
                            <?php }?>
                            <?php if($type == 'upcoming'){?>
                            <?php $mentee_id = \Crypt::encrypt($d->mentee_id);?>
                            <td>
                                <a href="{{url('/mentor/videochat/initiate')}}?mentee_id={{$mentee_id}}" class="btn btn-success"><i class="fa fa-video-camera" aria-hidden="true"></i></a>
                                <?php $mid = \Crypt::encrypt($d->id);?>
                                <a class="btn btn-success" href="{{ url('/mentor/meeting/delete')}}?id={{$mid}}">Delete</a>
                            </td>
                            <?php }?>

                        </tr>
                        <?php }}else{?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No session found</td>
                        </tr>
                        <?php }?>
                        </tbody>
                    </table>
                    {{ $meetings->links() }}
                </div>
            </div>
        </div>
        <?php }?>
</div>
</div>
<script>
    $(document).ready(function() {
    var url_string = window.location.href;
    var url = new URL(url_string);
    var view = url.searchParams.get("view");
    var type = url.searchParams.get("type");
    if(view === 'calendar') {
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: '',
                right: 'title'
            },
            height: 680,
            events: [
                    @foreach($meetings as $meeting)
                {
                    id: '{{ $meeting->id }}',
                    title: '{{ $meeting->title }}',
                    start: '{{ $meeting->schedule_time }}',
                    firstName: '{{ $meeting->firstname }}',
                    lastName: '{{ $meeting->lastname }}',
                    description: '{{ $meeting->description }}',
                    address: '{{ $meeting->address }}',
                    school_type: '{{ $meeting->school_type }}',
                    method_value: '{{ $meeting->method_value }}',
                    created_from: '{{ $meeting->created_from }}',
                    status: '{{ $meeting->status}}',
                    encryptedID: '{{  \Crypt::encrypt($meeting->id) }}',
                    mentee_id: '{{  \Crypt::encrypt($meeting->mentee_id) }}',
                    note: '{{ isset($meeting->note) ? $meeting->note : ''}}'
                },
                @endforeach
            ],
            eventClick: function (info) {
                // console.log(info);
                $("#successModal").modal("show");
                $(".title").text(info.title);
                $(".schedule").text(moment(info.start).format('DD-MM-YY hh:mm a'));
                $(".name").text(info.firstName + info.lastName);
                $(".description").text(info.description);
                $(".notes").text(info.note);
                $(".place").text(info.address ? info.address : info.school_type);
                $(".method").text(info.method_value);
                if (type === 'requested') {
                    if(info.status !== 3) {
                        if (!info.note) {
                            $(".reschedule_div").hide();
                            $(".cancel_div").hide();
                            $(".deny_div").hide();
                            $(".call_div").hide();
                            $(".delete_div").hide();
                            $(".notes").hide();
                            $(".edit_div").show();
                            $(".edit-text").text('Edit');
                            $(".edit").attr("href", '/mentor/meeting/add' + '?id=' + info.encryptedID);
                        } else {
                            $(".edit_div").hide();
                            $(".call_div").hide();
                            $(".delete_div").hide();
                            $(".reschedule_div").show();
                            $(".cancel_div").show();
                            $(".deny_div").show();
                            $(".notes").show();
                            $(".reschedule-text").text('Reschedule');
                            $(".reschedule").attr("href", '/mentor/meeting/add' + '?id=' + info.encryptedID);
                            $(".cancel-text").text('Cancel');
                            $(".cancel").attr("href", '/mentor/meeting/cancel' + '?id=' + info.encryptedID);
                            $(".deny-text").text('Deny');
                            $(".deny").attr("href", '/mentor/meeting/deny' + '?id=' + info.encryptedID);
                        }
                    }
                }
                if (type === 'upcoming') {
                    console.log(info.id)
                    console.log(info.encryptedID)
                    if (!info.note) {
                        $(".notes").hide();
                    } else {
                        $(".notes").show();
                    }
                    $(".edit_div").hide();
                    $(".reschedule_div").hide();
                    $(".cancel_div").hide();
                    $(".deny_div").hide();
                    $(".call_div").show();
                    $(".delete_div").show();
                    $('.call').attr("href", '/mentor/videochat/initiate' + '?mentee_id=' + info.mentee_id);
                    $('.delete-text').text('Delete');
                    $('.delete').attr("href", '/mentor/meeting/delete' + '?id=' + info.encryptedID);
                    }
                if (type === 'past') {
                    $(".reschedule_div").hide();
                    $(".cancel_div").hide();
                    $(".deny_div").hide();
                    $(".call_div").hide();
                    $(".delete_div").hide();
                    $(".notes").hide();
                    $(".edit_div").hide();
                }
            }
        });
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css"/>
<style>
    .fc-view, .fc-view>table {
        position: relative;
        z-index: 0 !important;
    }
    .fc-event {
        font-size: .94em;
        border: 1px solid transparent;
        background-color: transparent;
        font-weight: 400;
    }
    a:not([href]):not([tabindex]):focus, a:not([href]):not([tabindex]):hover {
        color: inherit;
        text-decoration: underline;
    }
    .fc-day-grid-event .fc-time {
        font-weight: 400;
        font-size: 0.65rem;
    }
    .fc-day-grid-event .fc-title {
        font-weight: 500;
    }
    .modal-dialog {
        margin-top: 200px;
    }
    .notes{
        border-radius: 10px;
        border-color: darkgrey;
        font-size: 16px;
    }
</style>
@endsection
