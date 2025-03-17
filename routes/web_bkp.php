<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/***************************************************************/
Route::get('/dataimport/gettoken', 'DataimportController@gettoken');
Route::get('/dataimport/updateassignmentee', 'DataimportController@updateassignmentee');
//Route::get('/dataimport/{token}', 'DataimportController@index');
Route::get('/dataimport/schoolentry/{token}', 'DataimportController@schoolentry');
Route::get('/dataimport/schooldetailsentry/{token}', 'DataimportController@schooldetailsentry');
Route::get('/dataimport/studenentrybyagency/{id}/{token}', 'DataimportController@studenentrybyagency');
Route::get('/dataimport/mentorentrybyagency/{id}/{token}', 'DataimportController@mentorentrybyagency');
Route::get('/dataimport/mentoringsessions', 'DataimportController@mentoringsessions');

Route::get('/dataimport/mentee_mentor_data_import', 'DataimportController@mentee_mentor_data_import');
Route::get('/dataimport/testroute', 'DataimportController@testroute');
/***************************************************************/

/*++++++++++++++++++++++++++CRON++++++++++++++++++++++++++++++*/

Route::get('/cron/mapMentee', 'CronController@mapMentee');
Route::get('/cron/get_user_address', 'CronController@get_user_address');
Route::get('/cron/get_timezone_latlong', 'CronController@get_timezone_latlong');
Route::get('/cron/get_duplicate_entry', 'CronController@get_duplicate_entry');
Route::get('/cron/mentor_staff_chat_email', 'CronController@mentor_staff_chat_email');
Route::get('/cron/mentor_mentee_chat_email', 'CronController@mentor_mentee_chat_email');
Route::get('/cron/mentee_staff_chat_email', 'CronController@mentee_staff_chat_email');
Route::get('/cron/create_star_session/{token}', 'CronController@create_star_session');
Route::get('/cron/checking_email', 'CronController@checking_email');
Route::get('/cron/check_active_keyword_exists', 'CronController@check_active_keyword_exists');
Route::get('/cron/send_notification_unread_mentor', 'CronController@send_notification_unread_mentor');
Route::get('/cron/send_notification_unread_mentee', 'CronController@send_notification_unread_mentee');
Route::get('/cron/meeting_reminder_before', 'CronController@meeting_reminder_before');
Route::get('/cron/meeting_reminder_after', 'CronController@meeting_reminder_after');
Route::get('/cron/reset_session_log_count', 'CronController@reset_session_log_count');
Route::get('/cron/db_export', 'CronController@db_export');
// Route::get('/cron/create_video_compostion', 'CronController@create_video_compostion');
Route::get('/cron/fetch_video_duration', 'CronController@fetch_video_duration');
Route::get('/cron/reset_videochat_weekly', 'CronController@reset_videochat_weekly');
Route::get('/cron/db_backup', 'CronController@db_backup');
Route::get('/cron/remove_video_chats', 'CronController@remove_video_chats');
Route::get('/cron/test_entry', 'CronController@test_entry');
/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

/*+++++++++++++++++++++++++++DB+++++++++++++++++++++++++++++*/
Route::get('/db/download/{folder?}/{filename?}', 'DBController@download');
/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

Route::get('/404', 'ErrorController@four_not_four');
Route::get('/exception/index', 'ExceptionController@index');

Auth::routes();
Route::get('/', 'Admin\AdminController@index');
Route::get('/home', 'HomeController@index');
Route::get('/ReadXml', 'ReadXmlController@load');

