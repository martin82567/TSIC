'use strict';

var Video = require('twilio-video');

var activeRoom;
var previewTracks;
var identity;
var tokenData;
var roomName;
var roomCreateData = {};
var sendRequest = false;

var roomCheckData = {};
var inititateData = {};

var remainimgCallTime = 0;
var countDownInterval;

var mainUrl = "https://mentorappdev.tsic.org";

function secondsToDhms(seconds) {
  // seconds = Number(seconds);
console.log(seconds);

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
  console.log(displayTime);
  return displayTime;
};

// Attach the Tracks to the DOM.
function attachTracks(tracks, container) {
  tracks.forEach(function (track) {
    container.appendChild(track.attach());
  });
}

// Attach the Participant's Tracks to the DOM.
function attachParticipantTracks(participant, container) {
  var tracks = Array.from(participant.tracks.values());
  attachTracks(tracks, container);
}

// Detach the Tracks from the DOM.
function detachTracks(tracks) {
  tracks.forEach(function (track) {
    track.detach().forEach(function (detachedElement) {
      detachedElement.remove();
    });
  });
}

// Detach the Participant's Tracks from the DOM.
function detachParticipantTracks(participant) {
  var tracks = Array.from(participant.tracks.values());
  detachTracks(tracks);
}

// When we are about to transition away from this page, disconnect
// from the room, if joined.
window.addEventListener('beforeunload', leaveRoomIfJoined);

