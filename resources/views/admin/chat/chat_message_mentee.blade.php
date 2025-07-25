@extends('layouts.admin')

@section('content')
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
            <div class="chat">
                <div class="chat-header clearfix">
                    <div class="chat-about">
                        <div class="chat-with">
                            <h3>Messages</h3>
                        </div>
                    </div>
                </div> <!-- end chat-header -->

                <!-- <div class="chat-history">
                    <ul id="direct-chat-messages">
                        <?php if(!empty($chat_details_arr)){  ?>
                        <?php  foreach($chat_details_arr as $chats){ ?>
                        <?php if($chats->from_where == 'mentee'){ ?>
                        <li class="clearfix">
                            <div class="message-data align-right">
                                <span class="message-data-time">
                                    <?php //echo $chats->admins_name; ?></span>
                                <span class="message-data-name">
                                    <?php echo date('m-d-Y H:i:s', strtotime($chats->created_date)); ?></span> <i class="fa fa-circle me"></i>
                                <span>
                                    <?php $mentee_name = DB::table('mentee')->select('firstname','middlename','lastname')->where('id',$chats->sender_id)->first();
                                    if(!empty($mentee_name)){
                                        echo $mentee_name->firstname.' '.$mentee_name->middlename.' '.$mentee_name->lastname;
                                    }?>
                                </span>
                            </div>
                            <div class="message other-message float-right">
                                <?php echo $chats->message; ?>
                            </div>
                        </li>
                        <?php }else{ ?>
                        <li>
                            <div class="message-data">
                                <span class="message-data-name"><i class="fa fa-circle online"></i>
                                    <?php //echo $note->admins_name; ?></span>
                                <span class="message-data-time">
                                    <?php echo date('m-d-Y H:i:s', strtotime($chats->created_date)); ?></span>
                            </div>
                            <div class="message my-message">
                                <?php echo $chats->message; ?>
                            </div>
                        </li>
                        <?php } ?>
                        <?php } ?>
                        <?php } ?>
                    </ul>

                </div> -->

                <div class="chat-history">
                    <ul id="direct-chat-messages">
                        @if(!empty($chat_details_arr))
                            @foreach($chat_details_arr as $chats)
                                @if($chats->from_where != 'mentee')
                                    <li class="clearfix">
                                        <div class="message-data align-right">
                                            <span class="message-data-time">
                                            <span class="message-data-name">
                                                {{ date('m-d-Y h:i a', strtotime($chats->created_date)) }}</span> <i class="fa fa-circle me"></i>
                                            <span>
                                                <?php 
                                                    $mentee_name = DB::table('mentee')->select('firstname','middlename','lastname')->where('id',$chats->sender_id)->first();
                                                    if(!empty($mentee_name)){
                                                        echo $mentee_name->firstname.' '.$mentee_name->middlename.' '.$mentee_name->lastname;
                                                    }
                                                ?>
                                            </span>
                                        </div>
                                        <div class="message other-message float-right">
                                            {{ $chats->message }}
                                        </div>
                                    </li>
                                @else
                                    <li>
                                        <div class="message-data">
                                            <span class="message-data-name"><i class="fa fa-circle online"></i>
                                            <span class="message-data-time">
                                                {{ date('m-d-Y h:i a', strtotime($chats->created_date)) }}</span>
                                        </div>
                                        <div class="message my-message">
                                            {{ $chats->message }}
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </div>

                <div class="box-inner">
                    <form action="" method="post" id="chat-form">

                        <div class="form-section">
                            <div class="chat-message clearfix">
                                <div class="row mb-3">
                                    <div class="col-xl-12 col-md-12">
                                        <div class="form-group">
                                            <label>Message <sup>*</sup></label>
                                            <textarea name="note" id="chat-msg" placeholder="Write some words" rows="3" required="required"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="box-footer">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                    <a href="{{url('/admin/chat?type=mentee')}}" class="btn btn-danger">Cancel</a>

                                </div>
                            </div> <!-- end chat-message -->
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</div>

