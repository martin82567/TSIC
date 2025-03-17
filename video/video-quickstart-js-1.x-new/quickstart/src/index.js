
"use strict";

require("dotenv").load({ path: "../../../.env" });
// var Video = require('twilio-video');
var ZoomVideo = window.WebVideoSDK.default;
var Video = ZoomVideo.createClient();
var zoomSession;
navigator.getMedia =
  navigator.getUserMedia ||
  navigator.webkitGetUserMedia ||
  navigator.mozGetUserMedia ||
  navigator.msGetUserMedia;
const { isMobile } = require("./browser");

var socketConnect;
var activeRoom;
var previewTracks;
var identity;
var tokenData;
var roomName;
var roomCreateData = {};
var sendRequest = false;
var callReceived = false;

var roomCheckData = {};
var inititateData = {};

var remainimgCallTime = 0;
var countDownInterval;
var timerActive = false;

var userType = "sender";

const connectOptions = {};

// For mobile browsers, limit the maximum incoming video bitrate to 2.5 Mbps.
if (isMobile) {
  connectOptions.bandwidthProfile.video.maxSubscriptionBitrate = 2500000;
}

// On mobile browsers, there is the possibility of not getting any media even
// after the user has given permission, most likely due to some other app reserving
// the media device. So, we make sure users always test their media devices before
// joining the Room. For more best practices, please refer to the following guide:
// https://www.twilio.com/docs/video/build-js-video-application-recommendations-and-best-practices
const deviceIds = {
  audio: isMobile ? null : localStorage.getItem("audioDeviceId"),
  video: isMobile ? null : localStorage.getItem("videoDeviceId"),
};

var mainUrl = process.env.APP_URL;

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

// When we are about to transition away from this page, disconnect
// from the room, if joined.
window.addEventListener("beforeunload", leaveRoomIfJoined);

