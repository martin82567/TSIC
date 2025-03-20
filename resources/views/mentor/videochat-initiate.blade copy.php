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
            <div class="videoView">
                <div id="remote-media">
                    <!-- <video id="remote-self-view-video" autoplay playsinline></video>
                    <canvas id="remote-self-view-canvas" width=""></canvas> -->
                    <video-player-container></video-player-container>
                </div>

                <div id="preview">
                    <div id="local-media">
                        <video id="my-self-view-video" autoplay playsinline></video>
                        <!-- <canvas id="my-self-view-canvas" width=""></canvas> -->
                    </div>
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

            <button class="btn btn-success" id="roomJoinBtn" style="display: none">Start Call</button>
            <button class="btn btn-danger" id="roomLeftBtn" style="display: none">End Call</button>
            <button class="btn btn-secondary" id="roomConnectingBtn" disable style="display: none">Connecting <i class="fa fa-spinner fa-pulse"></i></button>
        </div>

        
        {{-- <div id="zoom-container"></div> --}}

    </div>
</div>

<style>
    .videoView {
        min-height: 200px;
        position: relative;
        margin-bottom: 20px;
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

    #zmmtg-root {
        height: 100vh;
        width: 100vw;
    }

</style>


{{-- <script type="text/javascript" src="{{ env('APP_URL') }}:3000/quickstart/index.js"></script> --}}

{{-- <script type="text/javascript" src="{{ env('APP_URL') }}/video/video-quickstart-js-1.x-new/quickstart/src/index.js"></script> --}}
{{-- <script type="text/javascript" src="{{ env('APP_URL') }}:5173/index.js"></script> --}}

{{-- <script src="https://source.zoom.us/2.16.0/lib/vendor/react.min.js"></script>
<script src="https://source.zoom.us/2.16.0/lib/vendor/react-dom.min.js"></script>
<script src="https://source.zoom.us/2.16.0/lib/vendor/redux.min.js"></script>
<script src="https://source.zoom.us/2.16.0/lib/vendor/redux-thunk.min.js"></script>
<script src="https://source.zoom.us/2.16.0/zoom-meeting-embedded-2.16.0.min.js"></script> --}}

{{-- <script>
    var zoomSDK = ZoomMtgEmbedded.createClient();
    zoomSDK.init({
        debug: true,
        zoomAppRoot: document.getElementById("zoom-container"),
        language: "en-US",
    });

    function joinZoomMeeting() {
        zoomSDK.join({
            sdkKey: "{{ env('ZOOM_API_KEY') }}",
            signature: "{{ $signature }}",
            meetingNumber: "{{ $unique_name }}",
            password: "",
            userName: "Guest User",
            userEmail: "guest@example.com"
        }).then(response => {
            console.log("Joined successfully:", response);
        }).catch(error => {
            console.error("Join error:", error);
        });
    }

    joinZoomMeeting();
</script> --}}

<script src="https://source.zoom.us/videosdk/zoom-video-2.1.10.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded',function(){
        const ZoomVideo = window.WebVideoSDK.default;
        // console.log(ZoomVideo.createClient())

        // Create Zoom Client
        var client = ZoomVideo.createClient();
        var stream

        client.init('en-US', 'Global', { patchJsMedia: true }).then(() => {
            client
                .join('{{ $unique_name }}', '{{ $signature }}', '{{ Auth::user()->firstname .' '. Auth::user()->lastname }}')
                .then(() => {
                    stream = client.getMediaStream()
                    // stream = zoomVideo.getMediaStream()
                    client.getAllUser().forEach((user) => {
                        console.log(user);
                        // if (user.bVideoOn) {
                            stream.attachVideo(user.userId, 16778240).then((userVideo) => {
                                document.querySelector('video-player-container').appendChild(userVideo)
                            })
                        // }
                    })
            })
        });

        console.log("Zoom session started successfully");

    });

</script>


{{-- <script src="{{ asset('js/zoom/index.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        console.log("Video script loaded");
    });
</script> --}}

@endsection
