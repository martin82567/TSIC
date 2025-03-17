'use strict';

const { isSupported } = require('twilio-video');
navigator.getMedia = (navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia);

var roomName;
var roomCreateData = {};
var sendRequest = false;
var previewTracks;

var roomCheckData = {};
var inititateData = {};
var tokenData;

var remainimgCallTime = 0;
var countDownInterval;
var timerActive = false;

var userType = "sender";

var mainUrl = process.env.APP_URL;

const { isMobile } = require('./browser');
const joinRoom = require('./joinroom');
const micLevel = require('./miclevel');
const selectMedia = require('./selectmedia');
const selectRoom = require('./selectroom');
const showError = require('./showerror');

const $modals = $('#modals');
const $selectMicModal = $('#select-mic', $modals);
const $selectCameraModal = $('#select-camera', $modals);
const $showErrorModal = $('#show-error', $modals);
const $joinRoomModal = $('#join-room', $modals);

var selfType = document.getElementById('selfType').value;
var selfId = document.getElementById('selfId').value;
var otherType = document.getElementById('otherType').value;
var otherId = document.getElementById('otherId').value;
var otherDeviceType = document.getElementById('otherDeviceType').value;
var otherFirebaseId = document.getElementById('otherFirebaseId').value;
var otherVoipToken = document.getElementById('otherVoipToken').value;


function secondsToDhms(seconds) {
  var h = Math.floor(seconds % (3600 * 24) / 3600);
  var m = Math.floor(seconds % 3600 / 60);
  var s = Math.floor(seconds % 60);

  if (h < 10) {
    h = "0" + h;
  };
  if (m < 10) {
    m = "0" + m;
  };
  if (s < 10) {
    s = "0" + s;
  };

  var displayTime = h + ":" + m + ":" + s;
  // console.log(displayTime);
  return displayTime;
};

// ConnectOptions settings for a video web application.
const connectOptions = {
  // Available only in Small Group or Group Rooms only. Please set "Room Type"
  // to "Group" or "Small Group" in your Twilio Console:
  // https://www.twilio.com/console/video/configure
  bandwidthProfile: {
    video: {
      dominantSpeakerPriority: 'high',
      mode: 'collaboration',
      clientTrackSwitchOffControl: 'auto',
      contentPreferencesMode: 'auto'
    }
  },

  // Available only in Small Group or Group Rooms only. Please set "Room Type"
  // to "Group" or "Small Group" in your Twilio Console:
  // https://www.twilio.com/console/video/configure
  dominantSpeaker: true,

  // Comment this line if you are playing music.
  maxAudioBitrate: 16000,

  // VP8 simulcast enables the media server in a Small Group or Group Room
  // to adapt your encoded video quality for each RemoteParticipant based on
  // their individual bandwidth constraints. This has no utility if you are
  // using Peer-to-Peer Rooms, so you can comment this line.
  preferredVideoCodecs: [{ codec: 'VP8', simulcast: true }],

  // Capture 720p video @ 24 fps.
  video: { height: 720, frameRate: 24, width: 1280 }
};

// For mobile browsers, limit the maximum incoming video bitrate to 2.5 Mbps.
if (isMobile) {
  connectOptions
    .bandwidthProfile
    .video
    .maxSubscriptionBitrate = 2500000;
}

// On mobile browsers, there is the possibility of not getting any media even
// after the user has given permission, most likely due to some other app reserving
// the media device. So, we make sure users always test their media devices before
// joining the Room. For more best practices, please refer to the following guide:
// https://www.twilio.com/docs/video/build-js-video-application-recommendations-and-best-practices
const deviceIds = {
  audio: isMobile ? null : localStorage.getItem('audioDeviceId'),
  video: isMobile ? null : localStorage.getItem('videoDeviceId')
};

/**
 * Select your camera.
 */
async function selectCamera() {
  if (deviceIds.video === null) {
    try {
      deviceIds.video = await selectMedia('video', $selectCameraModal, videoTrack => {
        const $video = $('video', $selectCameraModal);
        videoTrack.attach($video.get(0))
      });
    } catch (error) {
      showError($showErrorModal, error);
      return false;
    }
  }
  return true;
}

/**
 * Select your microphone.
 */
async function selectMicrophone() {
  if (deviceIds.audio === null) {
    try {
      deviceIds.audio = await selectMedia('audio', $selectMicModal, audioTrack => {
        const $levelIndicator = $('svg rect', $selectMicModal);
        const maxLevel = Number($levelIndicator.attr('height'));
        micLevel(audioTrack, maxLevel, level => $levelIndicator.attr('y', maxLevel - level));
      });
    } catch (error) {
      showError($showErrorModal, error);
      return false;
    }
  }
  return selectCamera();
}

async function startLoading() {
  const { token, identity } = await fetch(`${mainUrl}:3000/token`);

  tokenData = token

  roomCheckData = {
    sender_id: otherId,
    sender_type: otherType,
    receiver_id: selfId,
    receiver_type: selfType,
    receiver_device: "web"
  };

  var roomData = await fetch('/api/webvideochat/check_room', {
    method: 'POST',
    body: JSON.stringify(roomCheckData)
  })

  if (roomData.status) {
    roomCreateData = roomData.data;

    if (previewTracks) {  // TODO
      connectOptions.tracks = previewTracks;
    }

    userType = "receiver";

    // Add the specified audio device ID to ConnectOptions.
    connectOptions.audio = { deviceId: { exact: deviceIds.audio } };

    // Add the specified Room name to ConnectOptions.
    connectOptions.name = roomCreateData.unique_name;

    // Add the specified video device ID to ConnectOptions.
    connectOptions.video.deviceId = { exact: deviceIds.video };

    const permissionsAccepted = await selectMicrophone()

    if(permissionsAccepted) {
      // Join the Room.
      await joinRoom(token, connectOptions);
    }

  } else {
    document.getElementById('roomJoinBtn').style.display = 'inline';
  }
}

