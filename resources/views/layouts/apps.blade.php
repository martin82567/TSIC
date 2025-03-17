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
    <link rel="stylesheet" href="<?php echo url('assets/'); ?>/css/layout.css" type="text/css">
    <link rel="stylesheet" href="<?php echo url('assets/'); ?>/css/media.css" type="text/css">
    <link rel="stylesheet" href="<?php echo url('assets/'); ?>/css/select2.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?php echo url('assets/'); ?>/css/sweetalert.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,500;0,600;0,700;1,500;1,600&display=swap" rel="stylesheet">
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" /> -->
    <!--jQuery-->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/jquery-input-file-text.js"></script>    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/moment.js"></script>
    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/select2.full.js"></script>

    <script src="https://media.twiliocdn.com/sdk/js/chat/v4.0/twilio-chat.min.js"></script>
    <script src="https://source.zoom.us/videosdk/zoom-video-1.10.7.min.js"></script>

    <!-- SOCKET -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.1/socket.io.js"></script>

</head>
<style type="text/css">
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #3D8DBD;
        border: 1px solid #367FA9;
        color: #FFFFFF;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #FE0000;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover{
        color: #FE0000;
    }
</style>
<?php
    $pending_goals = "";
    $completed_goals = "";
    $pending_assignments = "";
    $completed_assignments = "";
    $upload_report_url = "";
    $chat_mm_label = "";
    $update_password_url = "";

    if(Auth::guard('mentor')->check()){
        $logged_in = "Mentor";
        $logout_url = route('logout');
        $home_url = "/mentor";
        $scheduling_session_url = "/mentor/meeting/list?type=requested&view=calendar";
        $confirmed_session_url = "/mentor/meeting/list?type=upcoming&view=calendar";
        $completed_session_url = "/mentor/meeting/list?type=past&view=calendar";
        $chat_staff_list_url = "/mentor/chat/userlist?type=st";
        $chat_mm_list_url = "/mentor/chat/userlist?type=mm";
        $resources = "/mentor/resources";
        $message_center = "/mentor/message_center";
        $chat_mm_label = "Mentee";
        $update_password_url = "/mentor/change_password";
    }else if(Auth::guard('mentee')->check()){
        $logged_in = "Mentee";
        $logout_url = route('logout');
        $home_url = "/mentee";
        $scheduling_session_url = "/mentee/meeting/list?type=requested&view=calendar";
        $confirmed_session_url = "/mentee/meeting/list?type=upcoming&view=calendar";
        $completed_session_url = "/mentee/meeting/list?type=past&view=calendar";
        $chat_staff_list_url = "/mentee/chat/userlist?type=st";
        $chat_mm_list_url = "/mentee/chat/userlist?type=mm";
        $resources = "/mentee/resources";
        $message_center = "/mentee/message_center";
        $chat_mm_label = "Mentor";
        $pending_goals = "/mentee/my_goals?type=pending";
        $completed_goals = "/mentee/my_goals?type=completed";
        $pending_assignments = "/mentee/my_assignments?type=pending";
        $completed_assignments = "/mentee/my_assignments?type=completed";
        $upload_report_url = "/mentee/upload_report";
        $chat_mm_label = "Mentor";
        $update_password_url = "/mentee/change_password";
    }
