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

<script src="/assets/js/twilio-chat-script.js?v0.5"></script>

@endsection
