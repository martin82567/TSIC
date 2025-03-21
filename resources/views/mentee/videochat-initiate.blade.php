@extends('layouts.apps')
@section('content')
<!-- <p>Mentee</p> -->

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
            <button class="btn btn-success" id="roomJoinBtn">Start Call</button>
            <button class="btn btn-danger" id="roomLeftBtn" style="display: none">End Call</button>
            <button class="btn btn-secondary" id="roomConnectingBtn" disable style="display: none">Connecting <i class="fa fa-spinner fa-pulse"></i></button>

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
                <input id="selfType" type="hidden" value="mentee">
                <input id="selfId" type="hidden" value="{{ Auth::user()->id }}">
                <input id="selfName" type="hidden" value="{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}">
                <input id="otherType" type="hidden" value="mentor">
                <input id="otherId" type="hidden" value="{{ $mentor_id }}">
                <input id="otherDeviceType" type="hidden" value="{{ $mentor_device_type }}">
                <input id="otherFirebaseId" type="hidden" value="{{ $mentor_firebase_id }}">
                <input id="otherVoipToken" type="hidden" value="{{ $mentor_voip_device_token }}">
            </div>

            <!-- <button class="btn btn-success" id="roomJoinBtn" style="display: none; width: 0; height: 0; overflow: hidden; opacity: 0;">Start Call</button> -->
            
        </div>

    </div>
