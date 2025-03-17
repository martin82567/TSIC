<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes Mentee
|--------------------------------------------------------------------------
*/

Route::match(array('GET','POST'),'/getVersionCode', 'Api\VersionController@get');

Route::match(array('GET','POST'),'/videochat/initiate_chat', 'Api\VideochatController@initiate_chat');
Route::match(array('GET','POST'),'/videochat/generate_room', 'Api\VideochatController@generate_room');
Route::match(array('GET','POST'),'/videochat/disconnect_room', 'Api\VideochatController@disconnect_room');
Route::match(array('GET','POST'),'/videochat/apn_push', 'Api\VideochatController@apn_push');

Route::match(array('GET','POST'),'/webhook/twilio_disconnect', 'Api\WebhookController@twilio_disconnect');
Route::match(array('GET','POST'),'/webhook/twilio_chat', 'Api\WebhookController@twilio_chat');

Route::post('/v1/user/login', 'Api\V1\UsersController@login');
Route::post('/v1/user/forgotpassword', 'Api\V1\UsersController@forgot_password');
Route::post('/v1/user/resetpassword', 'Api\V1\UsersController@reset_password');

Route::post('/v1/user/changepassword', 'Api\V1\UserController@change_password');
Route::get('/v1/user/userdetails', 'Api\V1\UserController@user_details');
Route::post('/v1/user/updateuserdetails', 'Api\V1\UserController@update_user_details');
Route::get('/v1/user/logout', 'Api\V1\UserController@logout');
Route::get('/v1/user/mentordetails', 'Api\V1\UserController@mentordetails');
Route::get('/v1/user/get_timezone', 'Api\V1\UserController@get_timezone');
Route::post('/v1/user/update_password', 'Api\V1\UserController@update_password');

Route::post('/v1/user/creategoaltask', 'Api\V1\GoaltaskController@add');
Route::post('/v1/user/getgoaltask', 'Api\V1\GoaltaskController@getlist');
Route::post('/v1/user/getgoaltaskdetails', 'Api\V1\GoaltaskController@getdetails');
Route::post('/v1/user/actiongoaltask', 'Api\V1\GoaltaskController@actiongoaltask');
Route::post('/v1/user/goaltaskcompltelist', 'Api\V1\GoaltaskController@goaltaskcompltelist');
Route::post('/v1/user/notesavegoaltask', 'Api\V1\GoaltaskController@notesavegoaltask');
Route::post('/v1/user/filesavegoaltask', 'Api\V1\GoaltaskController@filesavegoaltask');
Route::get('/v1/user/filedeletegoaltask/{id}', 'Api\V1\GoaltaskController@filedeletegoaltask');

Route::post('/v1/user/searchresource', 'Api\V1\ResourceController@searchresource');
Route::get('/v1/user/resourcedetails/{id}', 'Api\V1\ResourceController@resourcedetails');

Route::post('/v1/user/searchelearning', 'Api\V1\ElearningController@search_learning');
Route::get('/v1/user/elearningdetails/{id}', 'Api\V1\ElearningController@e_learningdetails');

Route::get('/v1/getjob/', 'Api\V1\JobController@getlist');
Route::get('/v1/jobdetails/{id}', 'Api\V1\JobController@getdetails');
Route::post('/v1/job/apply', 'Api\V1\JobController@apply');
Route::get('/v1/getappliedjob/', 'Api\V1\JobController@getappliedjob');

Route::match(array('GET','POST'),'/v1/chat/my_chats', 'Api\V1\ChatsController@index');
// Route::get('/v1/chat/my_chats', 'Api\V1\ChatsController@index');
Route::match(array('GET','POST'),'/v1/chat/staff_chat', 'Api\V1\ChatsController@staff_chat');
Route::get('/v1/getstaffs', 'Api\V1\ChatsController@get_staff_list');

Route::get('/v1/journal/list', 'Api\V1\JournalController@my_journal');
Route::get('/v1/journal/details/{id}', 'Api\V1\JournalController@journal_details');
Route::post('/v1/journal/add', 'Api\V1\JournalController@add_journal');

Route::post('/v1/createreport', 'Api\V1\ReportController@add');
Route::post('/v1/listreport', 'Api\V1\ReportController@list');


Route::match(array('GET','POST'), '/v1/meeting/list', 'Api\V1\MeetingController@list');
Route::match(array('GET','POST'), '/v1/meeting/alllist', 'Api\V1\MeetingController@all');
Route::post('/v1/meeting/saverequest', 'Api\V1\MeetingController@saverequest');
Route::post('/v1/meeting/make_accept_web_meeting', 'Api\V1\MeetingController@make_accept_web_meeting');
Route::post('/v1/meeting/accept', 'Api\V1\MeetingController@make_accepted');
Route::match(array('GET','POST'), '/v1/meeting/upcoming', 'Api\V1\MeetingController@upcoming');
Route::match(array('GET','POST'), '/v1/meeting/past', 'Api\V1\MeetingController@past');

Route::match(array('GET','POST'), '/v1/system-messaging/index', 'Api\V1\SystemMessagingController@index');

Route::match(array('GET','POST'), '/v1/message-center/index', 'Api\V1\MessageCenterController@index');

