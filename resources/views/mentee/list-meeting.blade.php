@extends('layouts.apps')
@section('content')
<!-- <p>Mentee</p> -->
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
                        <?php if($view == 'list'){?>
                        <a class="btn btn-success btn-sm mt-2" href="{{ url('/mentee/meeting/list?type='. $type . '&view=calendar')}}">Calendar View</a>
                        <?php }?>
                        <?php if($view == 'calendar'){?>
                        <a class="btn btn-success btn-sm mt-2" href="{{ url('/mentee/meeting/list?type='. $type . '&view=list')}}">List View</a>
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
                    <div class="modal-dialog modal-md meeting-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="title" style="color: #000000"></h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <h5 class="mt-3"><strong>Session Title :- </strong><span class="title"></span></h5>
                                <h5 class="mt-2"><strong>Created From :- </strong><span class="created_from"></span></h5>
                                <h5 class="mt-2"><strong>Scheduled On :- </strong><span class="schedule"></span></h5>
                                <h5 class="mt-2"><strong>Requested By :- </strong><span class="name"></span></h5>
                                <h5 class="mt-2"><strong>Description :- </strong><span class="description"></span></h5>
                                <h5 class="mt-2"><strong>Place :- </strong><span class="place"></span></h5>
                                <h5 class="mt-2"><strong>Method :- </strong><span class="method"></span></h5>
                                <h5 class="mt-2"><strong>Requested Notes :- </strong><span class="notes"></span></h5>
                            </div>
                            <div class="modal-footer mt-3">
                                <span class="accept_div"><a class="btn btn-danger accept"><span class="accept-text"></span></a></span>
                                <span class="reschedule_div"><a class="btn btn-success reschedule"><span class="reschedule-text"></span></a></span>
                                <span class="call_div"><a class="btn btn-success call"><i class="fa fa-video-camera" aria-hidden="true"></i></a></span>
                                <span class="reschedule_note_div"><span class="reschedule_note_text" style="font-size: 16px"></span></span>
                                <span class="cancel_div"><a class="btn btn-danger cancel"><span class="cancel-text"></span></a></span>
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
                                <th>Requested By</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Schedule</th>
                                <th>Place</th>
                                <th>Method</th>
                                <?php if($type == 'requested'){?>
                                <th>Action</th>
                                <?php }?>
                                <?php if($type == 'upcoming'){?>
                                <th>Video Call</th>
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


                            ?>
                            <tr>
                                <td><?php echo ucwords(str_replace("_"," ",$d->created_from)); ?></td>
                                <td><?php echo $d->creator_name ;?></td>
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
                                            if($d->created_from == "affiliate_portal"){
                                                ?>
                                                <a class="btn btn-success" href="{{ url('/mentee/meeting/accept-web')}}?id={{$mid}}&web_status=2">Accept</a>

                                                <?php

                                            }else{
                                                if(empty($d->is_requested)){
                                                    ?>
                                                    <a class="btn btn-success" href="{{ url('/mentee/meeting/accept-app')}}?id={{$mid}}">Accept</a>
                                                    <a class="btn btn-success" href="javascript:void(0);" onclick="requestform('<?php echo $d->id; ?>');">Reschedule</a>
                                                    <?php

                                                }else{
                                                    echo '<span>Reschedule Request Sent</span>';
                                                }

                                            }
                                        }else{
                                    ?>

                                    <span>Cancelled</span>
                                    <?php }?>
                                </td>
                                <?php }?>
                                <?php if($type == 'upcoming'){?>
                                <?php $mentor_id = \Crypt::encrypt($d->mentor_id);?>
                                <td>
                                    <a href="{{url('/mentee/videochat/initiate')}}?mentor_id={{$mentor_id}}" class="btn btn-success"><i class="fa fa-video-camera" aria-hidden="true"></i></a>
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

<div class="modal" id="modal_request">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header" style="color:black;">
                <h4 class="modal-title">Request for Reschedule</h4>
                <button type="button" class="close" data-dismiss="modal"><img src="<?php echo url('/assets/images/close.png');?>"></button>
            </div>
            <!-- Modal body -->
            <form id="" method="post" action="{{url('/mentee/meeting/request-reschedule')}}">
                {{ csrf_field() }}
            <div class="modal-body">
                <input type="hidden" id="meeting_id" name="meeting_id" value="" />
                <label>Add Note</label>
                <textarea name="note" id="note" class="form-control" placeholder="Please write something" rows="4" cols="50" maxlength="1000" required="required"></textarea>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" onclick="">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
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
                        creatorName: '{{ $meeting->creator_name }}',
                        description: '{{ $meeting->description }}',
                        address: '{{ $meeting->address }}',
                        school_type: '{{ $meeting->school_type }}',
                        method_value: '{{ $meeting->method_value }}',
                        created_from: '{{ $meeting->created_from }}',
                        status: '{{ $meeting->status}}',
                        encryptedID: '{{  \Crypt::encrypt($meeting->id) }}',
                        note: '{{ isset($meeting->note) ? $meeting->note : ''}}',
                        is_requested: '{{ isset($meeting->is_requested) ? $meeting->is_requested : ''}}',
                        mentor_id: '{{ \Crypt::encrypt($meeting->mentor_id) }}'
                    },
                    @endforeach
                ],
                eventClick: function (info) {
                    // console.log(info);

                    $("#successModal").modal("show");
                    $(".title").text(info.title);
                    $(".schedule").text(moment(info.start).format('DD-MM-YY hh:mm a'));
                    $(".name").text(info.creatorName);
                    $(".description").text(info.description);
                    $(".notes").text(info.note);
                    $(".place").text(info.address ? info.address : info.school_type);
                    $(".method").text(info.method_value);
                    $(".created_from").text(info.created_from);
                    if (type === 'requested') {
                        if(info.status !== 3) {
                            if (info.created_from === "affiliate_portal") {
                                $(".reschedule_div").hide();
                                $(".reschedule_note_div").hide();
                                $(".accept_div").show();
                                $(".accept-text").text('Accept');
                                $(".accept").attr("href", '/mentee/meeting/accept-web' + '?id=' + info.encryptedID);
                            } else {
                                if (info.is_requested === '1') {
                                    $(".accept_div").hide();
                                    $(".reschedule_div").hide();
                                    $(".cancel_div").hide();
                                    $(".call_div").hide();
                                    $(".reshedule_note_div").show();
                                    $(".reschedule_note_text").text('Reschedule Request Sent');
                                } else {
                                    $(".reschedule_note_div").hide();
                                    $(".cancel_div").hide();
                                    $(".call_div").hide();
                                    $(".accept_div").show();
                                    $(".reschedule_div").show();
                                    $(".accept-text").text('Accept');
                                    $(".accept").attr("href", '/mentee/meeting/accept-app' + '?id=' + info.encryptedID);
                                    $(".reschedule-text").text('Reschedule');
                                    $(".reschedule").click(function () {
                                        $('#modal_request').modal().show();
                                        $('#meeting_id').val(info.id);
                                    });
                                }
                            }
                        } else {
                            $(".reschedule_div").hide();
                            $(".reschedule_note").hide();
                            $(".accept_div").hide();
                            $(".reshedule_note_div").hide();
                            $(".cancel_div").show();
                            $(".cancel_text").text('Cancelled');
                        }
                    }
                    if (type === 'upcoming') {
                        $(".accept_div").hide();
                        $(".reschedule_div").hide();
                        $(".cancel_div").hide();
                        $(".reschedule_note_div").hide();
                        $(".call_div").show();
                        $('.call').attr("href", '/mentee/videochat/initiate' + '?mentor_id=' + info.mentor_id);
                    }
                    if (type === 'past') {
                        $(".accept_div").hide();
                        $(".reschedule_div").hide();
                        $(".cancel_div").hide();
                        $(".call_div").hide();
                        $(".reshedule_note_div").hide();
                    }
                }
            });
        };
    });
    function requestform(i)
    {
        console.log(i);
        $('#modal_request').modal().show();
        $('#meeting_id').val(i);
    }
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
     .meeting-dialog {
        margin-top: 200px;
    }
    .notes{
        border-radius: 10px;
        border-color: darkgrey;
        font-size: 16px;
    }
</style>

@endsection