</div>

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

    .videoView #local-media {
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
        animation-iteration-count: infinite;
    }

    @keyframes blinkAnimation {
        0% {
            opacity: 1
        }

        33% {
            opacity: 0
        }

        66% {
            opacity: 1
        }

        100% {
            opacity: 1
        }
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

{{-- <script type="text/javascript" src="{{ env('APP_URL') }}:3000/quickstart/index.js"></script> --}}

<script src="https://source.zoom.us/videosdk/zoom-video-2.1.10.min.js"></script>

<script>
    var ZoomVideo = window.WebVideoSDK.default;
    var Video = ZoomVideo.createClient();
    var zoomSession;
    var socketConnect;
    var activeRoom;
    var previewTracks;
    var roomName;
    var roomCreateData = {};
    var sendRequest = false;
    var callReceived = false;
    var roomCheckData = {};
    var inititateData = {};

    var remainimgCallTime = 0;
    var countDownInterval;
    var receiverInterval;
    var timerActive = false;

    var userType = "sender";

    const connectOptions = {};

    // For mobile browsers, limit the maximum incoming video bitrate to 2.5 Mbps.
    // if (isMobile) {
    //     connectOptions.bandwidthProfile.video.maxSubscriptionBitrate = 2500000;
    // }

    // var mainUrl = "{{ env('APP_URL') }}";
    var mainUrl = "https://test.tsicmentorapp.org";

    // var identity = '{{ Auth::user()->firstname . ' ' . Auth::user()->lastname }}';
    var identity = document.getElementById("selfName").value;

    var selfType = document.getElementById("selfType").value;
    var selfId = document.getElementById("selfId").value;
    var otherType = document.getElementById("otherType").value;
    var otherId = document.getElementById("otherId").value;
    var otherDeviceType = document.getElementById("otherDeviceType").value;
    var otherFirebaseId = document.getElementById("otherFirebaseId").value;
    var otherVoipToken = document.getElementById("otherVoipToken").value;

    roomCheckData = {
        sender_id: otherId,
        sender_type: otherType,
        receiver_id: selfId,
        receiver_type: selfType,
        receiver_device: "web",
    };

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

    function initiateCall() {
        $.post(
            mainUrl + "/api/webvideochat/initiate_chat",
            inititateData,
            function(roomData, status) {
                if (roomData.status === false) {
                    alert(roomData.message);
                    document.getElementById("roomConnectingBtn").style.display = "none";
                    document.getElementById("roomJoinBtn").style.display = "inline";
                    // leaveRoomIfJoined();
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

                // Add the specified Room name to ConnectOptions.
                connectOptions.name = roomName;

                // Add the specified video device ID to ConnectOptions.
                if (roomCreateData.remaining_time < 10) {
                    alert("You dont have enought time to call this user.");
                    document.getElementById("roomConnectingBtn").style.display = "none";
                    document.getElementById("roomJoinBtn").style.display = "inline";
                    return;
                }
                sendRequest = true;
                remainimgCallTime = roomCreateData.remaining_time;

                $("#countDownTime").show();
                countDownInterval = setInterval(countDown, 1000);

                Video.init("en-US", "Global", {
                    patchJsMedia: true
                }).then(() => {
                    Video.join(roomName, tokenData, identity).then(
                        roomJoined,
                        function(error) {
                            sendRequest = false;
                            console.log("Video.connect ERROR");
                            console.log(error);

                            if (error.message) {
                                alert(error.message);
                            }

                            $.post(
                                mainUrl + "/api/webvideochat/disconnect_room", {
                                    unique_name: roomCreateData.unique_name,
                                },
                                function(data, status) {
                                    roomCreateData = {};
                                    alert(data.message);
                                    document.getElementById("roomConnectingBtn").style.display = "none";
                                    document.getElementById("roomJoinBtn").style.display = "inline";
                                }
                            );

                            if (countDownInterval) {
                                clearInterval(countDownInterval);
                                $("#countDownTime").hide();
                            }
                        }
                    );
                });
            });
    }


    function attachVideoElement(userId, videoElement) {
        videoElement.id = `user-video-${userId}`;
        document.querySelector("video-player-container").appendChild(videoElement);
    }

    function detachVideoElement(userId) {
        const userVideoElement = document.getElementById(`user-video-${userId}`);
        if (userVideoElement) {
            userVideoElement.remove();
        }
    }

    function roomJoined() {
        let userList = {};
        callReceived = true;

        clearInterval(receiverInterval);

        zoomSession = Video.getMediaStream();
        zoomSession.startAudio();

        if (zoomSession.isRenderSelfViewWithVideoElement()) {
            zoomSession
                .startVideo({
                    videoElement: document.querySelector("#my-self-view-video"),
                })
                .then(() => {
                    document.getElementById("roomJoinBtn").style.display = "none";
                    document.getElementById("roomConnectingBtn").style.display = "none";
                    document.getElementById("roomLeftBtn").style.display = "inline";

                    activeRoom = true;

                    Video.on("peer-video-state-change", (payload) => {
                        if (payload.action === "Start") {
                            userList.userId = payload.userId;
                            zoomSession.attachVideo(payload.userId, 1).then((userVideo) => {
                                attachVideoElement(userList.userId, userVideo);
                            });
                        } else if (payload.action === "Stop") {
                            activeRoom = false;
                            delete userList.userId;
                            zoomSession.detachVideo(payload.userId);
                            detachVideoElement(payload.userId);

                            clearInterval(countDownInterval);
                            $("#countDownTime").hide();

                            if (callReceived == false) {
                                socketConnect.emit("endBeforeReceived", inititateData);
                            }

                            // if (selfType.value === "mentor") {
                                document.getElementById("roomJoinBtn").style.display = "inline";
                            // }
                            document.getElementById("roomLeftBtn").style.display = "none";
                        }
                    });

                    Video.getAllUser().forEach((user) => {
                        if (user.bVideoOn) {
                            if (identity !== user.displayName) {
                                zoomSession.attachVideo(user.userId, 3).then((userVideo) => {
                                    attachVideoElement(user.userId, userVideo);
                                    userList.userId = user.userId;
                                });
                            }
                        }
                    });

                    Video.on("user-added", (payload) => {
                        userList.userId = payload[0].userId;
                    });

                    // session ended by host
                    Video.on("connection-change", (payload) => {
                        if (payload.state === "Closed") {
                            clearInterval(countDownInterval);
                            $("#countDownTime").hide();

                            detachVideoElement(userList.userId);
                            Video.leave();
                            zoomSession.muteAudio();

                            // if (selfType.value === "mentor") {
                                document.getElementById("roomJoinBtn").style.display = "inline";
                            // }
                            document.getElementById("roomLeftBtn").style.display = "none";

                            if (callReceived == false) {
                                socketConnect.emit("endBeforeReceived", inititateData);
                            }

                            // window.location.reload();
                            document.getElementById("roomJoinBtn").style.display = "inline";
                            document.getElementById("roomLeftBtn").style.display = "none";
                            document.getElementById("my-self-view-video").style.display = "none";

                            // if (callReceived == true) {
                            //     alert("The mentor has ended the video call.");
                            // }
                        }
                    });

                    // user left
                    Video.on("user-removed", (payload) => {
                        endCallAndCleanup(payload[0].userId);
                    });

                    function endCallAndCleanup(userId) {
                        delete userList[userId];
                        if (!callReceived) {
                            socketConnect.emit("endBeforeReceived", inititateData);
                        }

                        // Stop and detach video
                        zoomSession.muteAudio();
                        zoomSession
                            .stopVideo()
                            .then(() => {
                                // console.log("Video stopped successfully for user:", userId);
                                return zoomSession.detachVideo(userId);
                            })
                            .then(() => {
                                // console.log("Video detached successfully for user:", userId);
                                detachVideoElement(userId);
                                Video.leave();
                                // window.location.reload();
                            })
                            .catch((error) => {
                                console.error(
                                    "Error stopping or detaching video for user:",
                                    userId,
                                    error
                                );
                            });

                        clearInterval(countDownInterval);
                        $("#countDownTime").hide();

                        document.getElementById("roomLeftBtn").style.display = "none";
                        document.getElementById("roomJoinBtn").style.display = "inline";
                        document.getElementById("my-self-view-video").style.display = "none";
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        } else {
            zoomSession
                .startVideo()
                .then(() => {
                    zoomSession
                        .renderVideo(
                            document.querySelector("#my-self-view-canvas"),
                            Video.getCurrentUserInfo().userId,
                            1920,
                            1080,
                            0,
                            0,
                            3
                        )
                        .then(() => {
                            // video successfully started and rendered
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                })
                .catch((error) => {
                    console.log(error);
                });
        }

        
        if (userType == "receiver") {
            remainimgCallTime = roomCreateData.remaining_time;
            $("#countDownTime").show();

            if (!timerActive) {
                timerActive = true;
                countDownInterval = setInterval(countDown, 1000);
            }
        }

        socketConnect = io.connect(mainUrl + ":3000", {
            transports: ["websocket", "polling", "flashsocket"],
        });

        socketConnect.on("connect", function() {
            var det = {
                id: inititateData.sender_id,
                type: inititateData.sender_type,
                status: "videoChat",
            };
            socketConnect.emit("connected", det);

            if (sendRequest) {
                socketConnect.emit("reqSend", inititateData);
            }

            if (userType == "receiver") {
                socketConnect.emit("getTimer", roomCheckData);
            }
        });

        socketMain.on("getTimerVal", function(data) {
            data.remainimgCallTime = remainimgCallTime;

            socketConnect.emit("setTimer", data);
            callReceived = true;
        });

        socketMain.on("setTimerVal", function(data) {
            remainimgCallTime = data.remainimgCallTime;

            $("#countDownTime").show();

            if (!timerActive) {
                timerActive = true;
                countDownInterval = setInterval(countDown, 1000);
            }
        });
    }


    function countDown() {
        if (remainimgCallTime < 2) {
            if (activeRoom) {
                Video.leave();
                $.post(
                    mainUrl + "/api/webvideochat/disconnect_room", {
                        room_sid: roomCreateData.unique_name,
                    },
                    function(data, status) {
                        roomCreateData = {};
                        alert(data.message);
                    }
                );
            }

            clearInterval(countDownInterval);
            $("#countDownTime").hide();
        }

        if (
            inititateData.remaining_time > remainimgCallTime + 50 &&
            callReceived == false
        ) {
            if (activeRoom) {
                Video.leave();
                $.post(
                    mainUrl + "/api/webvideochat/disconnect_room", {
                        unique_name: roomCreateData.unique_name,
                    },
                    function(data, status) {
                        roomCreateData = {};
                        alert("User did not received the call.");
                        // alert(data.message);
                        window.location.reload();
                    }
                );
            }

            clearInterval(countDownInterval);
            $("#countDownTime").hide();
        }

        var timeCount = secondsToDhms(remainimgCallTime);
        remainimgCallTime = remainimgCallTime - 1;
        $("#countDownTime").html(timeCount);

        if (remainimgCallTime < 300) {
            $("#countDownTime").addClass("text-danger");
            if (remainimgCallTime < 120) {
                $("#countDownTime").addClass("blinking");
            } else {
                $("#countDownTime").removeClass("blinking");
            }
        } else {
            $("#countDownTime").removeClass("text-danger");
            $("#countDownTime").removeClass("blinking");
        }
    }

    function secondsToDhms(seconds) {
        var h = Math.floor((seconds % (3600 * 24)) / 3600);
        var m = Math.floor((seconds % 3600) / 60);
        var s = Math.floor(seconds % 60);

        if (h < 10) {
            h = "0" + h;
        }
        if (m < 10) {
            m = "0" + m;
        }
        if (s < 10) {
            s = "0" + s;
        }

        var displayTime = h + ":" + m + ":" + s;
        // console.log(displayTime);
        return displayTime;
    }

    receiverInterval = setInterval(function () {
        $.post(
            mainUrl + "/api/webvideochat/check_room",
            roomCheckData,
            function(roomData, status) {
                if (roomData.status) {
                    roomCreateData = roomData.data;
                    roomName = roomCreateData.unique_name;

                    userType = "receiver";

                    connectOptions.name = roomName;
                    if (roomCreateData.token) {
                        tokenData = roomCreateData.token;
                    }

                    if (previewTracks) {
                        connectOptions.tracks = previewTracks;
                    }

                    if (typeof navigator !== "undefined") {
                        if (
                            typeof navigator.getMedia === "undefined" &&
                            typeof navigator.mediaDevices === "object" &&
                            typeof navigator.mediaDevices.getUserMedia === "function"
                        ) {
                            navigator.mediaDevices
                                .getUserMedia({
                                    video: true,
                                    audio: true
                                })
                                .then(function() {
                                    Video.init("en-US", "Global", {
                                        patchJsMedia: true
                                    }).then(
                                        () => {
                                            Video.join(roomName, tokenData, identity).then(
                                                roomJoined,
                                                function(error) {
                                                    console.log("Video.connect");
                                                    console.log("error Here", error);
                                                }
                                            );
                                        }
                                    );
                                })
                                .catch(function(err) {
                                    console.log("video, audio false");
                                    alert("Please allow permission for camera and microphone");
                                });
                        } else {
                            navigator.getMedia({
                                    video: true,
                                    audio: true
                                },
                                function() {
                                    Video.init("en-US", "Global", {
                                        patchJsMedia: true
                                    }).then(
                                        () => {
                                            Video.join(roomName, tokenData, identity).then(
                                                roomJoined,
                                                function(error) {
                                                    console.log("Video.connect");
                                                    console.log(error);
                                                }
                                            );
                                        }
                                    );
                                },
                                function() {
                                    console.log("video, audio false");
                                    alert("Please allow permission for camera and microphone");
                                }
                            );
                        }
                    }
                } else {
                    document.getElementById("roomJoinBtn").style.display = "inline";
                }
            }
        );
    }, 2000); // Poll every 5 seconds

    // Activity log.
    function log(message) {
        console.log(message);
    }

    // Leave Room.
    function leaveRoomIfJoined() {
        if (activeRoom) {
            Video.leave();
        }
    }

    // Bind button to join Room.
    document.getElementById("roomJoinBtn").onclick = function() {
        userType = "sender";
        document.getElementById("roomJoinBtn").style.display = "none";
        document.getElementById("roomConnectingBtn").style.display = "inline";

        if (typeof navigator !== "undefined") {
            if (
                typeof navigator.getMedia === "undefined" &&
                typeof navigator.mediaDevices === "object" &&
                typeof navigator.mediaDevices.getUserMedia === "function"
            ) {
                navigator.mediaDevices
                    .getUserMedia({
                        video: true,
                        audio: true
                    })
                    .then(() => {
                        initiateCall();
                    })
                    .catch((err) => {
                        alert("Please allow permission for camera and microphone");
                        document.getElementById("roomConnectingBtn").style.display = "none";
                        document.getElementById("roomJoinBtn").style.display = "inline";
                    });
            } else {
                navigator.getMedia({
                        video: true,
                        audio: true
                    },
                    function() {
                        initiateCall();
                    },
                    function() {
                        alert("Please allow permission for camera and microphone");
                        document.getElementById("roomConnectingBtn").style.display = "none";
                        document.getElementById("roomJoinBtn").style.display = "inline";
                    }
                );
            }
        }
    };

    // Bind button to leave Room.
    document.getElementById("roomLeftBtn").onclick = function() {
        log("Leaving room...");
        Video.leave();

        document.getElementById("roomJoinBtn").style.display = "inline";
        document.getElementById("roomLeftBtn").style.display = "none";
        document.getElementById("my-self-view-video").style.display = "none";

        if (countDownInterval) {
            clearInterval(countDownInterval);
            $("#countDownTime").hide();
        }

        $.post(
            mainUrl + "/api/webvideochat/disconnect_room", {
                unique_name: roomCreateData.unique_name,
            },
            function(data, status) {
                roomCreateData = {};
                alert(data.message);
                window.location.reload();
                // window.location.href = mainUrl + "/mentor/chat/userlist?type=mm";
            }
        );
    };
</script>
@endsection
