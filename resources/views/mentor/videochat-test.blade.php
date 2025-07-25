@extends('layouts.apps')
@section('content')
<!-- <p>Mentor</p> -->

<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-lg-8">

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

        <style>
            .videoView {
                min-height: 200px;
                position: relative;
                margin-bottom: 20px;
            }
            .videoView #remote-media video {
                width: 100%;
                height: 600px;
            }
            .videoView #local-media video {
                width: 200px;
                height: 150px;
                position: absolute;
                right: 10px;
                bottom: 10px;
            }
        </style>

            <div class="videoView">
                <div id="remote-media"></div>

                <div id="preview">
                    <div id="local-media"></div>
                </div>
            </div>

            <div>
                <input id="selfType" type="hidden" value="mentor">
                <input id="selfId" type="hidden" value="{{ Auth::user()->id }}">
                <input id="otherType" type="hidden" value="mentee">
                <input id="otherId" type="hidden" value="11">
            </div>
            
            <button class="btn btn-success" id="roomJoinBtn">Join Room</button>
            <button class="btn btn-danger" id="roomLeftBtn" style="display: none">Leave Room</button>

        </div>

    </div>
</div>

<style></style>

<script type="text/javascript" src="https://mentorappdev.tsic.org:3000/quickstart/index.js"></script>
<script type="text/javascript">
    var roomCreateData = {};

    // $(function(){
    //     $.post("https://mentorappdev.tsic.org/api\", {
    //         sender_id:11,
    //         sender_type:"mentee",
    //         receiver_id:15,
    //         receiver_type:"mentor"
    //     }, function(data, status) {
    //         // console.log(data.message);
    //         console.log("Page Data");
    //         console.log(data);
    //     });
    // });

    function roomJoinEvent() {
        $.post("https://mentorappdev.tsic.org/api/webvideochat/initiate_chat", {
            userId : 0
        }, function(data, status){
            // console.log(data);
            console.log(data.message);
            if(data.status == true) {
                roomCreateData = data.data;
                joinRoomFunction(roomCreateData.unique_name);

                // roomCreateData.receiver_accesstoken;
                // roomCreateData.room_sid
                // roomCreateData.sender_accesstoken;
                // roomCreateData.unique_name;
            };
        });
    };

    function roomLeftEvent() {
        // alert("roomLeftEvent");

        if(roomCreateData.room_sid) {
            $.post("https://mentorappdev.tsic.org/api/webvideochat/disconnect_room", {
                room_sid: roomCreateData.room_sid
            }, function(data, status){
                roomCreateData = {};
                console.log(data);
                alert(data.message);
            });
            leftRoomFunction();
        };
    };
</script>

@endsection
