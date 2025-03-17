'use strict';

/**
 * Load Twilio configuration from .env config file - the following environment
 * variables should be set:
 * process.env.TWILIO_ACCOUNT_SID
 * process.env.TWILIO_API_KEY
 * process.env.TWILIO_API_SECRET
 */
require('dotenv').load({ path: '../../.env' });

const express = require('express');
const http = require('http');
const path = require('path');
const { jwt: { AccessToken } } = require('twilio');
var cors = require('cors');

var randomName = require('./randomname');

const VideoGrant = AccessToken.VideoGrant;

// Max. period that a Participant is allowed to be in a Room (currently 14400 seconds or 4 hours)
const MAX_ALLOWED_SESSION_DURATION = 14400;

// Create Express webapp.
const app = express();
app.use(cors());

// Create http server and run it.
var server = http.createServer(app);
var port = process.env.video_chat_port || 3000;

/*server.listen(port, function() {
  console.log('Express server running on *:' + port);
});*/

var fs = require('fs');
var https = require('https');
// const options = {
//   key: fs.readFileSync(`../../../../../../etc/letsencrypt/archive/tsicmentorapp.org/privkey7.pem`),
//   cert: fs.readFileSync(`../../../../../../etc/letsencrypt/archive/tsicmentorapp.org/cert7.pem`)
// };
const options = {
  key: fs.readFileSync(process.env.VIDEO_PRIVAY_KEY_FILE),
  cert: fs.readFileSync(process.env.VIDEO_CERT_FILE)
};

var server = https.createServer(options, app).listen(port, function (req, res) {
  console.log(`Server now listening ${port} https.`);
});

var io = require('socket.io')(server);

// Apn Connect
var apn = require('apn');
// var apnOptions = {
//   token: {
//     key: "../APNsAuthKey_XXXXXXXXXX.p8",
//     keyId: "key-id",
//     teamId: "developer-team-id"
//   },
//   production: false
// };
// var apnProvider = new apn.Provider(apnOptions);

var gcm = require('node-gcm');
var gcmSender = new gcm.Sender(process.env.GCM_SENDER_KEY);

// Set up the paths for the examples.
[
  'bandwidthconstraints',
  'codecpreferences',
  'dominantspeaker',
  'localvideofilter',
  'localvideosnapshot',
  'mediadevices',
  'networkquality',
  'reconnection',
  'screenshare',
  'localmediacontrols',
  'remotereconnection',
  'datatracks',
  'manualrenderhint',
  'autorenderhint'
].forEach(example => {
  const examplePath = path.join(__dirname, `../examples/${example}/public`);
  app.use(`/${example}`, express.static(examplePath));
});

// Set up the path for the quickstart.
const quickstartPath = path.join(__dirname, '../quickstart/public');
app.use('/quickstart', express.static(quickstartPath));

// Set up the path for the examples page.
const examplesPath = path.join(__dirname, '../examples');
app.use('/examples', express.static(examplesPath));

/**
 * Default to the Quick Start application.
 */
app.get('/', (request, response) => {
  response.redirect('/quickstart');
});

/**
 * Generate an Access Token for a chat application user - it generates a random
 * username for the client requesting a token, and takes a device ID as a query
 * parameter.
 */
app.get('/token', function(request, response) {
  var identity = randomName();

  response.header("Access-Control-Allow-Origin", "*");
  response.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");

  // Create an access token which we will sign and return to the client,
  // containing the grant we just created.
  const token = new AccessToken(
    process.env.TWILIO_ACCOUNT_SID,
    process.env.TWILIO_API_KEY,
    process.env.TWILIO_API_SECRET,
    { ttl: MAX_ALLOWED_SESSION_DURATION }
  );

  // Assign the generated identity to the token.
  token.identity = identity;

  // Grant the access token Twilio Video capabilities.
  const grant = new VideoGrant();
  token.addGrant(grant);

  // Serialize the token to a JWT string.
  response.send({
    identity: identity,
    token: token.toJwt()
  });
});

// SOCKET
io.on('connect', function onConnect(socket) {
  console.log(`${socket.id} connected.`);

  socket.on('connected', function connected(det) {
    console.log("connected");
    console.log(det);
    if (det.type && det.id) {
      socket.join('room_' + det.type + '_' + det.id);
    };
  });

  socket.on('reqSend', function (data) {
    console.log("reqSend");
    console.log(data);
    io.to('room_' + data.receiver_type + '_' + data.receiver_id).emit('reqReceived', data);

    sendNotification(data);
  });

  socket.on('callDenied', function (data) {
    console.log("callDenied");
    console.log(data);
    io.to('room_' + data.sender_type + '_' + data.sender_id).emit('endVideo', data);
  });

  socket.on('getTimer', function (data) {
    console.log("getTimer");
    console.log(data);
    io.to('room_' + data.sender_type + '_' + data.sender_id).emit('getTimerVal', data);
  });

  socket.on('setTimer', function (data) {
    console.log("setTimer");
    console.log(data);
    io.to('room_' + data.receiver_type + '_' + data.receiver_id).emit('setTimerVal', data);
  });

  socket.on('disconnect', function (data) {
    console.log("disconnect");
    console.log(data);
  });

  socket.on('roomExit', function (det) {
  });

  socket.on('endBeforeReceived', function (data) {
    console.log("endBeforeReceived");
    console.log(data.receiver_device_type);
    console.log(data.receiver_firebase_id);
    var dateStamp = new Date();

    data.endBefore = true;
    io.to('room_' + data.receiver_type + '_' + data.receiver_id).emit('endVideo', data);

    if (data.receiver_device_type == "android" && data.receiver_firebase_id) {
      var notificationMsg = new gcm.Message({
        data: {
          title: "Video Call Cancel",
          type: "video_chat",
          call_from: "web",
          timestamp: dateStamp.getTime()
        },
        notification: {
        }
      });

      var regTokens = [data.receiver_firebase_id];

      gcmSender.send(notificationMsg, {
        registrationTokens: regTokens
      }, function (err, response) {
        if (err) {
          console.error("err " + err);
        } else {
          console.log("response " + JSON.stringify(response));
        }
      });
    };
  });

  function sendNotification(message) {
    var dateStamp = new Date();

    if (message.receiver_device_type == "android" && message.receiver_firebase_id) {
      var notificationMsg = new gcm.Message({
        data: {
          title: "Incoming Video call",
          sender_name: "Testing",
          type: "video_chat",
          call_from: "web",
          receiver_accesstoken: message.receiver_accesstoken,
          unique_name: message.unique_name,
          room_sid: message.room_sid,
          remaining_time: message.remaining_time,
          created_at: message.created_at,
          timestamp: dateStamp.getTime()
        },
        notification: {
        }
      });

      var regTokens = [message.receiver_firebase_id];

      gcmSender.send(notificationMsg, {
        registrationTokens: regTokens
      }, function (err, response) {
        if (err) {
          console.error("err " + err);
        } else {
          console.log("response " + JSON.stringify(response));
        }
      });
    };
  };

});
