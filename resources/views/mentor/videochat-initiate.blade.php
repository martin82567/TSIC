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

</style>

<script type="text/javascript" src="{{ env('APP_URL') }}:3000/quickstart/index.js"></script>


@endsection
