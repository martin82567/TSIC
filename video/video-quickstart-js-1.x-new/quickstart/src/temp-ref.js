function roomJoined2() {
    console.log("Room Joined as " + identity)
    callReceived = false;

    let userVideos = {}

    zoomSession = Video.getMediaStream()
    // zoomSession.startAudio()
    if (zoomSession.isRenderSelfViewWithVideoElement()) {
        zoomSession
            .startVideo({ videoElement: document.querySelector('#my-self-view-video') })
            .then(() => {
                // video successfully started and rendered
                console.log("zoomSession", zoomSession)
                document.getElementById('roomJoinBtn').style.display = 'none';
                document.getElementById('roomConnectingBtn').style.display = 'none';
                document.getElementById('roomLeftBtn').style.display = 'inline';

                const removeVideoElement = (userId) => {
                    const videoToRemove = userVideos[userId];
                    if (videoToRemove) {
                        videoToRemove.remove();
                        delete userVideos[userId];
                    }
                };

                Video.on('peer-video-state-change', (payload) => {
                    if (payload.action === 'Start') {
                        console.log("step 1", payload)

                        zoomSession.attachVideo(payload.userId, 1).then((userVideo) => {
                            document.querySelector('video-player-container').appendChild(userVideo)
                            userVideos[payload.userId] = userVideo;
                        })
                    } else if (payload.action === 'Stop') {
                        // a user turned off their video, stop rendering it
                        console.log("step 2", payload)

                        removeVideoElement(payload.userId);
                        zoomSession.detachVideo(payload.userId)

                        Video.leave()
                        const USER_ID = payload.userId;

                        console.log('Attempting to stop video for user:', USER_ID);
                        zoomSession.stopVideo()
                            .then(() => {
                                console.log('Video stopped successfully for user:', USER_ID);
                                return zoomSession.detachVideo(USER_ID);
                            })
                            .then(() => {
                                console.log('Video detached successfully for user:', USER_ID);
                            })
                            .catch(error => {
                                console.error('Error stopping or detaching video for user:', USER_ID, error);
                            });


                        if (userType === 'sender') {
                            document.getElementById('roomJoinBtn').style.display = 'inline';
                            document.getElementById('my-self-view-video').style.display = "none";
                        }
                        document.getElementById('roomLeftBtn').style.display = 'none';

                        if (countDownInterval) {
                            clearInterval(countDownInterval);
                            $("#countDownTime").hide();
                        };

                        if (callReceived == false) {
                            socketConnect.emit('endBeforeReceived', inititateData);
                        };
                    }
                })

                console.log("Video.getAllUser() : ", Video.getAllUser())

                Video.getAllUser().forEach((user) => {
                    if (user.bVideoOn) {
                        if (identity !== user.displayName) {
                            zoomSession.attachVideo(user.userId, 3).then((userVideo) => {
                                document
                                    .querySelector('video-player-container')
                                    .appendChild(userVideo);
                                userVideos[user.userId] = userVideo;
                            })
                        }
                    }
                })
                // Video.on('user-added', (payload) => {
                //   console.log("User joined: ", payload[0].userId + ' joined the session');
                // });

                // Video.on('user-updated', (payload) => {
                //   // user updated, like unmuting and muting
                //   console.log("user-updated : ", payload)
                // })

                // user left
                Video.on('user-removed', (payload) => {

                    console.log("step 3 --> user left", payload)

                    if (payload[0].isHost) {
                        isHost = payload[0].isHost;
                    }
                    console.log("isHost : ", isHost)

                    if (isHost) {
                        removeVideoElement(payload[0].userId);
                        zoomSession.detachVideo(payload[0].userId);
                    }
                    document.getElementById("my-self-view-video").style.display = "none"
                    Video.leave()
                })

                // session ended by host
                Video.on('connection-change', (payload) => {
                    if (payload.state === 'Closed') {
                        console.log("step 4 --> session ended by host : ", payload)
                        for (const userId in userVideos) {
                            removeVideoElement(userId);
                        }
                        if (inititateData.sender_type === "mentor")
                            document.getElementById('roomLeftBtn').style.display = 'none';
                        Video.leave();
                    } else if (payload.state === 'Reconnecting') {
                        console.log("connection-change : ", "Reconnecting")
                    } else if (payload.state === 'Connected') {
                        console.log("connection-change : ", "Connected")
                    } else if (payload.state === 'Fail') {
                        console.log("connection-change : ", "Fail")
                    }
                })
            })
            .catch((error) => {
                console.log(error)
            })
    } else {
        zoomSession
            .startVideo()
            .then(() => {
                zoomSession
                    .renderVideo(
                        document.querySelector('#my-self-view-canvas'),
                        Video.getCurrentUserInfo().userId, 1920, 1080, 0, 0, 3)
                    .then(() => {
                        // video successfully started and rendered
                    })
                    .catch((error) => {
                        console.log(error)
                    })
            })
            .catch((error) => {
                console.log(error)
            })
    }

    socketConnect = io.connect("https://localhost" + ":3000", {
        transports: ['websocket', 'polling', 'flashsocket']
    });

    socketConnect.on('connect', function () {
        // console.log(inititateData);
        // alert(inititateData.sender_id);
        var det = {
            id: inititateData.sender_id,
            type: inititateData.sender_type,
            status: "videoChat"
        };
        socketConnect.emit('connected', det);

        if (sendRequest) {
            socketConnect.emit('reqSend', inititateData);
        };

        if (userType == "receiver") {
            socketConnect.emit('getTimer', roomCheckData);
        };
    });

    socketMain.on('getTimerVal', function (data) {
        data.remainimgCallTime = remainimgCallTime;

        socketConnect.emit('setTimer', data);
        callReceived = true;
    });

    socketMain.on('setTimerVal', function (data) {
        remainimgCallTime = data.remainimgCallTime;

        $("#countDownTime").show();

        if (!timerActive) {
            timerActive = true;
            countDownInterval = setInterval(countDown, 1000);
        };
    });
}