?>
    <body class="user-dashboard appuser-dashboard">
    <!--end header-->
    <!--New Header-->
    <header class="ast-custom-header" itemscope="itemscope" itemtype="https://schema.org/WPHeader">
        <div class="astra-advanced-hook-46">
            <div class="fl-builder-content fl-builder-content-46 fl-builder-global-templates-locked" data-post-id="46">
                <div class="fl-row fl-row-full-width fl-row-bg-color fl-node-5fdcf1698ea6b tar" data-node="5fdcf1698ea6b">
                    <div class="fl-row-content-wrap">
                        <div class="fl-row-content fl-row-full-width fl-node-content">
                            <div class="fl-col-group fl-node-5fdcf1698ea6f" data-node="5fdcf1698ea6f">
                                <div class="fl-module fl-module-search fl-node-61080e800c32a fl-visible-desktop-medium search-inline" data-node="61080e800c32a">
                                    <div class="fl-module-content fl-node-content">
                                        <div class="fl-search-form fl-search-form-inline fl-search-form-width-full">
                                            <div class="fl-search-form-wrap">
                                                <div class="fl-search-form-fields">
                                                    <div class="fl-search-form-input-wrap">
                                                        <form role="search" aria-label="Search form" method="get" action="https://www.takestockinchildren.org/" id="search-form">
                                                            <div class="fl-form-field">
                                                                <input type="search" aria-label="Search input" class="fl-search-text" placeholder="Search..." value="" name="s">

                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="fl-button-wrap fl-button-width-auto fl-button-center fl-button-has-icon">
                                                        <a href="#" target="_self" class="fl-button" role="button" id="search-submit">
                                                        <i class="fa fa-search" aria-hidden="true"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="fl-col fl-node-5fdcf1698ea71" data-node="5fdcf1698ea71">
                                    <div class="fl-col-content fl-node-content">
                                        <div class="fl-module fl-module-button fl-node-5fdcf1698ea72 donate btn" data-node="5fdcf1698ea72">
                                            <div class="fl-module-content fl-node-content">
                                                <div class="fl-button-wrap fl-button-width-auto fl-button-right">
                                                    <a href="https://takestockinchildren.networkforgood.com/projects/108012-take-stock-in-children-of-florida" target="_blank" class="fl-button" role="button" rel="noopener">
                                                        <span class="fl-button-text">Donate Now</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fl-row fl-row-full-width fl-row-bg-none fl-node-5fdcf1698ea73" data-node="5fdcf1698ea73">
                    <div class="fl-row-content-wrap">
                        <div class="fl-row-content fl-row-full-width fl-node-content" style="position: relative;">
                            <div class="fl-col-group fl-node-5fdcf1698ea74 fl-col-group-equal-height fl-col-group-align-center" data-node="5fdcf1698ea74">
                                <div class="fl-col fl-node-5fdcf1698ea75 fl-col-small" data-node="5fdcf1698ea75">
                                    <div class="fl-col-content fl-node-content">
                                        <div class="fl-module fl-module-photo fl-node-5fdcf1698ea77" data-node="5fdcf1698ea77">
                                            <div class="fl-module-content fl-node-content">
                                                <div class="fl-photo fl-photo-align-left" itemscope="" itemtype="https://schema.org/ImageObject">
                                                    <div class="fl-photo-content fl-photo-img-png">
                                                        <a href="https://www.takestockinchildren.org/" target="_self" itemprop="url">
                                                        <img loading="lazy" class="fl-photo-img wp-image-47" src="https://www.takestockinchildren.org/wp-content/uploads/2020/12/take-stock-in-children-logo@2x.png" alt="take-stock-in-children-logo@2x" itemprop="image" title="take-stock-in-children-logo@2x" srcset="https://www.takestockinchildren.org/wp-content/uploads/2020/12/take-stock-in-children-logo@2x.png 398w, https://www.takestockinchildren.org/wp-content/uploads/2020/12/take-stock-in-children-logo@2x-300x184.png 300w" sizes="(max-width: 398px) 100vw, 398px" width="398" height="244">
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="fl-col fl-node-5fdcf1698ea76" data-node="5fdcf1698ea76">
                                    <div class="fl-col-content fl-node-content">
                                        <div class="fl-module fl-module-menu fl-node-5fdcf1698ea78" data-node="5fdcf1698ea78">
                                            <div class="fl-module-content fl-node-content">
                                                <div class="fl-menu fl-menu-responsive-toggle-mobile">
                                                    <button class="fl-menu-mobile-toggle hamburger" aria-label="Menu"><span class="svg-container">
                                                        <svg version="1.1" class="hamburger-menu" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512">
                                                        <rect class="fl-hamburger-menu-top" width="512" height="102"></rect>
                                                        <rect class="fl-hamburger-menu-middle" y="205" width="512" height="102"></rect>
                                                        <rect class="fl-hamburger-menu-bottom" y="410" width="512" height="102"></rect>
                                                        </svg></span>
                                                    </button>
                                                    <div class="fl-clear"></div>
                                                    <nav aria-label="Menu" itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement">
                                                        <ul id="menu-main-menu" class="menu fl-menu-horizontal fl-toggle-none">
                                                            <li id="menu-item-14" class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item"><a href="https://www.takestockinchildren.org/who-we-are/">Who We Are</a></li>
                                                            <li id="menu-item-275" class="menu-item menu-item-type-post_type menu-item-object-page"><a href="https://www.takestockinchildren.org/programs/">Programs</a></li>
                                                            <li id="menu-item-17" class="menu-item menu-item-type-custom menu-item-object-custom"><a href="https://www.takestockinchildren.org/students/">Students</a></li>
                                                            <li id="menu-item-18" class="menu-item menu-item-type-custom menu-item-object-custom"><a href="https://www.takestockinchildren.org/mentors/">Mentors</a></li>
                                                            <li id="menu-item-19" class="menu-item menu-item-type-custom menu-item-object-custom"><a href="https://www.takestockinchildren.org/news-events/">News &amp; Events</a></li>
                                                            <li id="menu-item-16" class="menu-item menu-item-type-custom menu-item-object-custom"><a href="https://www.takestockinchildren.org/contact/">Contact</a></li>
                                                        </ul>
                                                    </nav>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uabb-js-breakpoint" style="display: none;"></div>
        </div>
    </header>
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
                                $type = !empty($request['type'])?$request['type']:'';
                                $view = !empty($request['view'])?$request['view']:'';

                                ?>
                                <li class="<?php if($uri_segment == ''){?>active<?php }?>">
                                    <a href="<?php echo url('/'); ?>{{$home_url}}"><span>Dashboard</span></a>
                                </li>
                                <li class="">
                                    <a> <span>Session Management</span></a>
                                    <ul class="sub-menu" style="display:block !important">
                                        <li class="<?php if($uri_segment == 'meeting' && $type == 'requested' && ($view == 'calendar' ? $view == 'calendar' : $view == 'list')){?>active<?php }?>">
                                            <a href="{{$scheduling_session_url}}"><span>Scheduling</span></a>
                                        </li>
                                        <li class="<?php if($uri_segment == 'meeting' && $type == 'upcoming' && ($view == 'calendar' ? $view == 'calendar' : $view == 'list')){?>active<?php }?>">
                                            <a href="{{$confirmed_session_url}}"> <span>Confirmed</span></a>
                                        </li>
                                        <li class="<?php if($uri_segment == 'meeting' && $type == 'past' && ($view == 'calendar' ? $view == 'calendar' : $view == 'list')){?>active<?php }?>">
                                            <a href="{{$completed_session_url}}"> <span>Completed</span></a>
                                        </li>
                                    </ul>
                                </li>
                                @if(Auth::guard('mentor')->check())
                                <li class="<?php if($uri_segment == 'sessionlog'){?>active<?php }?>">
                                    <a href="{{url('/mentor/sessionlog/list')}}"> <span>Log A Session</span></a>
                                </li>
                                @endif
                                <li class="">
                                    <a> <span>Chat / Video</span></a>
                                    <ul class="sub-menu" style="display:block !important">
                                        <li class="<?php if($uri_segment == 'chat' && $type == 'mm'){?>active<?php }?>">
                                            <a href="{{$chat_mm_list_url}}"><span>{{$chat_mm_label}}</span></a>
                                        </li>
                                        <li class="<?php if($uri_segment == 'chat' && $type == 'st'){?>active<?php }?>">
                                            <a href="{{$chat_staff_list_url}}"><span>Staff</span></a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="<?php if($uri_segment == 'resources'){?>active<?php }?>">
                                    <a href="{{$resources}}"> <span>Resources</span></a>
                                </li>
                                <li class="<?php if($uri_segment == 'message_center'){?>active<?php }?>">
                                    <a href="{{$message_center}}"> <span>Announcements</span></a>
                                </li>
                                @if(Auth::guard('mentee')->check())
                                    <li class="">
                                        <a> <span>My Goals</span></a>
                                        <ul class="sub-menu" style="display:block !important">
                                            <li class="<?php if($uri_segment == 'my_goals' && $type == 'pending'){?>active<?php }?>">
                                                <a href="{{$pending_goals}}"><span>Pending</span></a>
                                            </li>
                                            <li class="<?php if($uri_segment == 'my_goals' && $type == 'completed'){?>active<?php }?>">
                                                <a href="{{$completed_goals}}"><span>Completed</span></a>
                                            </li>
                                        </ul>
                                    </li>
                                        <li class="">
                                            <a> <span>Take Stock Assignments</span></a>
                                            <ul class="sub-menu" style="display:block !important">
                                                <li class="<?php if($uri_segment == 'my_assignments' && $type == 'pending'){?>active<?php }?>">
                                                    <a href="{{$pending_assignments}}"><span>Pending</span></a>
                                                </li>
                                                <li class="<?php if($uri_segment == 'my_assignments' && $type == 'completed'){?>active<?php }?>">
                                                    <a href="{{$completed_assignments}}"><span>Completed</span></a>
                                                </li>
                                            </ul>
                                        </li>
                                @endif
                                <li class="">
                                    <a href="https://drive.google.com/file/d/14ZEN4PAIwPTlhdUv5j9XnXsbv6oZO8AY/view" target="_blank"> <span>Mentor Toolkit</span></a>
                                </li>
                                    @if(Auth::guard('mentee')->check())
                                        <li class="<?php if($uri_segment == 'upload_report'){?>active<?php }?>">
                                            <a href="{{$upload_report_url}}"><span>Upload Report</span></a>
                                        </li>
                                    @endif
                                    <li>
                                        <a  href="{{$faq_url}}" target="_blank"><span>App Help</span></a>
                                    </li>
                                <li>
                                <a  href="{{$update_password_url}}"><span>Update Password</span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="overlay db-menu-toggle-btn"></div>
                </aside>
                <section class="db-container">
                    <header class="clearfix">
                        <div class="db-header">
                            <div class="db-header-right">
                                <div class="inner clearfix">
                                    <h2>
                                        <?php echo $logged_in; ?>
                                    </h2>
                                    <div class="h-user-top">
                                        <div class="h-user">
                                            <span>{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</span>
                                            <?php if(Auth::guard('mentor')->check() && !empty(Auth::user()->image)){?>
                                            <img src="<?php echo config('app.aws_url'); ?>mentor_pic/<?php echo Auth::user()->image; ?>" alt="">
                                            <?php }else if(Auth::guard('mentee')->check() && !empty(Auth::user()->image)){?>
                                            <img src="<?php echo config('app.aws_url'); ?>userimage/<?php echo Auth::user()->image; ?>" alt="">
                                            <?php }else{ ?>
                                            <img src="<?php echo url('assets/'); ?>/images/logo.png" alt="">
                                            <?php }?>
                                        </div>
                                        <div class="h-logout">
                                            <a href="{{ $logout_url }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                <img src="<?php echo url('assets/'); ?>/icon/logout.png" alt="">
                                            </a>
                                            <form id="logout-form" action="{{ $logout_url }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="db-menu-toggle">
                        <button type="button" class="db-menu-toggle-btn"><i class="fa fa-bars"></i></button>
                    </div>
                    @yield('content')
                </section>
            </div>
        </div>
        <div class="db-footer">
            <div class="db-f-inr">
                <!-- <p>Copyright © 1995-<?php //echo date("Y"); ?> <?php //echo env('APP_COMPANY_NAME');?>.</p> -->
            </div>
        </div>
    </main>
    <!--New Footer-->
    <footer class="ast-custom-footer" itemscope="itemscope" itemtype="https://schema.org/WPFooter">
        <div class="astra-advanced-hook-24">
            <div class="fl-builder-content fl-builder-content-24 fl-builder-global-templates-locked" data-post-id="24">
                <div class="fl-row fl-row-full-width fl-row-bg-color fl-node-5fd903ae7ce43" data-node="5fd903ae7ce43">
                    <div class="fl-row-content-wrap">
                        <div class="fl-row-content fl-row-fixed-width fl-node-content">
                            <div class="fl-col-group fl-node-5fd903ae7e8f2" data-node="5fd903ae7e8f2">
                                <div class="fl-col fl-node-5fd903ae7e992" data-node="5fd903ae7e992">
                                    <div class="fl-col-content fl-node-content">
                                        <div id="bottom-footer" class="fl-module fl-module-rich-text fl-node-5fd9079d1f990" data-node="5fd9079d1f990">
                                            <div class="fl-module-content fl-node-content">
                                                <div class="fl-rich-text">
                                                    <p>© Take Stock in Children 2020. All Rights Reserved. <a href="https://www.takestockinchildren.org/wp-content/uploads/2021/01/TSIC-990-Form.pdf" target="_blank" rel="noopener">990 Form</a> | <a href="https://www.takestockinchildren.org/wp-content/uploads/2021/01/Financial-Statements.pdf" target="_blank" rel="noopener">Audited Financials</a> | <a href="https://www.takestockinchildren.org/get-involved/">Annual Evaluation(s)</a> | <a href="https://www.takestockinchildren.org/privacy-policy/">Privacy Policy </a>| <a href="https://www.takestockinchildren.org/terms-of-use/">Terms of Use</a></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uabb-js-breakpoint" style="display: none;"></div>
        </div>
    </footer>
    <!--Js-->
    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/owl.carousel.min.js"></script>
    <!-- <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/fullcalendar.min.js"></script> -->    <!--Custom JS-->
    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/script.js"></script>
    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/sweetalert.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.11.1/basic/ckeditor.js"></script>
    <script type="text/javascript" src="<?php echo url('assets/'); ?>/js/jquery.mask.js"></script>


    <div class="modal fade" id="videoCallPop">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal body -->
                <div class="modal-body text-center p-5">
                    <h3>You have a video call request...</h3>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer justify-content-center">
                    <button type="button" id="videoCallDeny" class="btn btn-danger">DENY</button>
                    <button type="button" id="videoCallAccept" class="btn btn-success">ACCEPT</button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="logSessionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Log Session</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body my-4 text">
                    <h6>Do you want to log this session?</h6>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-success" href="{{ url('/mentor/sessionlog/add') }}">Yes</a>
                    <a class="btn btn-danger" onclick="closeModalAndRefresh()">No</a>
                </div>
            </div>
        </div>
    </div>

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

    <!-- <div class="autopilot">
        <a class="autopilot-btn" onclick="showchat();" ><i class="fa fas fa-comments" aria-hidden="true"></i></a>
        <div id="chat_div" class="chat-div" style="display:none;">
            <iframe src="https://localhost:3000/chat/?id=<?php echo strtolower($logged_in); ?>_<?php echo Auth::user()->id; ?>" style="width:500px;height:500px;"></iframe>
        </div>
    </div> -->

    <audio id="videoCallingAudio" loop>
        <source src="{{ env('APP_URL') }}/public/ringtone.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

    <!-- SOCKET CONNECTION -->
    <script type="application/javascript">
        var userType = "<?php echo $logged_in; ?>".toLowerCase();
        var urlRedirect = "";
        var recivedData = {};

        var callingAudio = document.getElementById("videoCallingAudio");

        const socketMain = io.connect("{{ env('APP_URL') }}:3000", {
            transports: ['websocket', 'polling', 'flashsocket']
        });

        socketMain.on('connect', function() {
            var det = {
                id: {{ Auth::user()->id }},
                type: userType,
                device: "web"
            };
            // console.log(det);

            socketMain.emit('connected', det);
        });

        socketMain.on('reqReceived', function(data) {
            // console.log("reqReceived");
            // console.log(data);
            // alert("success");
            recivedData = data;

            $("#videoCallPop").modal({backdrop: "static"});
        });

        socketMain.on('endVideo', function(data) {
            console.log("endVideo");
            console.log(data);

            if(data.endBefore) {
                $("#videoCallPop").modal("hide");
                if(userType === 'mentor') {
                    $('#logSessionModal').modal('show');
                }
            } else {
                $("#roomLeftBtn").trigger("click");
                alert("Call denied by receiver.");
		window.location.reload()
            };
        });

        $("#videoCallAccept").click(function() {
            $("#videoCallPop").modal("hide");

            if(userType == 'mentor') {
                urlRedirect = "{{ env('APP_URL') }}/mentor/videochat/initiate?mentee_id=" + recivedData.sender_id;
            };

            if(userType == 'mentee') {
                urlRedirect = "{{ env('APP_URL') }}/mentee/videochat/initiate?mentor_id=" + recivedData.sender_id;
            };

            window.location.href = urlRedirect;
        });

        $("#videoCallDeny").click(function() {
            socketMain.emit('callDenied', recivedData);
            $("#videoCallPop").modal("hide");
        });

        // $("#videoCallPop").on('show.bs.modal', function(){
        //     // alert('The modal is about to be shown.');
        //     callingAudio.load();
        //     callingAudio.play();
        // });

        // $("#videoCallPop").on('hide.bs.modal', function(){
        //     // alert('The modal is about to be hide.');
        //     callingAudio.pause();
        // });

        function closeModalAndRefresh() {
            $('#logSessionModal').modal('hide');
            window.location.reload()
        }
    </script>
</body>
</html>