Route::prefix('admin')->group(function() {
	Route::get('/login','Auth\Admin\AdminLoginController@showLoginForm')->name('admin.login');
	Route::post('/login', 'Auth\Admin\AdminLoginController@login')->name('admin.login.submit');
	Route::post('logout/', 'Auth\Admin\AdminLoginController@logout')->name('admin.logout');
	Route::get('/', 'Admin\AdminController@index')->name('admin.dashboard');
	Route::get('/settings', 'Admin\AdminController@settings')->name('admin.settings');
	Route::post('/save_settings', 'Admin\AdminController@save_settings')->name('admin.settings.save');
	#################################### SWITCH A/C ###########################################
	Route::match(array('GET','POST'), '/switch-acc/index/{type?}', 'Admin\SwitchAccountController@index')->name('admin.switch-acc');
	Route::match(array('GET','POST'), '/switch-acc/back-to-admin', 'Admin\SwitchAccountController@back_to_admin')->name('admin.switch-acc.back-to-admin');
	
	Route::post('/switch-acc/save_post', 'Admin\SwitchAccountController@save_post')->name('admin.switch-acc.save_post');
	#################################### KEYWORDS ###########################################

	Route::get('/keyword/list','Admin\KeywordController@index')->name('admin.keyword.list');
	Route::get('/keyword/change_status','Admin\KeywordController@change_status')->name('admin.keyword.change_status');
	Route::get('/keyword/add','Admin\KeywordController@add')->name('admin.keyword.add');
	Route::post('/keyword/save','Admin\KeywordController@save')->name('admin.keyword.save');
	#################################### AGENCY ###########################################
	Route::get('/agency', 'Admin\AgencyController@index')->name('admin.agency');
	Route::get('/inactiveagencies', 'Admin\AgencyController@inactive_agencies')->name('admin.inactiveagencies');
	Route::get('/user', 'Admin\AgencyController@user')->name('admin.user');
	Route::get('/inactiveuser', 'Admin\AgencyController@inactive_user')->name('admin.inactiveuser');
	Route::get('/agency/add/{id}', 'Admin\AgencyController@add')->name('admin.agency.add');
	Route::post('/agency/save', 'Admin\AgencyController@save')->name('admin.agency.save');
	Route::get('/agency/delete_file/{id}/{admin_id}/{file_name}', 'Admin\AgencyController@delete_agency_files')->name('admin.agency.delete_file');
	Route::post('/agency/getcities', 'Admin\AgencyController@getcities')->name('admin.agency.getcities');
	Route::get('/agency/remove_pic/{id}/{profile_pic}/{type}', 'Admin\AgencyController@remove_agency_pic')->name('admin.agency.remove_pic');
	Route::post('/agency/statuschangeajax', 'Admin\AgencyController@status_change_ajax')->name('admin.agency.statuschangeajax');
	Route::get('/agency/changestatus/{id}/{uri}', 'Admin\AgencyController@change_status')->name('admin.agency.changestatus');


	Route::get('/agency/meeting', 'Admin\AgencyController@meeting_list')->name('admin.agency.meeting');
	Route::get('/agency/inactivemeeting', 'Admin\AgencyController@inactive_meeting_list')->name('admin.agency.inactivemeeting');
	Route::get('/agency/meeting/add/{id?}', 'Admin\AgencyController@add_meetings')->name('admin.agency.add');
	Route::post('/agency/save_meetings', 'Admin\AgencyController@save_meetings')->name('admin.agency.meeting.save');
	Route::post('/agency/get_mentees_by_mentor', 'Admin\AgencyController@get_mentees_by_mentor');
	Route::post('/agency/get_mentee_from_meeting_data', 'Admin\AgencyController@get_mentee_from_meeting_data');
	Route::get('/agency/change_status_meetings/{id?}/{uri?}', 'Admin\AgencyController@change_status_meetings');
	Route::get('/agency/view_meeting', 'Admin\AgencyController@view_meeting');
	Route::get('/agency/staff_keyword_access/{id?}/{parent_id?}', 'Admin\AgencyController@change_staff_keyword_alert_access');
	#################################### RESOURCE CATEGORY #################################
	Route::get('/resource_category', 'Admin\ResourcecategoryController@index')->name('admin.resource_category');
	Route::get('/resource_category/add/{id}', 'Admin\ResourcecategoryController@add')->name('admin.resource_category.add');
	Route::post('/resource_category/save', 'Admin\ResourcecategoryController@save')->name('admin.resource_category.save');
	#################################### RESOURCE #################################
	Route::get('/resource', 'Admin\ResourceController@index')->name('admin.resource');
	Route::get('/inactiveresource', 'Admin\ResourceController@inactive_resource')->name('admin.inactiveresource');
	Route::get('/resource/add/{id}', 'Admin\ResourceController@add')->name('admin.resource.add');
	Route::post('/resource/save', 'Admin\ResourceController@save')->name('admin.resource.save');
	Route::get('/resource/delete_file/{id}/{resource}/{file_name}', 'Admin\ResourceController@delete_resource_files')->name('admin.resource.delete_file');
	Route::get('/resource/remove_pic/{id}/{profile_pic}', 'Admin\ResourceController@remove_resource_pic')->name('admin.resource.remove_pic');
	Route::post('/resource/changestatusajax', 'Admin\ResourceController@change_status_ajax')->name('admin.resource.changestatusajax');
	Route::get('/resource/changestatus/{id}/{uri}', 'Admin\ResourceController@change_status')->name('admin.resource.changestatus');

	#################################### GOAL / TASK #################################
	Route::get('/goaltask', 'Admin\GoaltaskController@index')->name('admin.goaltask');
	Route::get('/inactivegoaltask', 'Admin\GoaltaskController@inactive_goaltask')->name('admin.inactivegoaltask');
	Route::get('/goaltask/add/{id}', 'Admin\GoaltaskController@add')->name('admin.goaltask.add');
	Route::post('/goaltask/save', 'Admin\GoaltaskController@save')->name('admin.goaltask.save');
	Route::get('/goaltask/delete_file/{id}/{goaltask_id}/{file_name}', 'Admin\GoaltaskController@delete_task_files')->name('admin.goaltask.delete_file');

	Route::get('/viewtask/{id}', 'Admin\GoaltaskController@viewtask')->name('admin.viewtask');
	Route::get('/viewgoal/{id}', 'Admin\GoaltaskController@viewgoal')->name('admin.viewgoal');
	Route::get('/viewchallenge/{id}', 'Admin\GoaltaskController@viewchallenge')->name('admin.viewchallenge');

	Route::post('/taskpoint', 'Admin\GoaltaskController@taskpoint')->name('admin.taskpoint');
	Route::post('/maxpoint', 'Admin\GoaltaskController@getmaxpoint')->name('admin.maxpoint');

	Route::post('/goaltask/assign_mentee', 'Admin\GoaltaskController@assign_mentee')->name('admin.assign_mentee');
	Route::post('/goaltask/changestatusajax', 'Admin\GoaltaskController@change_status_ajax')->name('admin.changestatusajax');
	Route::get('/goaltask/changestatus/{id}/{uri}', 'Admin\GoaltaskController@change_status')->name('admin.changestatus');
	Route::post('/goaltask/view_notes', 'Admin\GoaltaskController@view_notes')->name('admin.viewnotes');
	Route::post('/goaltask/view_uploaded_files', 'Admin\GoaltaskController@view_uploaded_files')->name('admin.viewuploadedfiles');

	#################################### VICTIM #################################
	Route::get('/mentee', 'Admin\VictimController@index')->name('admin.mentee');
	Route::get('/mentee/notelist/{id}', 'Admin\VictimController@notelist')->name('admin.mentee.notelist');
	Route::post('/mentee/notesave', 'Admin\VictimController@notesave')->name('admin.mentee.notesave');
	Route::get('/mentee/notefiledownload/{note_id}', 'Admin\VictimController@notefiledownload')->name('admin.mentee.notefiledownload');
	Route::get('/mentee/notefiledelete/{note_id}', 'Admin\VictimController@notefiledelete')->name('admin.mentee.notefiledelete');
	Route::get('/mentee/add/{id}', 'Admin\VictimController@add')->name('admin.mentee.add');
	Route::post('/mentee/save', 'Admin\VictimController@save')->name('admin.mentee.save');
	Route::get('/inactivementee', 'Admin\VictimController@inactive_victim')->name('admin.inactivementee');
	Route::get('/victim/changestatus/{id}/{uri}', 'Admin\VictimController@change_status')->name('admin.changestatus');
	Route::get('/viewnote/{id}/{user_id}', 'Admin\VictimController@viewnote')->name('admin.viewnote');
	Route::post('/mentee/viewtracker', 'Admin\VictimController@view_tracker')->name('admin.viewtracker');
	Route::post('/mentee/get_mentor_from_agency', 'Admin\VictimController@get_mentor_from_agency')->name('admin.get_mentor_from_agency');
	Route::get('/mentee/view_report/{id?}', 'Admin\VictimController@view_report')->name('admin.view_report');
	#################################### MENTOR #################################

	Route::get('/mentor', 'Admin\MentorController@index')->name('admin.mentor');
	Route::get('/inactivementor', 'Admin\MentorController@inactive_mentor')->name('admin.inactivementor');
	Route::get('/mentor/changestatus/{id}/{uri}', 'Admin\MentorController@change_status')->name('admin.mentor.changestatus');
	Route::get('/mentor/add/{id}', 'Admin\MentorController@add')->name('admin.mentor.add');
	Route::post('/mentor/save', 'Admin\MentorController@save')->name('admin.mentor.save');
	Route::get('/mentor/session/{id?}', 'Admin\MentorController@view_session')->name('admin.mentor.session');

	#################################### JOB #################################
	Route::get('/job', 'Admin\JobController@index')->name('admin.job');
	Route::get('/inactivejobs', 'Admin\JobController@inactive_jobs')->name('admin.inactivejobs');
	Route::get('/job/add/{id}', 'Admin\JobController@add')->name('admin.job.add');
	// Route::get('/job/viewapplication/{id}', 'Admin\JobController@viewapplication')->name('admin.job.viewapplication');
	Route::post('/job/save', 'Admin\JobController@save')->name('admin.job.save');
	Route::post('/job/changestatusajax', 'Admin\JobController@change_status_ajax')->name('admin.job.changestatusajax');
	Route::get('/job/changestatus/{id}/{uri}', 'Admin\JobController@change_status')->name('admin.job.changestatus');
	Route::get('/job/remove_job_upload_desc/{id}/{file}', 'Admin\JobController@remove_job_upload_desc')->name('admin.job.remove_job_upload_desc');
	#################################### RESOURCE CATEGORY #################################
	Route::get('/e_learning', 'Admin\ElearningController@index')->name('admin.e_learning');
	Route::get('/inactiveelearning', 'Admin\ElearningController@inactive_elearning')->name('admin.inactiveelearning');
	Route::get('/e_learning/add/{id}', 'Admin\ElearningController@add')->name('admin.e_learning.add');
	Route::post('/e_learning/save', 'Admin\ElearningController@save')->name('admin.e_learning.save');
	Route::get('/e_learning/changestatus/{id}/{type}', 'Admin\ElearningController@change_status')->name('admin.e_learning.changestatus');
	
	#################################### PROFILE ###########################################
	Route::get('/profile', 'Admin\ProfileController@index')->name('admin.profile');
	Route::get('/removeprofilepic/{id}/{profile_pic}', 'Admin\ProfileController@remove_profile_pic')->name('admin.removeprofilepic');
	Route::post('/profile/save', 'Admin\ProfileController@save')->name('admin.profile.save');
	Route::get('/profile/deletefile/{id}', 'Admin\ProfileController@deletefile')->name('admin.profile.deletefile');
	Route::get('/profile/userservices/{user_id}', 'Admin\ProfileController@user_services')->name('admin.profile.userservices');
	#################################### CHAT ###########################################
	Route::get('/chat', 'Admin\ChatController@index')->name('admin.chat.list');
	Route::get('/chat/get_mentor_staff_chatcode', 'Admin\ChatController@get_mentor_staff_chatcode')->name('admin.chat.chatcode');
	Route::get('/chat/get_mentee_staff_chatcode', 'Admin\ChatController@get_mentee_staff_chatcode')->name('admin.chat.chatcode');
	Route::get('/chat/details_mentor/{chat_code?}', 'Admin\ChatController@chat_details_mentor')->name('admin.chat.history');
	Route::get('/chat/details_mentee/{chat_code?}', 'Admin\ChatController@chat_details_mentee')->name('admin.chat.history');
	Route::get('/chat/threads', 'Admin\ChatController@view_threads')->name('admin.chat.thread-view');
	Route::post('/chat/get_data_from_mentor', 'Admin\ChatController@get_data_from_mentor')->name('admin.chat.mentor-data');
	Route::post('/chat/get_data_from_affiliate', 'Admin\ChatController@get_data_from_affiliate')->name('admin.chat.get_data_from_affiliate');
	Route::get('/chat/keyword-notification-reviewed', 'Admin\ChatController@keyword_notification_reviewed')->name('admin.chat.keyword-notification-reviewed');
	Route::get('/chat/keyword-notification-unreviewed', 'Admin\ChatController@keyword_notification_unreviewed')->name('admin.chat.keyword-notification-unreviewed');
	Route::post('/chat/make-reviewed-notification', 'Admin\ChatController@make_reviewed_notification')->name('admin.chat.make-reviewed-notification');
	Route::post('/chat/make-flagged-notification', 'Admin\ChatController@make_flagged_notification')->name('admin.chat.make-flagged-notification');

	####################################  ###########################################

	#################################### OFFERS #################################
	Route::get('/active_offertypes', 'Admin\OfferController@active_offer_type')->name('admin.active_offertypes');
	Route::get('/offer_type/add/{id}', 'Admin\OfferController@add')->name('admin.offer_type.add');
	Route::post('/offer_type/save', 'Admin\OfferController@save')->name('admin.offer_type.save');
	####################################  #################################
	####################################  #################################
	Route::post('/mentee/add_offerings', 'Admin\VictimController@add_offerings')->name('admin.add_offerings');
	Route::post('/mentee/view_offerings', 'Admin\VictimController@view_offerings')->name('admin.view_offerings');
	#################################### 04.03.2019 #################################  
	Route::post('/mentee/add_note', 'Admin\VictimController@add_note')->name('admin.add_note');
	Route::post('/mentee/view_note', 'Admin\VictimController@view_note')->name('admin.view_note');
	Route::post('/mentee/assign_task', 'Admin\VictimController@assign_task')->name('admin.assign_task');
	Route::post('/mentee/assign_goal', 'Admin\VictimController@assign_goal')->name('admin.assign_goal');
	Route::post('/mentee/assign_challenge', 'Admin\VictimController@assign_challenge')->name('admin.assign_challenge');
	Route::post('/mentee/view_assign_gtc', 'Admin\VictimController@view_assign_gtc')->name('admin.view_assign_gtc');
	#################################### SCHOOL #################################
	Route::get('/active-school', 'Admin\SchoolController@index')->name('admin.school.index');
	Route::get('/inactive-school', 'Admin\SchoolController@inactive')->name('admin.school.inactive');
	Route::get('/school/add/{id?}', 'Admin\SchoolController@add')->name('admin.school.add');
	Route::post('/school/save', 'Admin\SchoolController@save')->name('admin.school.save');
	Route::get('/school/change_status/{id?}/{uri?}', 'Admin\SchoolController@change_status')->name('admin.school.change_status');
	Route::get('/videochat/list', 'Admin\VideochatController@index')->name('admin.videochat.list');
	Route::get('/videochat/details/{room?}', 'Admin\VideochatController@details')->name('admin.videochat.details');
	#################################### VIDEO CHAT #################################
	Route::get('/videochat/create_compositions/{room?}', 'Admin\VideochatController@create_compositions')->name('admin.videochat.create_compositions');
	Route::match(array('GET','POST'), '/videochat/getmedia/{compositionSid?}', 'Admin\VideochatController@getmedia')->name('admin.videochat.getmedia');
	#################################### SYSTEM MESSAGING #################################
	Route::match(array('GET','POST'), '/system-messaging/list', 'Admin\SystemMessagingController@index')->name('admin.system-messaging.list');
	Route::match(array('GET','POST'), '/system-messaging/add', 'Admin\SystemMessagingController@add')->name('admin.system-messaging.add');
	Route::match(array('GET','POST'), '/system-messaging/delete', 'Admin\SystemMessagingController@delete')->name('admin.system-messaging.delete');
	Route::match(array('GET','POST'), '/system-messaging/expire', 'Admin\SystemMessagingController@expire')->name('admin.system-messaging.expire');
	Route::post('/system-messaging/save', 'Admin\SystemMessagingController@save')->name('admin.system-messaging.save');
	#################################### MESSAGE CENTER #################################
	Route::match(array('GET','POST'), '/message-center/list', 'Admin\MessageCenterController@index')->name('admin.message-center.list');
	Route::match(array('GET','POST'), '/message-center/add', 'Admin\MessageCenterController@add')->name('admin.message-center.add');
	Route::post('/message-center/save', 'Admin\MessageCenterController@save')->name('admin.message-center.save');
	Route::match(array('GET','POST'), '/message-center/delete', 'Admin\MessageCenterController@delete')->name('admin.message-center.delete');
	Route::match(array('GET','POST'), '/message-center/get_user_search', 'Admin\MessageCenterController@get_user_search')->name('admin.message-center.get_user_search');
	Route::match(array('GET','POST'), '/message-center/message_users', 'Admin\MessageCenterController@message_users')->name('admin.message-center.message_users');

});

