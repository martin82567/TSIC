var userType = $("#userType").val();
var userId = $("#userId").val();
var userName = $("#userName").val();
var chatType = $("#chatType").val();
var chatCode = $("#chatCode").val();
var channelId = $("#channelId").val();
var receiverId = $("#receiverId").val();
var receiverType = $("#receiverType").val();
var timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

console.log(userType);
console.log(userId);
console.log(userName);
console.log(chatType);
console.log(chatCode);
console.log(channelId);

var tc = {};

$.ajax({
    url: '/api/chat/get_access_token',
    type: 'post',
    contentType: "application/json",
    success: function (data) {
        console.log(data);
        if (data.token) {
            twilloChatInit(data.token);
        };
    },
    data: JSON.stringify({
        user_type: userType,
        user_id: userId,
        user_name: userName
    })
});

function twilloChatInit(token) {
    console.log("twilloChatInit");
    Twilio.Chat.Client.create(token).then(function (client) {
        tc.messagingClient = client;
        // console.log(tc.messagingClient);
        if (!channelId || channelId == "") {
            createChannel();
        } else {
            channelInit()
        };
    });
};

async function createChannel() {
    console.log("createChannel");
    if (chatCode && chatCode != "") {
        tc.messagingClient.createChannel({
            uniqueName: chatCode,
            friendlyName: chatCode
        }).then(function (channel) {
            channelId = channel.sid;
            channelInit();
            $.ajax({
                url: '/api/chat/channel_id_update',
                type: 'post',
                contentType: "application/json",
                success: function (data) {
                    // console.log(data);
                },
                data: JSON.stringify({
                    chat_code: chatCode,
                    channel_sid: channelId,
                    chat_type: chatType
                })
            });
        }).catch(function (err) {
            console.log(err);
        });
    } else {
        alert("Chat code not available")
    }
};

async function channelInit() {
    console.log("channelInit");

    tc.currentChannel = await tc.messagingClient.getChannelBySid(channelId);

    tc.currentChannel.join().then(function (joinedChannel) {
        console.log('Joined channel ' + joinedChannel.friendlyName);
    }).catch(function (err) {
        console.log("err");
        // console.log(err);
    });

    tc.currentChannel.on('messageAdded', function (message) {
        // console.log(message.state);
        addMessageData(message.state);
    });

    // tc.currentChannel.sendMessage("testing msg");

    tc.currentChannel.getMessages().then(function (messages) {
        var itemLength = messages.items.length;
        var lastItem = itemLength - 1;
        var someMessageIndex = messages.items[lastItem].index;

        // tc.currentChannel.updateLastReadMessageIndex(someMessageIndex).then(function (a, b) {
        //     // updated
        //     console.log("updateLastReadMessageIndex");
        //     console.log(a);
        //     console.log(b);
        // });

        for (var i = 0; i < itemLength; i++) {
            console.log("Message:-");console.log(messages.items[i]);
            if (messages.items[i].state) {
                addMessageData(messages.items[i].state)
            }
        };



        var someMessageIndex = messages.items[4].index;

        tc.currentChannel.updateLastConsumedMessageIndex(someMessageIndex).then(function () {
              // updated
              // alert("test")
        });

        tc.currentChannel.setAllMessagesConsumed();
    });

    chatInit();
};

function addMessageData(data) {
    var author = [];
    var authorName = [];
    var msg = "";
    var time;

    if (data.author) {
        author = data.author.split("_");
        if (!author[2]) {
            authorName = ""
        } else {
            authorName = author[2]
        }
    };

    if (data.body) {
        msg = data.body
    };

    if (data.timestamp) {
        if (!timeZone || userType === "admin") {
            timeZone = "America/New_York"
        };

        time = new Date(data.timestamp).toLocaleString('en-us', {
            timeZone: timeZone
        });

        var date = new Date(time);

        var time_format = formatTime(date);

        var time = addZero(date.getMonth() + 1) + "-" + addZero(date.getDate()) + "-" + date.getFullYear() + " " + time_format;

        //var time = addZero(date.getMonth() + 1) + "-" + addZero(date.getDate()) + "-" + date.getFullYear() + " " + addZero(date.getHours()) + ":" + addZero(date.getMinutes()) + ":" + addZero(date.getSeconds());


    };

    if (author[0] == userType && author[1] == userId) {
        var html = `<li>
                        <div class="message-data">
                            <span class="message-data-name"><i class="fa fa-circle online"></i></span>
                            <span class="message-data-time">` + time + `</span>
                        </div>
                        <div class="message my-message my-message-active">
                            ` + msg + `
                        </div>
                    </li>`;

        $("#direct-chat-messages").append(html);
        $('.chat-history').scrollTop(99999999);
    } else {
        var html = `<li class="clearfix">
                        <div class="message-data align-right">
                            <span class="message-data-time">` + time + `</span>
                            <span class="message-data-name">` + author[2] + `</span> 
                            <i class="fa fa-circle me"></i>
                        </div>
                        <div class="message other-message float-right">
                            ` + msg + `
                        </div>
                    </li>`

        $("#direct-chat-messages").append(html);
        $('.chat-history').scrollTop(99999999);
    };
};

function addZero(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
};

function chatInit() {
    $("#chat-form").submit(function (e) {
        // alert("test");
        e.preventDefault();
        var message = $("#chat-msg").val();

        if (message == undefined || message == "") {
            return;
        };
        tc.currentChannel.sendMessage(message);

        $.ajax({
            url: '/api/chat/send_nofication',
            type: 'post',
            contentType: "application/json",
            success: function (data) {
                // console.log(data);
            },
            data: JSON.stringify({
                "user_type":    receiverType,
                "user_id":  receiverId,
                "message":  message,
                "sender_name":  userName,
                "comes_from":   userType
            })
        });

        $("#chat-msg").val("");
    });
};

function formatTime(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();

    var ampm = hours >= 12 ? 'pm' : 'am';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0'+minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;
    return strTime;
}
