@extends('layouts.apps')
@section('content')
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css">
<!-- <p>Mentor</p> -->
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
            <div class="chat chat-web">
                <div class="chat-header clearfix">
                    <div class="chat-about">
                        <div class="chat-with">
                            <h3>Messages</h3>
                        </div>
                    </div>
                </div> <!-- end chat-header -->

                <div class="chat-history">
                    <ul id="direct-chat-messages">
                        <?php if(!empty($chat_details_arr)){  ?>
                        <?php  foreach($chat_details_arr as $chats){ ?>
                        <?php if($chats->from_where != 'mentor'){

                            ?>
                        <li class="clearfix">
                            <div class="message-data align-right">
                                <span class="message-data-time">
                                    <?php //echo $chats->admins_name; ?></span>
                                <span class="message-data-name">
                                    <?php echo date('m-d-Y H:i:s', strtotime($chats->created_date)); ?></span> <i class="fa fa-circle me"></i>
                                <span>
                                    <?php echo $sender_name; ?>
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

                </div> <!-- end chat-history -->

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
                                    <?php if($type == 'st'){?>
                                    <a href="{{url('/mentor/chat/userlist?type=st')}}" class="btn btn-danger">Cancel</a>
                                    <?php }else{?>
                                    <a href="{{url('/mentor/chat/userlist?type=mm')}}" class="btn btn-danger">Cancel</a>
                                    <?php }?>

                                </div>
                            </div> <!-- end chat-message -->
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</div>



<script src="//cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.1/socket.io.js"></script>

<script type="application/javascript">
    //$('#direct-chat-messages').scrollTop(99999999);
    $('.chat-history').scrollTop(99999999);

    const socket = io.connect("{{ env('APP_URL') }}:3700/");

    var det = {
        chat_code: "<?php echo $code; ?>",
        id: "<?php echo $mentor_id; ?>"
    };

    socket.on('connect', function() {
        socket.emit('connected', det);
    });


    function addZero(i) {
        if (i < 10) {
            i = "0" + i;
        }
        return i;
    };

    socket.on('receiveMessage', function onMsg(message) {

        if (message.sender_id == <?php echo $mentor_id; ?>) {
            return;
        };

        // alert(message.receiver_is_read);

        var time = new Date().toLocaleString('en-us', {
            timeZone: 'Asia/Bangkok'
        });
        var date = new Date(time);
        var timeView = addZero(date.getMonth() + 1) + "-" + addZero(date.getDate()) + "-" + date.getFullYear() + " " + addZero(date.getHours()) + ":" + addZero(date.getMinutes()) + ":" + addZero(date.getSeconds());



        var html = `<li class="clearfix">
                            <div class="message-data align-right">
                                <span class="message-data-time">` + timeView + `</span>
                                <span class="message-data-name">
                                    <?php echo $sender_name; ?>
                                </span>
                                <i class="fa fa-circle me"></i>
                            </div>
                            <div class="message other-message float-right">
                                ` + message.message + `
                            </div>
                        </li>`
        $("#direct-chat-messages").append(html);
        //$('#direct-chat-messages').scrollTop(99999999);
        $('.chat-history').scrollTop(99999999);
    });

    // socket.on('messagereceivedackback', function onMessage(message) {
    //     console.log("messagereceivedackback");
    // });

    socket.on('messagereceivedackback', function onMsg(message){
        if ($(".my-message").hasClass("my-message-active")) {
            $(".my-message").removeClass("my-message-active")
        }
    });

    socket.on('new_participant_joined', function onMsg(message) {
        if(message.id != det.id){
            if ($(".my-message").hasClass("my-message-active")) {
                $(".my-message").removeClass("my-message-active")
            }
        }
    });



    $(function() {

        console.log("<?php echo $timezone; ?>");

        $("#chat-form").submit(function(e) {
            e.preventDefault();

            if ($("#chat-msg").val() == undefined || $("#chat-msg").val() == "") {
                return;
            };

            var timeZoneDet = '<?php echo $timezone; ?>';
            if(!timeZoneDet) {
                timeZoneDet = "America/New_York"
            };

            var time = new Date().toLocaleString('en-us', {
                timeZone: timeZoneDet
            });
            var date = new Date(time);
            var timeView = addZero(date.getMonth() + 1) + "-" + addZero(date.getDate()) + "-" + date.getFullYear() + " " + addZero(date.getHours()) + ":" + addZero(date.getMinutes()) + ":" + addZero(date.getSeconds());

            var chatMsg = {
                chat_code: "<?php echo $code; ?>",
                type: "<?php echo $socket_chat_type; ?>",
                from_where: "<?php echo $from_where; ?>",
                sender_id: "<?php echo $mentor_id; ?>",
                sender_name: "<?php echo $mentor_name; ?>",
                receiver_id: "<?php echo $sender_id; ?>",
                receiver_name: "<?php echo $sender_name; ?>",
                time: timeView,
                message: $("#chat-msg").val(),
                device_token: "",
                device_type: "",
                time_zone: "<?php echo $timezone; ?>"
            }

            var html = `<li>
                            <div class="message-data">
                                <span class="message-data-name"><i class="fa fa-circle online"></i>
                                    <?php //echo $note->admins_name; ?></span>
                                <span class="message-data-time">` + timeView + `</span>
                            </div>
                            <div class="message my-message my-message-active">
                                ` + $("#chat-msg").val() + `
                            </div>
                        </li>`;

            socket.emit('sendMessage', chatMsg);

            // console.log(chatMsg);


            $("#direct-chat-messages").append(html);
            //$('#direct-chat-messages').scrollTop(99999999);
            $('.chat-history').scrollTop(99999999);

            $("#chat-msg").val("");

        });
    });

</script>

@endsection