Route::prefix('mentor')->group(function() {
	Route::get('/login','Auth\Mentor\MentorLoginController@showLoginForm')->name('mentor.login');
	Route::get('/forget-password','Auth\Mentor\MentorLoginController@showForgetPassword')->name('mentor.forget-password');
	Route::get('/reset-password/{email?}','Auth\Mentor\MentorLoginController@showResetPassword')->name('mentor.reset-password');
	Route::post('/login', 'Auth\Mentor\MentorLoginController@login')->name('mentor.login.submit');
	Route::post('/submit_forget_password', 'Auth\Mentor\MentorLoginController@submit_forget_password')->name('mentor.forget-password.submit');
	Route::post('/submit_reset_password', 'Auth\Mentor\MentorLoginController@submit_reset_password')->name('mentor.reset-password.submit');
	Route::get('/', 'Mentor\HomeController@index')->name('mentor.dashboard');
	Route::post('logout/', 'Auth\Mentor\MentorLoginController@logout')->name('mentor.logout');
	Route::get('meeting/list', 'Mentor\MeetingController@index')->name('mentor.meeting.index');
	Route::get('meeting/add', 'Mentor\MeetingController@add')->name('mentor.meeting.add');
	Route::post('meeting/save', 'Mentor\MeetingController@save')->name('mentor.meeting.save');
	Route::post('meeting/get_session_data_ajax', 'Mentor\MeetingController@get_session_data_ajax')->name('mentor.meeting.get_session_data_ajax');
	Route::get('meeting/cancel', 'Mentor\MeetingController@cancel')->name('mentor.meeting.cancel');
	Route::get('meeting/deny', 'Mentor\MeetingController@deny')->name('mentor.meeting.deny');
	Route::get('sessionlog/list', 'Mentor\SessionLogController@list')->name('mentor.sessionlog.list');
	Route::get('sessionlog/add', 'Mentor\SessionLogController@add')->name('mentor.sessionlog.add');
	Route::post('sessionlog/save', 'Mentor\SessionLogController@save')->name('mentor.sessionlog.save');
	Route::get('chat/userlist', 'Mentor\ChatController@userlist')->name('mentor.chat.userlist');
	Route::get('chat/get_staff_chatcode', 'Mentor\ChatController@get_staff_chatcode')->name('mentor.chat.get_staff_chatcode');
	Route::get('chat/get_mentee_chatcode', 'Mentor\ChatController@get_mentee_chatcode')->name('mentor.chat.get_mentee_chatcode');
	Route::get('chat/message', 'Mentor\ChatController@message')->name('mentor.chat.message');
	Route::match(array('POST','GET'), 'videochat/test', 'Mentor\VideochatController@test')->name('mentor.videochat.test');
	Route::match(array('POST','GET'), 'videochat/initiate', 'Mentor\VideochatController@initiate')->name('mentor.videochat.initiate');
	

});