// Obtain a token from the server in order to connect to the Room.
// $.getJSON("https://localhost:3000/token", function (data) {
$.getJSON(mainUrl + ":3000/token", function (data) {
  // console.log("data : ", data);

  identity = data.identity;
  tokenData = data.token;

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


  $.post(
    mainUrl + "/api/webvideochat/check_room",
    roomCheckData,
    function (roomData, status) {
      if (roomData.status) {
        roomCreateData = roomData.data;
        roomName = roomCreateData.unique_name;

        userType = "receiver";

        connectOptions.name = roomName;
        if (roomCreateData.token) {
          tokenData = roomCreateData.token;
        }

        // // Add the specified video device ID to ConnectOptions.
        // connectOptions.video.deviceId = { exact: deviceIds.video };

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
              .getUserMedia({ video: true, audio: true })
              .then(function () {
                Video.init("en-US", "Global", { patchJsMedia: true }).then(
                  () => {
                    Video.join(roomName, tokenData, identity).then(
                      roomJoined,
                      function (error) {
                        console.log("Video.connect");
                        console.log("error Here", error);
                      }
                    );
                  }
                );
              })
              .catch(function (err) {
                console.log("video, audio false");
                alert("Please allow permission for camera and microphone");
              });
          } else {
            navigator.getMedia(
              { video: true, audio: true },
              function () {
                Video.init("en-US", "Global", { patchJsMedia: true }).then(
                  () => {
                    Video.join(roomName, tokenData, identity).then(
                      roomJoined,
                      function (error) {
                        console.log("Video.connect");
                        console.log(error);
                      }
                    );
                  }
                );
              },
              function () {
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

  // Bind button to join Room.
  document.getElementById("roomJoinBtn").onclick = function () {
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
          .getUserMedia({ video: true, audio: true })
          .then(() => {
            initiateCall();
          })
          .catch((err) => {
            alert("Please allow permission for camera and microphone");
            document.getElementById("roomConnectingBtn").style.display = "none";
            document.getElementById("roomJoinBtn").style.display = "inline";
          });
      } else {
        navigator.getMedia(
          { video: true, audio: true },
          function () {
            initiateCall();
          },
          function () {
            alert("Please allow permission for camera and microphone");
            document.getElementById("roomConnectingBtn").style.display = "none";
            document.getElementById("roomJoinBtn").style.display = "inline";
          }
        );
      }
    }
  };

  // Bind button to leave Room.
  document.getElementById("roomLeftBtn").onclick = function () {
    log("Leaving room...");
    Video.leave();
    if(selfType === 'mentor' || selfType.value === "mentor"){
      document.getElementById("roomJoinBtn").style.display = "inline";
      document.getElementById("roomLeftBtn").style.display = "none";
    }else{
	window.location.reload();
    }

    if (countDownInterval) {
      clearInterval(countDownInterval);
      $("#countDownTime").hide();
    }

    $.post(
      mainUrl + "/api/webvideochat/disconnect_room",
      {
         unique_name: roomCreateData.unique_name,
      },
      function (data, status) {
        roomCreateData = {};
        alert(data.message);
      }
    );
  };
});

function initiateCall() {
  $.post(
    mainUrl + "/api/webvideochat/initiate_chat",
    inititateData,
    function (roomData, status) {
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

      // Add the specified audio device ID to ConnectOptions.
      // connectOptions.audio = { deviceId: { exact: deviceIds.audio } };

      // var connectOptions = {
      //   name: roomName,
      //   logLevel: 'debug'
      // };

      // Add the specified Room name to ConnectOptions.
      connectOptions.name = roomName;

      // Add the specified video device ID to ConnectOptions.
      // connectOptions.video.deviceId = { exact: deviceIds.video };

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

      Video.init("en-US", "Global", { patchJsMedia: true }).then(() => {
        Video.join(roomName, tokenData, identity).then(
          roomJoined,
          function (error) {
            sendRequest = false;
            console.log("Video.connect ERROR");
            console.log(error);

            if (error.message) {
              alert(error.message);
            }

            $.post(
              mainUrl + "/api/webvideochat/disconnect_room",
              {
                unique_name: roomCreateData.unique_name,
              },
              function (data, status) {
                roomCreateData = {};
                alert(data.message);
                document.getElementById("roomConnectingBtn").style.display =
                  "none";
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
    }
  );
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
  callReceived = false;

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

            if (selfType.value === "mentor") {
              document.getElementById("roomJoinBtn").style.display = "inline";
            }
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

            if (selfType.value === "mentor") {
              document.getElementById("roomJoinBtn").style.display = "inline";
            }
            document.getElementById("roomLeftBtn").style.display = "none";

            if (callReceived == false) {
              socketConnect.emit("endBeforeReceived", inititateData);
            }
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
          console.log("selfType", selfType.value);
          if (selfType.value === "mentor") {
            document.getElementById("roomJoinBtn").style.display = "inline";
          } 
          document.getElementById("roomLeftBtn").style.display = "none";
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

  socketConnect = io.connect(mainUrl + ":3000", {
    transports: ["websocket", "polling", "flashsocket"],
  });

  socketConnect.on("connect", function () {
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

  socketMain.on("getTimerVal", function (data) {
    data.remainimgCallTime = remainimgCallTime;

    socketConnect.emit("setTimer", data);
    callReceived = true;
  });

  socketMain.on("setTimerVal", function (data) {
    remainimgCallTime = data.remainimgCallTime;

    $("#countDownTime").show();

    if (!timerActive) {
      timerActive = true;
      countDownInterval = setInterval(countDown, 1000);
    }
  });
}

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

function countDown() {
  if (remainimgCallTime < 2) {
    if (activeRoom) {
      Video.leave();
      $.post(
        mainUrl + "/api/webvideochat/disconnect_room",
        {
          room_sid: roomCreateData.unique_name,
        },
        function (data, status) {
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
        mainUrl + "/api/webvideochat/disconnect_room",
        {
          unique_name: roomCreateData.unique_name,
        },
        function (data, status) {
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