Route::match(array('GET','POST'), '/v1/faq/index', 'Api\V1\FaqController@index');


/*
|--------------------------------------------------------------------------
| API Routes Mentor
|--------------------------------------------------------------------------
*/

/********LOGIN TEST*******/
Route::post('/v1/mentor/testlogin', 'Api\V1\mentor\UsersController@testlogin');
/**************/

Route::post('/v1/mentor/login', 'Api\V1\mentor\UsersController@login');
Route::post('/v1/mentor/logout', 'Api\V1\mentor\UserController@logout');
Route::get('/v1/mentor/get_timezone', 'Api\V1\mentor\UserController@get_timezone');
Route::get('/v1/mentor/my-profile', 'Api\V1\mentor\ProfileController@my_profile');
Route::post('/v1/mentor/save-profile', 'Api\V1\mentor\ProfileController@update_profile');
Route::post('/v1/mentor/update_password', 'Api\V1\mentor\ProfileController@update_password');
Route::post('/v1/mentor/menteelist', 'Api\V1\mentor\MentorController@menteelist');
Route::post('/v1/mentor/menteereports', 'Api\V1\mentor\MentorController@get_mentee_reports');
Route::post('/v1/mentor/stafflist', 'Api\V1\mentor\StaffController@stafflist');

Route::post('/v1/mentor/forgotpassword', 'Api\V1\mentor\UsersController@forgot_password');
Route::post('/v1/mentor/resetpassword', 'Api\V1\mentor\UsersController@reset_password');

Route::post('/v1/mentor/creategoaltaskchallenge', 'Api\V1\mentor\GoalTaskChallengeController@add');
Route::post('/v1/mentor/listgoaltaskchallenge', 'Api\V1\mentor\GoalTaskChallengeController@list');
Route::post('/v1/mentor/assigngoaltaskchallenge', 'Api\V1\mentor\GoalTaskChallengeController@assign');
Route::post('/v1/mentor/listmenteegoaltaskchallenge', 'Api\V1\mentor\GoalTaskChallengeController@listmentee');
Route::post('/v1/mentor/delmenteegoaltaskchallenge', 'Api\V1\mentor\GoalTaskChallengeController@delmentee');


Route::post('/v1/mentor/createsession', 'Api\V1\mentor\SessionController@add');
Route::post('/v1/mentor/listsession', 'Api\V1\mentor\SessionController@list');
Route::match(array('GET','POST'), '/v1/mentor/get_session_method_location', 'Api\V1\mentor\SessionController@get_session_method_location');


Route::post('/v1/mentor/addmeeting', 'Api\V1\mentor\MeetingController@add');
Route::post('/v1/mentor/listmeeting', 'Api\V1\mentor\MeetingController@list');
Route::match(array('GET','POST'),'/v1/mentor/alllistmeeting', 'Api\V1\mentor\MeetingController@all');
Route::post('/v1/mentor/no_reschedule_meeting', 'Api\V1\mentor\MeetingController@no_reschedule');
Route::post('/v1/mentor/cancel_meeting', 'Api\V1\mentor\MeetingController@cancel');
Route::get('/v1/mentor/upcoming_accepted_meeting', 'Api\V1\mentor\MeetingController@upcoming_accepted_meeting');

Route::match(array('GET','POST'),'/v1/mentor/mentee_chat', 'Api\V1\mentor\ChatsController@mentee');
Route::match(array('GET','POST'),'/v1/mentor/staff_chat', 'Api\V1\mentor\ChatsController@staff');

Route::get('/v1/mentor/schoollist', 'Api\V1\mentor\MentorController@get_school');
Route::get('/v1/mentor/todaymeeting', 'Api\V1\mentor\MeetingController@todaymeeting');
Route::match(array('GET','POST'), '/v1/mentor/logged_meeting', 'Api\V1\mentor\MeetingController@logged_meeting');

Route::post('/v1/mentor/searchelearning', 'Api\V1\mentor\MentorController@search_learning');

Route::match(array('GET','POST'),'/v1/mentor/system-messaging/index', 'Api\V1\mentor\SystemMessagingController@index');

Route::match(array('GET','POST'), '/v1/mentor/message-center/index', 'Api\V1\mentor\MessageCenterController@index');

Route::match(array('GET','POST'), '/v1/mentor/faq/index', 'Api\V1\mentor\FaqController@index');


/*+++++++Web Videochat API+++++++*/

Route::match(array('GET','POST'),'/webvideochat/initiate_chat', 'Api\WebVideochatController@initiate_chat');
Route::match(array('GET','POST'),'/webvideochat/disconnect_room', 'Api\WebVideochatController@disconnect_room');
Route::match(array('GET','POST'),'/webvideochat/check_room', 'Api\WebVideochatController@check_room');

/*+++++++Twilio Chat API+++++++*/

Route::match(array('GET','POST'),'/chat/get_access_token', 'Api\ChatController@get_access_token');
Route::match(array('GET','POST'),'/chat/channel_id_update', 'Api\ChatController@channel_id_update');
Route::match(array('GET','POST'),'/chat/send_nofication', 'Api\ChatController@send_nofication');

/*+++++++Disclaimer API+++++++*/

Route::match(array('GET','POST'),'/disclaimer/index', 'Api\DisclaimerController@index');

