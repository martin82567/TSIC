'use strict';

require('dotenv').load({ path: '../../../.env' });
var Video = require('twilio-video');
navigator.getMedia = (navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia);
const { isMobile } = require('./browser');

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

// ConnectOptions settings for a video web application.
const connectOptions = {
  // Available only in Small Group or Group Rooms only. Please set "Room Type"
  // to "Group" or "Small Group" in your Twilio Console:
  // https://www.twilio.com/console/video/configure
  bandwidthProfile: {
    video: {
      dominantSpeakerPriority: 'high',
      mode: 'grid',
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
  video: { height: 480, frameRate: 24, width: 640 },

  audio: true
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

var mainUrl = process.env.APP_URL;

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

// Attach the Tracks to the DOM.
function attachTracks(tracks, container) {
  tracks.forEach(publication => {
    trackPublished(publication, container)
  });
};

function trackPublished(publication, container) {
  // If the TrackPublication is already subscribed to, then attach the Track to the DOM.
  if (publication.track) {
    attachTrack(publication.track, container);
  }

  // Once the TrackPublication is subscribed to, attach the Track to the DOM.
  publication.on('subscribed', track => {
    attachTrack(track, container);
  });

  // Once the TrackPublication is unsubscribed from, detach the Track from the DOM.
  publication.on('unsubscribed', track => {
    detachTrack(track, container);
  });
}

function trackUnpublished(publication, container) {
  // If the TrackPublication is already subscribed to, then attach the Track to the DOM.
  if (publication.track) {
    detachTrack(publication.track, container);
  }

  // Once the TrackPublication is subscribed to, attach the Track to the DOM.
  publication.off()
}

function attachTrack(track, container) {
  // If the attached Track is a VideoTrack that is published by the active
  // Participant, then attach it to the main video as well.
  if (track.kind === 'video' || track.kind === 'audio') {
    const $track = $(track.kind, container);
    if ($track.length > 0) {
      track.attach($track.get(0));
      $track.get(0).style.display = 'inline'
    }
  }
}

function detachTrack(track, container) {
  // If the detached Track is a VideoTrack that is published by the active
  // Participant, then detach it from the main video as well.
  if (track.kind === 'video' || track.kind === 'audio') {
    const $track = $(track.kind, container);
    if ($track) {
      track.detach($track.get(0));
      $track.get(0).style.display = 'none'
    }
  }
}

// Attach the Participant's Tracks to the DOM.
function attachParticipantTracks(participant, container) {
  var tracks = Array.from(participant.tracks.values());
  attachTracks(tracks, container);

  // Handle theTrackPublications that will be published by the Participant later.
  participant.on('trackPublished', publication => {
    trackPublished(publication, participant);
  });

  // Handle theTrackPublications that will be published by the Participant later.
  participant.on('trackUnpublished', publication => {
    trackUnpublished(publication, participant);
  });

};

// Detach the Tracks from the DOM.
function detachTracks(tracks) {
  tracks.forEach(function (track) {
    console.log("detachTracks");
    console.log(track);

    if (typeof track.detach === 'function') {
      track.detach().forEach(function (detachedElement) {
        detachedElement.remove();
      });
    };

  });
};

// Detach the Participant's Tracks from the DOM.
function detachParticipantTracks(participant) {
  var tracks = Array.from(participant.tracks.values());
  detachTracks(tracks);
};

// When we are about to transition away from this page, disconnect
// from the room, if joined.
window.addEventListener('beforeunload', leaveRoomIfJoined);

// Obtain a token from the server in order to connect to the Room.
$.getJSON(mainUrl + ':3000/token', function (data) {
  console.log(data);

  identity = data.identity;
  tokenData = data.token;

  var selfType = document.getElementById('selfType').value;
  var selfId = document.getElementById('selfId').value;
  var otherType = document.getElementById('otherType').value;
  var otherId = document.getElementById('otherId').value;
  var otherDeviceType = document.getElementById('otherDeviceType').value;
  var otherFirebaseId = document.getElementById('otherFirebaseId').value;
  var otherVoipToken = document.getElementById('otherVoipToken').value;

  roomCheckData = {
    sender_id: otherId,
    sender_type: otherType,
    receiver_id: selfId,
    receiver_type: selfType,
    receiver_device: "web"
  };

  inititateData = {
    sender_id: selfId,
    sender_type: selfType,
    sender_device: "web",
    receiver_id: otherId,
    receiver_type: otherType,
    receiver_device_type: otherDeviceType,
    receiver_firebase_id: otherFirebaseId,
    receiver_voip_token: otherVoipToken
  };

  // console.log(inititateData);

  $.post(mainUrl + "/api/webvideochat/check_room", roomCheckData, function (roomData, status) {
    console.log(roomData);
    if (roomData.status) {
      roomCreateData = roomData.data;
      roomName = roomCreateData.unique_name;

      userType = "receiver";

      connectOptions.name = roomName;

      // // Add the specified video device ID to ConnectOptions.
      // connectOptions.video.deviceId = { exact: deviceIds.video };

      if (previewTracks) {
        connectOptions.tracks = previewTracks;
      }

      if (typeof navigator !== 'undefined') {
        if (typeof navigator.getMedia === 'undefined' && typeof navigator.mediaDevices === 'object' &&
          typeof navigator.mediaDevices.getUserMedia === 'function') {
          navigator.mediaDevices.getUserMedia({ video: true, audio: true })
            .then(function () {
              // Add the specified Room name to ConnectOptions.
              Video.connect(tokenData, connectOptions).then(roomJoined, function (error) {
                console.log("Video.connect");
                console.log(error);
              });
            })
            .catch(function (err) {
              console.log("video, audio false");
              alert("Please allow permission for camera and microphone");
            })
        } else {
          navigator.getMedia({ video: true, audio: true }, function () {
            Video.connect(tokenData, connectOptions).then(roomJoined, function (error) {
              console.log("Video.connect");
              console.log(error);
            });
          }, function () {
            console.log("video, audio false");
            alert("Please allow permission for camera and microphone");
          })
        }
      }

    } else {
      document.getElementById('roomJoinBtn').style.display = 'inline';
    }
  });

  // Bind button to join Room.
  document.getElementById('roomJoinBtn').onclick = function () {
    console.log("test");
    userType = "sender";
    document.getElementById('roomJoinBtn').style.display = 'none';
    document.getElementById('roomConnectingBtn').style.display = 'inline';

    if (typeof navigator !== 'undefined') {
      if (typeof navigator.getMedia === 'undefined' && typeof navigator.mediaDevices === 'object' &&
        typeof navigator.mediaDevices.getUserMedia === 'function') {
        navigator.mediaDevices.getUserMedia({ video: true, audio: true })
          .then(() => {
            initiateCall()
          })
          .catch((err) => {
            alert("Please allow permission for camera and microphone");
            document.getElementById('roomConnectingBtn').style.display = 'none';
            document.getElementById('roomJoinBtn').style.display = 'inline';
          })
      } else {
        navigator.getMedia({ video: true, audio: true }, function () {
          initiateCall()
        }, function () {
          alert("Please allow permission for camera and microphone");
          document.getElementById('roomConnectingBtn').style.display = 'none';
          document.getElementById('roomJoinBtn').style.display = 'inline';
        })
      }
    }
  };

  // Bind button to leave Room.
  document.getElementById('roomLeftBtn').onclick = function () {
    log('Leaving room...');
    activeRoom.disconnect();

    $.post(mainUrl + "/api/webvideochat/disconnect_room", {
      room_sid: roomCreateData.room_sid
    }, function (data, status) {
      roomCreateData = {};
      alert(data.message);
    });
  };

});

function initiateCall() {
  $.post(mainUrl + "/api/webvideochat/initiate_chat", inititateData, function (roomData, status) {
    if (roomData.status === false) {
      alert(roomData.message);
      document.getElementById('roomConnectingBtn').style.display = 'none';
      document.getElementById('roomJoinBtn').style.display = 'inline';
      return;
    }
    roomCreateData = roomData.data;
    roomName = roomCreateData.unique_name;

    //
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

    if (previewTracks) {
      connectOptions.tracks = previewTracks;
    }

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
}

// Successfully connected!
function roomJoined(room) {
  window.room = activeRoom = room;
  callReceived = false;

  log("Joined as '" + identity + "'");
  // document.getElementById('button-join').style.display = 'none';
  // document.getElementById('button-leave').style.display = 'inline';

  document.getElementById('roomJoinBtn').style.display = 'none';
  document.getElementById('roomConnectingBtn').style.display = 'none';
  document.getElementById('roomLeftBtn').style.display = 'inline';

  // Attach LocalParticipant's Tracks, if not already attached.
  var previewContainer = document.getElementById('local-media');
  // if (!previewContainer.querySelector('video')) {
  attachParticipantTracks(room.localParticipant, previewContainer);
  // }

  // Attach the Tracks of the Room's Participants.
  room.participants.forEach(function (participant) {
    log("Already in Room: '" + participant.identity + "'");
    var previewContainer = document.getElementById('remote-media');
    attachParticipantTracks(participant, previewContainer);
  });

  // When a Participant joins the Room, log the event.
  room.on('participantConnected', function (participant) {
    log("Joining: '" + participant.identity + "'");
    var previewContainer = document.getElementById('remote-media');
    attachParticipantTracks(participant, previewContainer);
  });

  // When a Participant adds a Track, attach it to the DOM.
  // room.on('trackAdded', function (track, participant) {
  //   log(participant.identity + " added track: " + track.kind);
  //   var previewContainer = document.getElementById('remote-media');
  //   attachTracks([track], previewContainer);
  // });



  // // When a Participant removes a Track, detach it from the DOM.
  // room.on('trackRemoved', function (track, participant) {
  //   log(participant.identity + " removed track: " + track.kind);
  //   detachTracks([track]);
  // });

  // When a Participant leaves the Room, detach its Tracks.
  room.on('participantDisconnected', function (participant) {
    log("Participant '" + participant.identity + "' left the room");
    detachParticipantTracks(participant);
  });

  // Once the LocalParticipant leaves the room, detach the Tracks
  // of all Participants, including that of the LocalParticipant.
  room.on('disconnected', function () {
    if (previewTracks) {
      previewTracks.forEach(function (track) {
        track.stop();
      });
      previewTracks = null;
    }
    detachParticipantTracks(room.localParticipant);
    room.participants.forEach(detachParticipantTracks);
    activeRoom = null;
    // document.getElementById('button-join').style.display = 'inline';
    // document.getElementById('button-leave').style.display = 'none';

    if (userType === 'sender') {
      document.getElementById('roomJoinBtn').style.display = 'inline';
    }
    document.getElementById('roomLeftBtn').style.display = 'none';

    if (countDownInterval) {
      clearInterval(countDownInterval);
      $("#countDownTime").hide();
    };

    if (callReceived == false) {
      socketConnect.emit('endBeforeReceived', inititateData);
    };
  });

  socketConnect = io.connect(mainUrl + ":3000", {
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
};

// Activity log.
function log(message) {
  console.log(message);
};

// Leave Room.
function leaveRoomIfJoined() {
  if (activeRoom) {
    activeRoom.disconnect();
  }
};

function countDown() {
  if (remainimgCallTime < 2) {
    if (activeRoom) {
      activeRoom.disconnect();
      $.post(mainUrl + "/api/webvideochat/disconnect_room", {
        room_sid: roomCreateData.room_sid
      }, function (data, status) {
        roomCreateData = {};
        alert(data.message);
      });
    };

    clearInterval(countDownInterval);
    $("#countDownTime").hide();
  };

  // console.log(inititateData.remaining_time);

  if (inititateData.remaining_time > remainimgCallTime + 50 && callReceived == false) {
    if (activeRoom) {
      activeRoom.disconnect();
      $.post(mainUrl + "/api/webvideochat/disconnect_room", {
        room_sid: roomCreateData.room_sid
      }, function (data, status) {
        roomCreateData = {};
        alert("User did not received the call.");
        // alert(data.message);
      });
    };

    clearInterval(countDownInterval);
    $("#countDownTime").hide();
  };

  var timeCount = secondsToDhms(remainimgCallTime);
  remainimgCallTime = remainimgCallTime - 1;
  // console.log(timeCount);
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
};
