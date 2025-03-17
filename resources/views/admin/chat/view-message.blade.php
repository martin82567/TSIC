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

                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>Messages</h3>
                    </div>
                    <div class="col-lg-6">
                        <a href="{{url('/admin/chat')}}?type={{$type}}&view_chat={{$view_chat}}" class="back-btn"><i class="fa fa-arrow-left"></i></a>
                    </div>
                </div>
                <?php if($type == 'mm' || $type == 'ms'){?>
                <div class="chat-history">
                    <ul id="direct-chat-messages">
                        <?php if(!empty($data)){  ?>
                        <?php  foreach($data as $chats){ ?>
                        <?php if($chats->from_where == 'mentor'){ ?>
                        <li class="clearfix">
                            <div class="message-data align-right">
                                <span class="message-data-time">
                                    <?php //echo $chats->admins_name; ?></span>
                                <span class="message-data-name">
                                    <?php echo get_standard_datetime_with_timezone($chats->created_date); ?>
                                    </span> <i class="fa fa-circle me"></i>
                                <span>
                                    <?php $mentor_name = DB::table('mentor')->select('firstname','middlename','lastname')->where('id',$chats->sender_id)->first();
                                    // print_r($mentor_name); die;
                                    if(!empty($mentor_name)){
                                        echo $mentor_name->firstname.' '.$mentor_name->middlename.' '.$mentor_name->lastname;
                                    }?>
                                </span>
                            </div>
                            <div class="message other-message float-right">
                                <?php echo $chats->message; ?>
                            </div>
                        </li>
                        <?php
                            }else{
                                if($chats->from_where == 'mentee'){
                                    $mentee_data = DB::table('mentee')->select('firstname','middlename','lastname')->where('id',$chats->sender_id)->first();
                                    // print_r($mentee_data); die;
                                    $receiver_name = $mentee_data->firstname.' '.$mentee_data->middlename.' '.$mentee_data->lastname;
                                }else if($chats->from_where == 'staff'){
                                    $staff_data = DB::table('admins')->where('id',$chats->sender_id)->first();
                                    $receiver_name = $staff_data->name;
                                }
                        ?>
                        <li>
                            <div class="message-data">
                                <span class="message-data-name"><i class="fa fa-circle online"></i>
                                    <?php echo $receiver_name; ?></span>
                                <span class="message-data-time">
                                    <?php echo get_standard_datetime_with_timezone($chats->created_date); ?></span>
                            </div>
                            <div class="message my-message">
                                <?php echo $chats->message; ?>
                            </div>
                        </li>
                        <?php } ?>
                        <?php } ?>
                        <?php }else{ ?>
                        <li>
                            <div class="message-data">
                                <p>No threads found</p>
                            </div>
                        </li>

                        <?php }?>
                    </ul>

                </div>
                <!-- end Mentor-Mentee && Mentor-Staff chat-history -->
                <?php }else if($type == 'menteestaff'){?>
                <div class="chat-history">
                    <ul id="direct-chat-messages">
                        <?php if(!empty($data)){  ?>
                        <?php  foreach($data as $chats){ ?>
                        <?php if($chats->from_where == 'mentee'){ ?>
                        <li class="clearfix">
                            <div class="message-data align-right">
                                <span class="message-data-time">
                                    <?php //echo $chats->admins_name; ?></span>
                                <span class="message-data-name">
                                    <?php echo get_standard_datetime($chats->created_date); ?></span> <i class="fa fa-circle me"></i>
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
                        <?php
                            }else{
                                if($chats->from_where == 'staff'){
                                    $staff_data = DB::table('admins')->where('id',$chats->sender_id)->first();
                                    $receiver_name = $staff_data->name;
                                }
                        ?>
                        <li>
                            <div class="message-data">
                                <span class="message-data-name"><i class="fa fa-circle online"></i>
                                    <?php echo $receiver_name; ?></span>
                                <span class="message-data-time">
                                    <?php echo get_standard_datetime($chats->created_date); ?></span>
                            </div>
                            <div class="message my-message">
                                <?php echo $chats->message; ?>
                            </div>
                        </li>
                        <?php } ?>
                        <?php } ?>
                        <?php }else{ ?>
                        <li>
                            <div class="message-data">
                                <p>No threads found</p>
                            </div>
                        </li>

                        <?php }?>
                    </ul>

                </div>
                <?php }?>
                <!-- end Mentee-Staff chat-history -->



            </div>

        </div>
    </div>
</div>
@endsection