Route::prefix('mentee')->group(function() {
	Route::get('/login','Auth\Mentee\MenteeLoginController@showLoginForm')->name('mentee.login');
	Route::get('/forget-password','Auth\Mentee\MenteeLoginController@showForgetPassword')->name('mentee.forget-password');
	Route::get('/reset-password/{email?}','Auth\Mentee\MenteeLoginController@showResetPassword')->name('mentee.reset-password');
	Route::post('/login', 'Auth\Mentee\MenteeLoginController@login')->name('mentee.login.submit');
	Route::post('/submit_forget_password', 'Auth\Mentee\MenteeLoginController@submit_forget_password')->name('mentee.forget-password.submit');
	Route::post('/submit_reset_password', 'Auth\Mentee\MenteeLoginController@submit_reset_password')->name('mentee.reset-password.submit');
	Route::get('/', 'Mentee\HomeController@index')->name('mentee.dashboard');
	Route::post('logout/', 'Auth\Mentee\MenteeLoginController@logout')->name('mentee.logout');
	Route::get('meeting/list', 'Mentee\MeetingController@index')->name('mentee.meeting.index');
	Route::get('meeting/accept-app', 'Mentee\MeetingController@accept_app_meeting')->name('mentee.meeting.accept.app');
	Route::get('meeting/accept-web', 'Mentee\MeetingController@accept_web_meeting')->name('mentee.meeting.accept.web');
	Route::post('meeting/request-reschedule', 'Mentee\MeetingController@request_reschedule')->name('mentee.meeting.request.reschedule');
	Route::get('chat/userlist', 'Mentee\ChatController@userlist')->name('mentee.chat.userlist');
	Route::get('chat/get_staff_chatcode', 'Mentee\ChatController@get_staff_chatcode')->name('mentee.chat.get_staff_chatcode');
	Route::get('chat/get_mentor_chatcode', 'Mentee\ChatController@get_mentor_chatcode')->name('mentee.chat.get_mentor_chatcode');
	Route::get('chat/message', 'Mentee\ChatController@message')->name('mentee.chat.message');
	Route::match(array('POST','GET'), 'videochat/test', 'Mentee\VideochatController@test')->name('mentee.videochat.test');
	Route::match(array('POST','GET'), 'videochat/initiate', 'Mentee\VideochatController@initiate')->name('mentee.videochat.initiate');

});