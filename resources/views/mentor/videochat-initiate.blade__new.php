@extends('layouts.apps')
@section('content')
<!-- <p>Mentor</p> -->

<style>
    .videoView {
        min-height: 200px;
        position: relative;
        margin-bottom: 20px;
        margin-top: 20px;
    }
    .videoView #remote-media video {
        width: 100%;
        height: 600px;
        background: #000;
    }
    .videoView #local-media{
        width: 200px;
        height: 150px;
        position: absolute;
        right: 10px;
        bottom: 10px;
    }
    .videoView #local-media video {
        width: 200px;
        height: 150px;
        position: absolute;
        right: 10px;
        bottom: 10px;
    }
    .videoView .countdown {
        font-size: 32px;
        color: #fff;
        font-weight: 700;
        position: absolute;
        right: 20px;
        top: 20px
    }
    .videoView .countdown.blinking {
        animation-name: blinkAnimation;
        animation-duration: 2s;
        animation-iteration-count:infinite;
    }
    @keyframes blinkAnimation {
        0%   {opacity: 1}
        33%  {opacity: 0}
        66%  {opacity: 1}
        100% {opacity: 1}
    }

    video-player-container {
        width: 100%;
        height: auto;
    }

    video-player {
        width: 100%;
        height: 600px;
        aspect-ratio: 16/9;
    }
</style>

<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-lg-8">

                </div>
            </div>
        </div>

        @if(!empty(session('success_message')))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                <h4 style="margin-bottom: 0"><i class="icon fa fa-check"></i>
                    @php 
                        echo session('success_message'); 
                        Session::forget('success_message');
                    @endphp
                </h4>
            </div>
        @endif
        
        <div class="box-inner">
            <button class="btn btn-success" id="roomJoinBtn">Join Room</button>
            <button class="btn btn-danger" id="roomLeftBtn" style="display: none">End Call</button>
            <button class="btn btn-secondary" id="roomConnectingBtn" disable style="display: none">Connecting <i class="fa fa-spinner fa-pulse"></i></button>
            
            <div class="videoView">
                <div id="remote-media">
                    <video-player-container></video-player-container>
                </div>

                <div id="preview">
                    <div id="local-media"></div>
                </div>
                <h3 id="countDownTime" class="countdown" style="display:none;">00:00</h3>
            </div>

            <div>                
                <input id="selfType" type="hidden" value="mentor">
                <input id="selfId" type="hidden" value="{{ Auth::user()->id }}">
                <input id="selfName" type="hidden" value="{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}">
                <input id="otherType" type="hidden" value="mentee">
                <input id="otherId" type="hidden" value="{{$mentee_id}}">
                <input id="otherDeviceType" type="hidden" value="{{$mentee_device_type}}">
                <input id="otherFirebaseId" type="hidden" value="{{$mentee_firebase_id}}">
                <input id="otherVoipToken" type="hidden" value="{{$mentee_voip_device_token}}">
            </div>
        </div>
    </div>
</div>


<script src="https://source.zoom.us/videosdk/zoom-video-2.1.10.min.js"></script>

<script type="text/javascript">
    // var mainUrl = "{{ env('APP_URL') }}";
    var mainUrl = "https://test.tsicmentorapp.org";
    
    // Mentor details
    var identity = document.getElementById("selfName").value;
    var selfType = document.getElementById("selfType").value;
    var selfId = document.getElementById("selfId").value;
    var otherType = document.getElementById("otherType").value;
    var otherId = document.getElementById("otherId").value;
    var otherDeviceType = document.getElementById("otherDeviceType").value;
    var otherFirebaseId = document.getElementById("otherFirebaseId").value;
    var otherVoipToken = document.getElementById("otherVoipToken").value;

    var roomName;
    var inititateData = {};
    inititateData = {
        sender_id: selfId,
        sender_type: selfType,
        sender_device: "web",
        receiver_id: otherId,
        receiver_type: otherType,
        receiver_device_type: otherDeviceType,
        receiver_firebase_id: otherFirebaseId,
        receiver_voip_token: otherVoipToken,
    };

    // Start and join sessions
    const ZoomVideo = window.WebVideoSDK.default;

    var client = ZoomVideo.createClient();
    var stream;


    // Join Button click for join the room
    document.getElementById("roomJoinBtn").onclick = function() {
        userType = "sender";
        document.getElementById("roomJoinBtn").style.display = "none";
        document.getElementById("roomConnectingBtn").style.display = "inline";

        // Initiate zoom video call
        initiateCall(); 
    };


    // Initiate video call from API
    function initiateCall() {
        $.post(
            mainUrl + "/api/webvideochat/initiate_chat",
            inititateData,
            function(roomData, status) {
                console.log(roomData);

                if (roomData.status === false) {
                    alert(roomData.message);
                    document.getElementById("roomConnectingBtn").style.display = "none";
                    document.getElementById("roomJoinBtn").style.display = "inline";   
                    return;
                }

                roomCreateData = roomData.data;
                roomName = roomCreateData.unique_name;

                if (selfType.value === "mentor") {
                    tokenData = roomCreateData.sender_accesstoken;
                } else {
                    tokenData = roomCreateData.receiver_accesstoken;
                }

                inititateData.room_sid = roomCreateData.room_sid;
                inititateData.unique_name = roomCreateData.unique_name;
                inititateData.receiver_accesstoken = roomCreateData.receiver_accesstoken;
                inititateData.remaining_time = roomCreateData.remaining_time;
                inititateData.created_at = roomCreateData.created_at;


                // Check remaining time
                if (roomCreateData.remaining_time < 10) {
                    alert("You dont have enought time to call this user.");
                    document.getElementById("roomConnectingBtn").style.display = "none";
                    document.getElementById("roomJoinBtn").style.display = "inline";
                    return;
                }

                sendRequest = true;
                remainimgCallTime = roomCreateData.remaining_time;


                // Start and join sessions
                client.init('en-US', 'Global', { patchJsMedia: true }).then(() => {
                    client
                        .join(roomName, tokenData, identity)
                        .then(() => {
                            stream = client.getMediaStream();

                            // Start video
                            stream.startVideo().then(() => {
                                stream.attachVideo(client.getCurrentUserInfo().userId, 1920, 1080, 0, 0, 3).then((userVideo) => {
                                    document.querySelector('video-player-container').appendChild(userVideo)
                                });
                            });
                        });
                });

            }
        );
    }
    
</script>

{{-- <script type="text/javascript">
    var roomCreateData = {};
    function roomJoinEvent() {
        $.post("https://mentorappdev.tsic.org/api/webvideochat/initiate_chat", {
            userId : 0
        }, function(data, status){
            // console.log(data);
            console.log(data.message);
            if(data.status == true) {
                roomCreateData = data.data;
                joinRoomFunction(roomCreateData.unique_name);
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
</script> --}}

@endsection
