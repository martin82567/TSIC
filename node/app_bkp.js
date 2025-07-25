// set up the server
var express = require('express');
var app = express();

var fs = require('fs');
var https = require('https');
const options = {
    key: fs.readFileSync('../../../../etc/letsencrypt/archive/tsicmentorapp.org/privkey2.pem'),
    cert: fs.readFileSync('../../../../etc/letsencrypt/archive/tsicmentorapp.org/cert2.pem')
};

var server = https.createServer(options, app).listen(3700, function (req, res) {
    console.log(`Server now listening 3700 https.`);
});

//var server = app.listen(3700, () => console.log(`Server now listening.`));

var io = require('socket.io')(server);
var mysql = require('mysql');
var gcm = require('node-gcm');
var moment = require('moment-timezone');

var gcmSender = new gcm.Sender('AAAAW51YusU:APA91bEMmzklmTI6gYim7sQiWZzXIpVbTxujuL88nnnmXQ96enmLAZoQMy7Siyo2khPMg5pKKsvKvHb0hHbfpFSXWr41pZ5P6BgLln77Nv4zNQb1jp72COYIwaJb8SeUHYJ1D5xeffTF');

console.log(moment().format("MM-DD-YYYY HH:mm:ss"));

var userActive = {};

app.use(express.static('./public'));

var connect = mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "?8H/7Sr+tWdVr5=h",
    database: "tsicdev_db"
});

io.on('connect', function onConnect(socket) {
    console.log(`${socket.id} connected.`);

    socket.on('connected', function connected(det) {
        if (!det) {
            var det = {}
        };        
        
        console.log("Connected => ");
        console.log(det);

        if (det.id) {
            userActive[det.id] = true;
        };

        if (det.chat_code) {
            socket.join('room_' + det.chat_code);
        };
    });

    socket.on('sendMessage', function (message) {
        var messageType = "mentor_mentee_chat_threads";

        if (!message) {
            message = {};
        };
        
        console.log("sendMessage");
        console.log("from" + message.from_where);
        console.log("type" + message.type);
        console.log(message);

        if (userActive[message.receiver_id] == true) {
            message.is_read = 1;
        } else {
            message.is_read = 0;
        };

        var val = {
            chat_code: message.chat_code,
            sender_id: message.sender_id,
            receiver_id: message.receiver_id,
            message: message.message,
            from_where: message.from_where,
            receiver_is_read: message.is_read,
            created_date: moment().tz(message.time_zone).format("YYYY-MM-DD HH:mm:ss")
        };

        if (message.type == "staff") {
            messageType = "mentor_staff_chat_threads";
        };

        if (message.type == "menteestaff") {
            messageType = "mentee_staff_chat_threads";
        };

        if(!message.time_zone){
            message.time_zone = "America/New_York"
        };

        var call = "INSERT INTO " + messageType + " SET ? ";

        connect.query(call, val, function (err, result) {
            if (err) {
                console.log(err);
            } else {
                console.log("userActive[message.receiver_id]");
                console.log(userActive[message.receiver_id]);
                //console.log(userActive[message.receiver_id]);
                
                if (!userActive[message.receiver_id] && message.device_token) {
                    sendNotification(message);
                };
                //sendNotification(message);
                console.log('moment().tz(message.time_zone).format("MM-DD-YYYY HH:mm:ss")');
                console.log(moment().tz(message.time_zone).format("MM-DD-YYYY HH:mm:ss"));

                var msg = {
                    sender_id: message.sender_id,
                    sender_name: message.sender_name,
                    receiver_id: message.receiver_id,
                    receiver_name: message.receiver_name,
                    message: message.message,
                    from_where: message.from_where,
                    receiver_is_read: message.is_read,
                    created_date: moment().tz(message.time_zone).format("MM-DD-YYYY HH:mm:ss")
                };

                io.to('room_' + message.chat_code).emit('receiveMessage', msg);
            };
        });

    });

    function sendNotification(message) {
        var notificationMsg = new gcm.Message({
            data: {
                announcement_data: {
                    agency_id: message.sender_id,
                    message: message.message,
                    type: 'chat'
                }
            },
            notification: {
                title: message.sender_name,
                body: message.message,
                icon: '',
                sound: 'default',
                click_action: 'chat_activity'
            }
        });

        var regTokens = [message.device_token];

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

    //socket.on('receiveMessage', function (message) {});

    socket.on('disconnect', function (socket) {
        console.log(socket.id + " => disconnected.");
    });

    socket.on('roomExit', function (det) {
        console.log("Room Exit => " + det.id);
        if (det.id) {
            userActive[det.id] = false;
        }
    });

})