// Obtain a token from the server in order to connect to the Room.
$.getJSON(mainUrl + ':3000/token', function (data) {
  console.log(data);
  // const socketMain = io.connect("https://mentorappdev.tsic.org:3000");

  identity = data.identity;
  tokenData = data.token;
  // document.getElementById('room-controls').style.display = 'block';

  // // Bind button to join Room.
  // document.getElementById('button-join').onclick = function() {
  //   roomName = document.getElementById('room-name').value;
  //   if (!roomName) {
  //     alert('Please enter a room name.');
  //     return;
  //   };

  //   log("Joining room '" + roomName + "'...");
  //   var connectOptions = {
  //     name: roomName,
  //     logLevel: 'debug'
  //   };

  //   if (previewTracks) {
  //     connectOptions.tracks = previewTracks;
  //   }

  //   // Join the Room with the token from the server and the
  //   // LocalParticipant's Tracks.
  //   Video.connect(data.token, connectOptions).then(roomJoined, function(error) {
  //     console.log("Video.connect");
  //     console.log(error);

  //     log('Could not connect to Twilio: ' + error.message);
  //   });
  // };

  // // Bind button to leave Room.
  // document.getElementById('button-leave').onclick = function() {
  //   log('Leaving room...');
  //   activeRoom.disconnect();
  // };

  var selfType = document.getElementById('selfType').value;
  var selfId = document.getElementById('selfId').value;
  var otherType = document.getElementById('otherType').value;
  var otherId = document.getElementById('otherId').value;

  roomCheckData = {
    sender_id: otherId,
    sender_type: otherType,
    receiver_id: selfId,
    receiver_type: selfType
  }

  inititateData = {
    sender_id: selfId,
    sender_type: selfType,
    receiver_id: otherId,
    receiver_type: otherType
  };

  $.post(mainUrl + "/api/webvideochat/check_room", roomCheckData, function (roomData, status) {
    console.log(roomData);
    console.log(roomData.status);

    if (roomData.status) {
      roomCreateData = roomData.data;
      roomName = roomCreateData.unique_name;

      var connectOptions = {
        name: roomName,
        logLevel: 'debug'
      };

      if (previewTracks) {
        connectOptions.tracks = previewTracks;
      };

      Video.connect(tokenData, connectOptions).then(roomJoined, function (error) {
        console.log("Video.connect");
        console.log(error);
      });
    } else {
      document.getElementById('roomJoinBtn').style.display = 'inline';
    }
  });

  // Bind button to join Room.
  document.getElementById('roomJoinBtn').onclick = function () {
    console.log("test");
    // console.log(socketMain);
    // socketMain.emit('reqSend', "testing");
    // return;

    $.post(mainUrl + "/api/webvideochat/initiate_chat", inititateData, function (roomData, status) {

      if (roomData.status == false) {
        return
      };

      roomCreateData = roomData.data;
      roomName = roomCreateData.unique_name;

      var connectOptions = {
        name: roomName,
        logLevel: 'debug'
      };

      if (previewTracks) {
        connectOptions.tracks = previewTracks;
      };

      sendRequest = true;
      remainimgCallTime = roomCreateData.remaining_time;
      // if(remainimgCallTime) {
      //   parseInt(remainimgCallTime)
      // };

      $("#countDownTime").show();
      countDownInterval = setInterval(countDown, 1000);
      function countDown() {

        if (remainimgCallTime < 1) {
          activeRoom.disconnect();
          $.post(mainUrl + "/api/webvideochat/disconnect_room", {
            room_sid: roomCreateData.room_sid
          }, function (data, status) {
            roomCreateData = {};
            alert(data.message);
          });
          clearInterval(countDownInterval);
          $("#countDownTime").hide();
        };

        var timeCount = secondsToDhms(remainimgCallTime);
        remainimgCallTime = remainimgCallTime - 1;
        $("#countDownTime").html(timeCount);
      };

      Video.connect(tokenData, connectOptions).then(roomJoined, function (error) {
        sendRequest = false;
        console.log("Video.connect");
        console.log(error);
      });
    });
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

// Successfully connected!
function roomJoined(room) {
  window.room = activeRoom = room;

  log("Joined as '" + identity + "'");
  // document.getElementById('button-join').style.display = 'none';
  // document.getElementById('button-leave').style.display = 'inline';

  document.getElementById('roomJoinBtn').style.display = 'none';
  document.getElementById('roomLeftBtn').style.display = 'inline';

  // Attach LocalParticipant's Tracks, if not already attached.
  var previewContainer = document.getElementById('local-media');
  if (!previewContainer.querySelector('video')) {
    attachParticipantTracks(room.localParticipant, previewContainer);
  }

  // Attach the Tracks of the Room's Participants.
  room.participants.forEach(function (participant) {
    log("Already in Room: '" + participant.identity + "'");
    var previewContainer = document.getElementById('remote-media');
    attachParticipantTracks(participant, previewContainer);
  });

  // When a Participant joins the Room, log the event.
  room.on('participantConnected', function (participant) {
    log("Joining: '" + participant.identity + "'");
  });

  // When a Participant adds a Track, attach it to the DOM.
  room.on('trackAdded', function (track, participant) {
    log(participant.identity + " added track: " + track.kind);
    var previewContainer = document.getElementById('remote-media');
    attachTracks([track], previewContainer);
  });

  // When a Participant removes a Track, detach it from the DOM.
  room.on('trackRemoved', function (track, participant) {
    log(participant.identity + " removed track: " + track.kind);
    detachTracks([track]);
  });

  // When a Participant leaves the Room, detach its Tracks.
  room.on('participantDisconnected', function (participant) {
    log("Participant '" + participant.identity + "' left the room");
    detachParticipantTracks(participant);
  });

  // Once the LocalParticipant leaves the room, detach the Tracks
  // of all Participants, including that of the LocalParticipant.
  room.on('disconnected', function () {
    log('Left');
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

    document.getElementById('roomJoinBtn').style.display = 'inline';
    document.getElementById('roomLeftBtn').style.display = 'none';

    if (countDownInterval) {
      clearInterval(countDownInterval);
      $("#countDownTime").hide();
    };
  });

  if (sendRequest) {
    var socketConnect = io.connect(mainUrl + ":3000", {
      transports: ['websocket', 'polling', 'flashsocket']
    });
    socketConnect.on('connect', function () {
      socketConnect.emit('reqSend', inititateData);
    });
  };
};

// Preview LocalParticipant's Tracks.
// document.getElementById('button-preview').onclick = function() {
//   var localTracksPromise = previewTracks
//     ? Promise.resolve(previewTracks)
//     : Video.createLocalTracks();

//   localTracksPromise.then(function(tracks) {
//     window.previewTracks = previewTracks = tracks;
//     var previewContainer = document.getElementById('local-media');
//     if (!previewContainer.querySelector('video')) {
//       attachTracks(tracks, previewContainer);
//     }
//   }, function(error) {
//     console.error('Unable to access local media', error);
//     log('Unable to access Camera and Microphone');
//   });
// };

// Activity log.
function log(message) {
  console.log(message);
  // var logDiv = document.getElementById('log');
  // logDiv.innerHTML += '<p>&gt;&nbsp;' + message + '</p>';
  // logDiv.scrollTop = logDiv.scrollHeight;
};

// Leave Room.
function leaveRoomIfJoined() {
  if (activeRoom) {
    activeRoom.disconnect();
  }
};