<input type="hidden" id="userType" value="staff">
<input type="hidden" id="userId" value="<?php echo $staff_id; ?>">
<input type="hidden" id="userName" value="<?php echo $staff_name; ?>">
<input type="hidden" id="chatType" value="<?php echo $chat_type; ?>">
<input type="hidden" id="chatCode" value="<?php echo $chat_code; ?>">
<input type="hidden" id="channelId" value="<?php echo $channel_sid; ?>">
<input type="hidden" id="timeZone" value="<?php echo $timezone; ?>">
<input type="hidden" id="receiverId" value="<?php echo $mentee_id; ?>">
<input type="hidden" id="receiverType" value="<?php echo $receiver_type; ?>">


<script src="//cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.1/socket.io.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.1/moment.min.js"></script>
<script src="//media.twiliocdn.com/sdk/js/common/v0.1/twilio-common.min.js"></script>
<script src="//media.twiliocdn.com/sdk/js/chat/v4.0/twilio-chat.min.js"></script>

{{-- <script src="/assets/js/twilio-chat-script.js?v0.5"></script> --}}


<script>
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
            console.log("Client created successfully");
            console.log(tc.messagingClient);
            if (!channelId || channelId == "") {
                createChannel();
            } else {
                channelInit()
            };
        }).catch(function(error) {
            console.error("Failed to initialize Twilio Chat:", error);
            // Check specifically for authentication errors
            if (error.code === 20101 || error.message.includes('token')) {
                alert("Authentication failed. Please refresh the page.");
            }
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
                console.log(channelId);
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
        console.log(channelId);

        try {
            // tc.i;l kh  = await tc.messagingClient.getChannelBySid(channelId);
            tc.currentChannel = await tc.messagingClient.getChannelBySid(channelId);
            console.log("Found channel by SID");
        } catch (sidError) {
            console.log("Channel not found by SID, trying by unique name");
            
            try {
                // If not found by SID, try by unique name
                tc.currentChannel = await tc.messagingClient.getChannelByUniqueName(chatCode);
                channelId = tc.currentChannel.sid; // Update channelId with the found channel's SID
                console.log("Found channel by unique name:", channelId);

                // Update New channelId
                $.ajax({
                    url: '/api/chat/channel_id_update',
                    type: 'post',
                    contentType: "application/json",
                    data: JSON.stringify({
                        chat_code: chatCode,
                        channel_sid: channelId,
                        chat_type: chatType
                    }),
                    success: function (data) {
                        console.log(data);
                    }
                });

            } catch (nameError) {
                console.error("Channel not found by SID or unique name:", nameError);
                // If neither works, create a new channel
                await createChannel();
                return;
            }
        }

        // Proceed with joining the channel
        try {
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

            // tc.currentChannel.getMessages().then(function (messages) {
            //     var itemLength = messages.items.length;

            //     for (var i = 0; i < itemLength; i++) {
            //         console.log("Message:-");console.log(messages.items[i]);
            //         if (messages.items[i].state) {
            //             addMessageData(messages.items[i].state)
            //         }
            //     };

            //     // Update last consumed message (only if messages exist)
            //     if (itemLength > 0) {
            //         var lastItem = itemLength - 1;
            //         var someMessageIndex = messages.items[lastItem].index;

            //         tc.currentChannel.updateLastConsumedMessageIndex(someMessageIndex).then(function () {
                        
            //         });
            //     }

            //     tc.currentChannel.setAllMessagesConsumed();
            // });
            
            $('.chat-history').scrollTop(99999999);

            chatInit();

        } catch (channelError) {
            console.error("Failed to get channel:", channelError);
            // console.log("Channel not found, creating new one.");
            // await createChannel();

            if (channelError.code === 50403) {
                console.log("User is already a member of the channel");
                // Continue with initialization even if already joined
            } else {
                throw joinError;
            }

            if (channelError.body && channelError.body.code === 50404) {
                console.log("Channel not found, creating new one");
                await createChannel();
            }
        }
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
        console.log(":: Chat Init ::");
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

</script>

@endsection
