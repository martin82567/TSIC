<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAKE STOCK IN CHILDREN</title>
    <!--Css-->
    <link rel="stylesheet" type="text/css" href="<?php echo url('assets/'); ?>/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo url('assets/'); ?>/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo url('assets/'); ?>/css/owl.carousel.min.css">
    <!--Custom CSS-->
    <link rel="stylesheet" href="<?php echo url('assets/'); ?>/css/style.css" type="text/css">
    <link rel="stylesheet" href="<?php echo url('assets/'); ?>/css/media.css" type="text/css">
    <link rel="stylesheet" href="<?php echo url('assets/'); ?>/css/select2.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?php echo url('assets/'); ?>/css/sweetalert.css">
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" /> -->
    <!--jQuery-->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/jquery-input-file-text.js"></script>

    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/jquery.mask.js"></script>
    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/moment.js"></script>
    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/select2.full.js"></script>

</head>
<style type="text/css">
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #3d8dbd;
        border: 1px solid #367fa9;
        color: #FFFFFF;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fe0000;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover{
        color: #fe0000;
    }
</style>

<body class="<?php if(Auth::user()->type == 3){ ?>user-dashboard <?php } ?>">

    <?php
    $keyword_notification_access = 0;
    $view_chat_access = 0;
    $video_chat_access = 0;
    if(Auth::user()->type == 1){
        $keyword_notification_access = 1;
        $video_chat_access = 1;
        $view_chat_access = 1;
    }else if(Auth::user()->type == 2){
        $keyword_notification_access = 1;
        $view_chat_access = 1;
        $video_chat_access = 1;
    }else{
        if(!empty(Auth::user()->is_allow_keyword_notification)){
            $keyword_notification_access = 1;
            if(Auth::user()->parent_id != 1){
                $view_chat_access = 1;
            }

        }

        if(Auth::user()->parent_id == 1){
            $view_chat_access = 1;
        }
        $video_chat_access = 1;
    }
    $chat_unread_notification = 0;
    if(Auth::user()->type == 1){
        $chat_unread = DB::table('keyword_chat_notification')->orderBy('id','desc')->get()->toarray();
        $chat_unread_notification = DB::table('keyword_chat_notification')->where('is_admin_read',0)->count();
    }else if(Auth::user()->type == 2){
        $chat_unread = DB::table('keyword_chat_notification')->where('affiliate_id', Auth::user()->id)->orderBy('id','desc')->get()->toarray();
        $chat_unread_notification = DB::table('keyword_chat_notification')->where('affiliate_id', Auth::user()->id)->where('is_affiliate_read',0)->count();
    }else{
        if(Auth::user()->parent_id != 1){
            $chat_unread = DB::table('keyword_chat_notification')->where('staff_id', Auth::user()->id)->orderBy('id','desc')->get()->toarray();
            $chat_unread_notification = DB::table('keyword_chat_notification')->where('staff_id', Auth::user()->id)->where('is_staff_read',0)->count();
        }else{
            $chat_unread = DB::table('keyword_chat_notification')->where('super_staff_id', Auth::user()->id)->orderBy('id','desc')->get()->toarray();
            $chat_unread_notification = DB::table('keyword_chat_notification')->where('super_staff_id', Auth::user()->id)->where('is_super_staff_read',0)->count();
        }

    }
    $system_messaging = false;
    $message_center = false;
    if(Auth::user()->type == 1){
        $system_messaging = true;
        $message_center = true;
    }else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
        $system_messaging = true;
    }else if(Auth::user()->type == 2){
        $message_center = true;
    }
    ?>
    <header class="clearfix">
        <div class="db-header">
            <div class="db-logo">
                <img src="<?php echo url('assets/'); ?>/images/logo.png" alt="">
            </div>
            <div class="db-header-right">
                <div class="inner clearfix">
                    <h2><?php if(Auth::user()->type == 1){ ?>
                        Super Admin
                        <?php }else if(Auth::user()->type == 2){ ?>
                        Affiliate Admin
                        <?php }else{
                            if(Auth::user()->parent_id != 1){
                                if(!empty(Auth::user()->is_allow_keyword_notification)){
                                    echo 'Lead Staff';
                                }else{
                                    echo 'General Staff';
                                }
                            }else{
                                echo 'Super Staff';
                            }
                        }
                        ?></h2>
                    <div class="h-user-top">
                    <div class="h-user">
                        <span>{{ Auth::user()->name }}</span>
                        <?php if(!empty(Auth::user()->profile_pic)){?>
                        <img src="<?php echo config('app.aws_url'); ?>agency_pic/<?php echo Auth::user()->profile_pic; ?>" alt="">
                        <?php }else{?>
                        <img src="<?php echo url('assets/'); ?>/images/logo.png" alt="">
                        <?php }?>
                    </div>
                    <?php if(!empty($keyword_notification_access)){?>
                    <div class="h-notification">
                        <a href="javascript:void(0);">
                            <i class="fa fa-bell"></i>
                            <?php if(!empty($chat_unread_notification)){?>
                            <span class="notification-badge">{{$chat_unread_notification}}</span>
                            <?php }?>
                        </a>
                        <div class="db-user-menu">
                            <ul>
                                <?php if(!empty($chat_unread)){
                                    foreach($chat_unread as $cu){?>
                                <li>
                                    <a href="{{url('/admin/chat/keyword-notification-unreviewed')}}">A keyword specific chat has been identified at <?php echo date('M d, Y, H:i', strtotime($cu->created_at));?> </a>
                                </li>
                                <?php }}?>
                            </ul>
                        </div>
                    </div>
                    <?php }?>

                    <div class="h-logout">
                        <a href="{{ route('admin.logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            <img src="<?php echo url('assets/'); ?>/icon/logout.png" alt="">
                        </a>
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </header>
    <!--end header-->

    <?php
        if(Auth::user()->type == 3){
            $user_access = DB::table('user_access')->where('user_id',Auth::user()->id)->first();
            $access_mentor = $user_access->access_mentor;
            $access_victim = $user_access->access_victim;
            $acces_resource = $user_access->acces_resource;
            $access_job = $user_access->access_job;
            $access_goal_task_challenge = $user_access->access_goal_task_challenge;
            $access_e_learning = $user_access->access_e_learning;
            $access_meeting = $user_access->access_meeting;
            $access_agency = $user_access->access_agency;

        }else{
            $access_mentor = 1;
            $access_victim = 1;
            $acces_resource = 1;
            $access_job = 1;
            $access_goal_task_challenge = 1;
            $access_e_learning = 1;
            $access_meeting = 1;
            $access_agency = 0;
        }


        $unread_mentor_chat = total_unread_chat_message_staff('mentor','mentor_staff_chat_threads',Auth::user()->id);
        $unread_mentee_chat = total_unread_chat_message_staff('mentee','mentee_staff_chat_threads',Auth::user()->id);

    ?>

    <main class="db-main">
        <div class="container-fluid">
            <div class="row">
                <aside class="db-sidebar" id="db-menu-toggle">
                    <div class="side-content">
                        <div class="db-s-menu">
                            <ul>
                            	<?php
                            	$current_route =  Route::currentRouteName();
                            	$uri_segment = Request::segment(2);
                            	$request = Request::all();
                                // $request['menu_type'];
                                $menu_type = !empty($request['menu_type'])?$request['menu_type']:'';
                                // echo $current_route;
                                // echo '<pre>';
                                // echo $menu_type;
                                ?>
                                <li class="<?php if($uri_segment == ''){?>active<?php }?>">
                                    <a href="<?php echo url('/'); ?>/admin"><span>Dashboard</span></a>
                                </li>
                                <li class="<?php if($uri_segment == 'profile'){?>active<?php }?>">
                                    <a href="<?php echo url('/'); ?>/admin/profile"> <span>My Profile</span></a>
                                </li>
                                <?php if($system_messaging){?>
                                <li class="<?php if($uri_segment == 'system-messaging'){?>active<?php }?>">
                                    <a href="<?php echo url('/'); ?>/admin/system-messaging/list"> <span>System Messaging</span></a>
                                </li>
                                <?php }?>
                                <?php if($message_center){?>
                                <li class="<?php if($uri_segment == 'message-center'){?>active<?php }?>">
                                    <a href="<?php echo url('/'); ?>/admin/message-center/list"> <span>Announcements</span></a>
                                </li>
                                <?php }?>
                                <?php if(Auth::user()->type == 1 || $access_agency == 1){?>

                                    <li class="">
                                        <a> <span>Affiliate</span></a>
                                        <ul class="sub-menu" style="display:block !important">
                                            <li class="<?php if($current_route == 'admin.agency' ||  $menu_type == 'agency'){?>active<?php }?>">
                                                <a href="<?php echo url('/'); ?>/admin/agency"><span>Active Affiliate</span></a>
                                            </li>
                                            <li class="<?php if($uri_segment == 'inactiveagencies'){?>active<?php }?>">
                                                <a href="<?php echo url('/'); ?>/admin/inactiveagencies"> <span>Inactive Affiliate</span></a>
                                            </li>
                                        </ul>
                                    </li>

                                <?php }?>
                                <?php if(Auth::user()->type != 3){?>

                                    <li class="">
                                        <a><span>Staff</span></a>
                                        <ul class="sub-menu" style="display:block !important;">
                                            <li class="<?php if($current_route == 'admin.user' ||  $menu_type == 'user'){?>active<?php }?>">
                                                <a href="<?php echo url('/'); ?>/admin/user"><span>Active Staff</span></a>
                                            </li>
                                            <li class="<?php if($uri_segment == 'inactiveuser'){?>active<?php }?>">
                                                <a href="<?php echo url('/'); ?>/admin/inactiveuser"><span>Inactive Staff</span></a>
                                            </li>
                                        </ul>
                                    </li>

                                <?php }?>
                                <?php
                                $access_school = 0;
                                if(Auth::user()->type == 1){
                                    $access_school = 1;
                                }else if(Auth::user()->type == 2){
                                    $access_school = 1;
                                }else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
                                    $access_school = 1;
                                }

                                if(!empty($access_school)){
                                ?>
                                <li class="">
                                    <a> <span>School</span></a>
                                    <ul class="sub-menu" style="display:block !important">
                                        <li class="<?php if($uri_segment == 'active-school' || $uri_segment == 'school'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/active-school"><span>Active School</span></a>
                                        </li>
                                        <li class="<?php if($uri_segment == 'inactive-school'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/inactive-school"> <span>Inactive School</span></a>
                                        </li>
                                    </ul>
                                </li>
                                <?php }?>

                                <?php if(!empty($access_mentor)){?>
                                <li class="">
                                    <a> <span>Mentor</span></a>
                                    <ul class="sub-menu" style="display:block !important">
                                        <li class="<?php if($uri_segment == 'mentor'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/mentor"><span>Active Mentor</span></a>
                                        </li>
                                        <li class="<?php if($uri_segment == 'inactivementor'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/inactivementor"> <span>Inactive Mentor</span></a>
                                        </li>
                                    </ul>
                                </li>

                                <?php }?>
                                <?php if(!empty($access_victim)){?>
                                <li class="">
                                    <a> <span>Mentee</span></a>
                                    <ul class="sub-menu" style="display:block !important">
                                        <li class="<?php if($uri_segment == 'mentee'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/mentee"><span>Active Mentee</span></a>
                                        </li>
                                        <li class="<?php if($uri_segment == 'inactivementee'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/inactivementee"> <span>Inactive Mentee</span></a>
                                        </li>
                                    </ul>
                                </li>

                                <?php }?>
                                <?php if(!empty($access_meeting)){?>
                                <li class="<?php if(Request::segment(3) == 'meeting'){?>active<?php }?>">
                                    <a href="<?php echo url('/'); ?>/admin/agency/meeting"> <span>Session</span></a>
                                </li>
                                <?php }?>

                                <?php if(!empty($video_chat_access)){?>
                                <li class="<?php if($uri_segment == 'videochat'){?>active<?php }?>">
                                    <a href="<?php echo url('/'); ?>/admin/videochat/list"> <span>Video Chat</span></a>
                                </li>
                                <?php }?>

                                <?php if(Auth::user()->type == 1){?>
                                <li class="<?php if(Request::segment(2) == 'keyword'){?>active<?php }?>">
                                    <a href="<?php echo url('/'); ?>/admin/keyword/list"> <span>Keywords</span></a>
                                </li>
                                <?php }?>

                                <?php if(Auth::user()->type == 3){?>
                                <?php if(Auth::user()->parent_id != 1){?>
                                <li class="">
                                    <a> <span>Chats</span></a>
                                    <ul class="sub-menu" style="display:block !important">
                                        <li class="<?php if(Request::get('type') == 'mentor'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/chat?type=mentor&view_chat=0">
                                                <span>Mentor</span>
                                                <?php if(!empty($unread_mentor_chat)){?>
                                                <span class="badge unread-chat-count "><?php echo $unread_mentor_chat; ?></span>
                                                <?php }?>
                                            </a>
                                        </li>
                                        <li class="<?php if(Request::get('type') == 'mentee'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/chat?type=mentee&view_chat=0">
                                                <span>Mentee</span>
                                                <?php if(!empty($unread_mentee_chat)){?>
                                                <span class="badge unread-chat-count "><?php echo $unread_mentee_chat; ?></span>
                                                <?php }?>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <?php } }?>
                                <?php if(!empty($view_chat_access)){?>
                                <li class="">
                                    <a> <span>View Chats</span></a>
                                    <ul class="sub-menu" style="display:block !important">
                                        <li class="<?php if(Request::get('type') == 'mm'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/chat?type=mm&view_chat=1"><span>Mentor-Mentee</span></a>
                                        </li>
                                        <li class="<?php if(Request::get('type') == 'ms'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/chat?type=ms&view_chat=1"> <span>Mentor-Staff</span></a>
                                        </li>
                                        <li class="<?php if(Request::get('type') == 'menteestaff'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/chat?type=menteestaff&view_chat=1"> <span>Mentee-Staff</span></a>
                                        </li>
                                    </ul>
                                </li>
                                <?php }?>

                                <?php if(!empty($keyword_notification_access)){?>

                                <li class="">
                                    <a><span>Chat Keyword Notification </span></a>
                                    <ul class="sub-menu" style="display:block !important;">
                                        <li class="<?php if(Request::segment(3) == 'keyword-notification-unreviewed'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/chat/keyword-notification-unreviewed"> <span>Unreviewed</span></a>
                                        </li>
                                        <li class="<?php if(Request::segment(3) == 'keyword-notification-reviewed'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/chat/keyword-notification-reviewed"> <span>Archived</span></a>
                                        </li>
                                    </ul>
                                </li>


                                <?php }?>



                                <?php if(!empty($access_goal_task_challenge)){?>
                                <li class="">
                                    <a><span>Goal / Assignment </span></a>
                                    <ul class="sub-menu" style="display:block !important;">
                                        <li class="<?php if($uri_segment == 'goaltask'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/goaltask"> <span>Active Goal / Assignment </span></a>
                                        </li>
                                        <li class="<?php if($uri_segment == 'inactivegoaltask'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/inactivegoaltask"> <span>Inactive Goal / Assignment </span></a>
                                        </li>
                                    </ul>
                                </li>
                                <?php }?>

                                <?php if(!empty($access_e_learning)){?>
                                <li class="">
                                    <a> <span>Resource</span></a>
                                    <ul class="sub-menu" style="display:block !important;">
                                        <li class="<?php if($uri_segment == 'e_learning'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/e_learning"> <span>Active Resource</span></a>
                                        </li>
                                        <li class="<?php if($uri_segment == 'inactiveelearning'){?>active<?php }?>">
                                            <a href="<?php echo url('/'); ?>/admin/inactiveelearning"><span>Inactive Resource</span></a>
                                        </li>
                                    </ul>
                                </li>
                                <?php }?>
                                <?php if(Auth::user()->type == 1){?>
                                <li class="<?php if($uri_segment == 'settings'){?>active<?php }?>">
                                    <a href="<?php echo url('/'); ?>/admin/settings"> <span>Settings</span></a>
                                </li>
                                <?php }?>
                            </ul>
                        </div>
                    </div>
                    <div class="overlay db-menu-toggle-btn"></div>
                </aside>

                <section class="db-container">

                    <div class="db-menu-toggle">
                        <button type="button" class="db-menu-toggle-btn"><i class="fa fa-bars"></i></button>
                    </div>
                    @yield('content')

                </section>

            </div>
        </div>
        <div class="db-footer">
            <div class="db-f-inr">
                <p>Copyright © 1995-<?php echo date("Y"); ?> <?php echo env('APP_COMPANY_NAME');?>.</p>
            </div>
        </div>
    </main>

    <!--start footer-->
    <footer></footer>
    <!--end footer-->

    <!--Js-->

    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/owl.carousel.min.js"></script>
    <!-- <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/fullcalendar.min.js"></script> -->

    <!--Custom JS-->
    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/script.js"></script>
    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/sweetalert.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.11.1/basic/ckeditor.js"></script>




    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script> -->
    <script type="text/javascript">
    function getpoint(staff_id,points,tgc_id){
        $('#t_id').val(staff_id);
        $('#point').val(points);
        $('#tgc_id').val(tgc_id);

     }
     function getmaxpoint(){
      var points= $('#point').val();
      var tgc_id= $('#tgc_id').val();
      var t_type= $('#t_type').val();
      var staff_id= $('#t_id').val();

      $.ajax({
            type:'POST',
            url:"{{route('admin.maxpoint')}}",
            data:{"_token": "{{ csrf_token() }}",
                  "points": points,
                  "tgc_id": tgc_id,
                  "t_type": t_type,
                  "staff_id": staff_id,
                },
            success:function(result){
                console.log(result);
                if(result['points']==0){
                    $('#point_msg').html('Given points can not be more than original points').show();
                    $( "#point" ).val('');
                }else{
                    $('#point').val(result['points']);
                    $('#point_msg').hide();
                    $('#taskpoint').submit();
                    $('#modal_point').modal('toggle');
                }

            }
        });
     }
    </script>
    <script>
        function showgoaltaskdiv(value){
            $('.db-hider').hide();
            $('#'+value).show();
        }
        $( document ).ready(function() {
            $('.selectboxmulti').select2();
        });

        function assign_mentee(val){
            var form_data = $('#form_id_'+val).serializeArray();
            $('#assign_goal_table_'+val).html('');
            if(form_data){
                $.ajax({
                    url: '<?php echo route("admin.assign_mentee"); ?>',
                    cache: false,
                    method:'POST',
                    data:form_data,
                    success: function(data){
                        // $('#assign_'+val).modal('toggle');
                        swal("Mentee assign successfully.",'', "success");
                        console.log(data);
                        $('#assign_goal_table_'+val).html(data);
                    }
                });
            }
        }

        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });

        function is_url(field)
        {
            var str = $('#'+field).val();
            regexp =  /^(?:(?:https?|ftp):\/\/)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/\S*)?$/;
            if (regexp.test(str))
            {

            }
            else
            {
                $('#'+field).val('');
            }
        }

        $('#is_active').click(function() {
            if($(this).is(':checked')){
                $('#check_title').text('Active');
                // $(this).val(1);
            } else {

                $('#check_title').text('Inactive');
                // $(this).val(0);
            }

        });

        $(".h-notification a").click(function () {
            $(".db-user-menu").slideToggle();

        });
    </script>
    <style>
        .autopilot {
            position: fixed;
            bottom: 20px;
            right: 100px;
            background: #00b0aa;
            text-align: center;
            display: block;
            overflow: hidden;
            border-radius:5px;
        }
        .autopilot-btn{
            line-height: 50px;
            width: 50px;
            height: 50px;
            display: block;
            border-radius: 5px;
            color: #fff;
            font-size: 27px;
        }
    </style>
    <script>
        function showchat(){
            jQuery("#chat_div").toggle();

        }
    </script>
    <div class="autopilot">
        <a class="autopilot-btn" onclick="showchat();" ><i class="fa fa-comments-o" aria-hidden="true"></i></a>
        <div id="chat_div" class="chat-div" style="display:none;">
            <iframe src="<?php echo config('app.twilio_autopilot_chaturl');?>/chat/?id=affiliate_<?php echo Auth::user()->id; ?>" style="width:500px;height:500px;"></iframe>
        </div>
    </div>
</body>

</html>