document.getElementById('roomJoinBtn').onclick = () => sendCall()

async function sendCall() {

  userType = "sender";
  document.getElementById('roomJoinBtn').style.display = 'none';
  document.getElementById('roomConnectingBtn').style.display = 'inline';

  const permissionsAccepted = await selectMicrophone()

  const inititateData = {
    sender_id: selfId,
    sender_type: selfType,
    sender_device: "web",
    receiver_id: otherId,
    receiver_type: otherType,
    receiver_device_type: otherDeviceType,
    receiver_firebase_id: otherFirebaseId,
    receiver_voip_token: otherVoipToken
  };

  if (permissionsAccepted) {
    const roomData = await fetch('/api/webvideochat/initiate_chat', {
      method: 'POST',
      body: JSON.stringify(inititateData)
    })

    if (roomData.status) {
      alert(roomData.message);
      document.getElementById('roomConnectingBtn').style.display = 'none';
      document.getElementById('roomJoinBtn').style.display = 'inline';
      return
    }

    roomCreateData = roomData.data;
    roomName = roomCreateData.unique_name;

    if (roomCreateData.remaining_time < 10) {
      alert("You dont have enought time to call this user.");
      document.getElementById('roomConnectingBtn').style.display = 'none';
      document.getElementById('roomJoinBtn').style.display = 'inline';
      return
    }

    //
    inititateData.room_sid = roomCreateData.room_sid;
    inititateData.unique_name = roomCreateData.unique_name;
    inititateData.receiver_accesstoken = roomCreateData.receiver_accesstoken;
    inititateData.remaining_time = roomCreateData.remaining_time;
    inititateData.created_at = roomCreateData.created_at;

    sendRequest = true;
    remainimgCallTime = roomCreateData.remaining_time;

    $("#countDownTime").show();
    countDownInterval = setInterval(countDown, 1000);

    // Add the specified audio device ID to ConnectOptions.
    connectOptions.audio = { deviceId: { exact: deviceIds.audio } };

    // Add the specified Room name to ConnectOptions.
    connectOptions.name = roomCreateData.unique_name;

    // Add the specified video device ID to ConnectOptions.
    connectOptions.video.deviceId = { exact: deviceIds.video };

    joinRoom(tokenData, connectOptions).then((room) => {
      sendRequest = false;

    }).catch((error) => {
      if (error.message) {
        alert(error.message);
      }
    })

  }
}
// Bind button to join Room.
document.getElementById('roomJoinBtn').onclick = function () {
  console.log("test");
  userType = "sender";
  document.getElementById('roomJoinBtn').style.display = 'none';
  document.getElementById('roomConnectingBtn').style.display = 'inline';

  const permissionsAccepted = selectMicrophone().th

  navigator.getMedia({ video: true, audio: true }, function () {
    // webcam and mic is available
    $.post(mainUrl + "/api/webvideochat/initiate_chat", inititateData, function (roomData, status) {
      if (roomData.status == false) {
        alert(roomData.message);
        document.getElementById('roomConnectingBtn').style.display = 'none';
        document.getElementById('roomJoinBtn').style.display = 'inline';
        return;
      };
      roomCreateData = roomData.data;
      roomName = roomCreateData.unique_name;

      //
      inititateData.room_sid = roomCreateData.room_sid;
      inititateData.unique_name = roomCreateData.unique_name;
      inititateData.receiver_accesstoken = roomCreateData.receiver_accesstoken;
      inititateData.remaining_time = roomCreateData.remaining_time;
      inititateData.created_at = roomCreateData.created_at;

      var connectOptions = {
        name: roomName,
        logLevel: 'debug'
      };

      if (previewTracks) {
        connectOptions.tracks = previewTracks;
      };

      if (roomCreateData.remaining_time < 10) {
        alert("You dont have enought time to call this user.");
        document.getElementById('roomConnectingBtn').style.display = 'none';
        document.getElementById('roomJoinBtn').style.display = 'inline';
        return;
      };
      sendRequest = true;
      remainimgCallTime = roomCreateData.remaining_time;

      $("#countDownTime").show();
      countDownInterval = setInterval(countDown, 1000);

      Video.connect(tokenData, connectOptions).then(roomJoined, function (error) {
        sendRequest = false;
        console.log("Video.connect ERROR");
        console.log(error);

        if (error.message) {
          alert(error.message);
        };

        $.post(mainUrl + "/api/webvideochat/disconnect_room", {
          room_sid: roomCreateData.room_sid
        }, function (data, status) {
          roomCreateData = {};
          alert(data.message);
          document.getElementById('roomConnectingBtn').style.display = 'none';
          document.getElementById('roomJoinBtn').style.display = 'inline';
        });

        if (countDownInterval) {
          clearInterval(countDownInterval);
          $("#countDownTime").hide();
        };

      });
    });
  }, function () {
    // webcam and mic is not available
    // console.log("video, audio false");
    alert("Please allow permission for camera and microphone");
    document.getElementById('roomConnectingBtn').style.display = 'none';
    document.getElementById('roomJoinBtn').style.display = 'inline';
  });
};

// If the current browser is not supported by twilio-video.js, show an error
// message. Otherwise, start the application.
window.addEventListener('load', isSupported ? startLoading : () => {
  showError($showErrorModal, new Error('This browser is not supported.'));
});
