package com.tsic.data.remote.api

/**
 * @author Kaiser Perwez
 */

import android.util.Log
import com.tsic.BuildConfig
import com.tsic.data.model.AppVersion
import com.tsic.data.model.BaseResponse
import com.tsic.data.model.common.DisclaimerModel
import com.tsic.data.model.common.MessageCenterResponse
import com.tsic.data.model.common.SystemMessage
import com.tsic.data.model.common.TimeZoneModel
import com.tsic.data.model.mentee_api.ChangePasswordModel
import com.tsic.data.model.mentee_api.ChatResponse
import com.tsic.data.model.mentee_api.NewChangePasswordModel
import com.tsic.data.model.mentee_api.UserLoginModel
import com.tsic.data.model.mentor_api.*
import io.reactivex.Observable
import okhttp3.Interceptor
import okhttp3.MultipartBody
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.adapter.rxjava2.RxJava2CallAdapterFactory
import retrofit2.converter.gson.GsonConverterFactory
import retrofit2.http.*
import java.util.concurrent.TimeUnit

interface MentorApiService {


    @GET("api/header")
    fun fetchDataSample(): Observable<BaseResponse<String>>

    //SAMPLES . METHOD-->GET

    @GET("api/header")
    fun getByHeader(@Header("Authorizations") token: String? = ""): Observable<BaseResponse<Any>>

    @GET("api/query")
    fun getByQuery(@Query("id") id: String): Observable<BaseResponse<Any>>

    @GET("api/path/{ID}")
    fun getByPath(@Path("ID") jobId: String): Observable<BaseResponse<Any>>


    //SAMPLES . METHOD-->POST
    @POST("api/asModelBody")
    fun postByModelBody(@Body requestModel: Any): Observable<BaseResponse<Any>>

    @POST("api/asParamKeys")
    @FormUrlEncoded
    fun postAsParamKeys(@Field("key") key: String): Observable<BaseResponse<Any>>

    @POST("api/multipart")
    fun postAsMultipart(
        @Header("Authorizations") token: String?,
        @Body requestBody: MultipartBody
    ): Observable<BaseResponse<Array<Any>>>

    @POST("api/v1/mentor/createsession")
    @FormUrlEncoded
    fun addSession(
        @Header("Authorizations") token: String? = "",
        @Field("name") notes: String,
        @Field("schedule_date") date: String,
        @Field("mentee_id") mentee_id: String,
        @Field("meeting_id") meeting_id: String,
        @Field("time_duration") timeDuration: String,
        @Field("session_method_location_id") session_method_location_id: String,
        @Field("type") type: String,
        @Field("no_show") noShow: Int
    ): Observable<BaseResponse<Any>>

    @POST("api/v1/mentor/addmeeting")
    @FormUrlEncoded
    fun addMeeting(
        @Header("Authorizations") token: String? = "",
        @Field("title") title: String,
        @Field("description") description: String = "",
        @Field("session_method_location_id") session_method_location_id: String,
        @Field("school_space") schoolSpace: String = "",
        @Field("school_id") schoolId: String,
        @Field("school_type") school_type: String,
        @Field("date") date: String,
        @Field("time") time: String,
        @Field("mentee_id") mentee_id: String
    ): Observable<BaseResponse<CreateMeeting.Meeting>>

    @POST("api/v1/mentor/addmeeting")
    @FormUrlEncoded
    fun rescheduleMeeting(
        @Header("Authorizations") token: String? = "",
        @Field("id") meeting_id: String,
        @Field("mentee_id") mentee_id: String,
        @Field("date") date: String,
        @Field("time") time: String
    ): Observable<BaseResponse<List<Any>>>

    @POST("api/v1/mentor/creategoaltaskchallenge")
    @FormUrlEncoded
    fun addGoal(
        @Header("Authorizations") token: String? = "",
        @Field("name") title: String,
        @Field("description") description: String,
        @Field("start_date") dateFrom: String,
        @Field("end_date") dateTo: String,
        @Field("type") type: String = "goal"
    ): Observable<BaseResponse<Any>>

    @POST("api/v1/mentor/creategoaltaskchallenge")
    @FormUrlEncoded
    fun addTask(
        @Header("Authorizations") token: String? = "",
        @Field("name") title: String,
        @Field("description") description: String,
        @Field("start_date") dateFrom: String,
        @Field("end_date") dateTo: String,
        @Field("type") type: String = "task"
    ): Observable<BaseResponse<Any>>


    @POST("api/v1/mentor/creategoaltaskchallenge")
    @FormUrlEncoded
    fun addChallenge(
        @Header("Authorizations") token: String? = "",
        @Field("name") title: String,
        @Field("description") description: String,
        @Field("start_date") dateFrom: String,
        @Field("end_date") dateTo: String,
        @Field("type") type: String = "challenge"
    ): Observable<BaseResponse<Any>>

    @POST("api/v1/mentor/creategoaltaskchallenge")
    @FormUrlEncoded
    fun editChallenge(
        @Header("Authorizations") token: String? = "",
        @Field("id") id: String? = " ",
        @Field("name") title: String,
        @Field("description") description: String,
        @Field("start_date") dateFrom: String,
        @Field("end_date") dateTo: String,
        @Field("type") type: String = "challenge"
    ): Observable<BaseResponse<Any>>


    @POST("api/v1/mentor/menteelist")
    fun fetchMenteeList(
        @Header("Authorizations") token: String? = " "
    ): Observable<BaseResponse<List<MentorMyMenteeModel>>>


    @POST("api/v1/mentor/stafflist")
    fun fetchStaffList(
        @Header("Authorizations") token: String? = " "
    ): Observable<BaseResponse<List<MentorMyStaffModel>>>


    @GET("api/v1/mentor/schoollist")
    fun fetchSchoolList(
        @Header("Authorizations") token: String? = " "
    ): Observable<BaseResponse<List<SchoolItem>>>

    @POST("api/v1/mentor/get_session_method_location")
    @FormUrlEncoded
    fun getSessionMethodLocationList(
        @Header("Authorizations") token: String? = " ",
        @Field("mentee_id") mentee_id: String? = " "
    ): Observable<BaseResponse<SessionMethodLocationList>>

    @POST("api/v1/mentor/staff_chat")
    @FormUrlEncoded
    fun getStaffChatMessages(
        @Header("Authorizations") token: String? = "",
        @Field("staff_id") staffId: String,
        @Field("page") page: String = "0",
        @Field("take") take: String = "15"
    ):
            Observable<ChatResponse>

    @POST("api/v1/mentor/mentee_chat")
    @FormUrlEncoded
    fun getMenteeChatMessages(
        @Header("Authorizations") token: String? = "",
        @Field("mentee_id") menteeId: String,
        @Field("page") page: String = "0",
        @Field("take") take: String = "15"
    ):
            Observable<ChatResponse>

    @POST("api/v1/mentor/login")
    fun loginMentor(@Body loginModel: UserLoginModel): Observable<BaseResponse<MentorLoginResponseDetails>>

    @POST("api/v1/mentor/testlogin")
    fun loginTestApiCall(): Observable<BaseResponse<Any>>

    @GET("api/v1/mentor/my-profile")
    fun getMentorProfileData(@Header("Authorizations") token: String? = ""):
            Observable<MentorMyProfile>

    @GET("api/v1/mentor/upcoming_accepted_meeting")
    fun getMentorAcceptedMeeting(@Header("Authorizations") token: String? = ""):
            Observable<BaseResponse<List<AcceptedMeeting>>>

    @POST("api/v1/mentor/save-profile")
    fun editProfile(
        @Header("Authorizations") token: String?,
        @Body requestBody: MultipartBody
    ): Observable<BaseResponse<MentorMyProfileDetails>>


    @POST("api/v1/mentor/listsession")
    fun getSessionList(
        @Header("Authorizations") token: String?
    ): Observable<BaseResponse<List<SessionResponse?>?>>


    @POST("api/v1/mentor/searchelearning")
    fun getLearningList(
        @Header("Authorizations") token: String?
    ): Observable<BaseResponse<ELearningResponse>>

    @POST("api/v1/mentor/listgoaltaskchallenge")
    @FormUrlEncoded
    fun getGoalList(
        @Header("Authorizations") token: String?,
        @Field("type") type: String = "goal"
    ): Observable<BaseResponse<MentorGoalListResponseModel>>

    @POST("api/v1/mentor/listgoaltaskchallenge")
    @FormUrlEncoded
    fun getTaskList(
        @Header("Authorizations") token: String?,
        @Field("type") type: String = "task"
    ): Observable<BaseResponse<MentorGoalListResponseModel>>

    @POST("api/v1/mentor/listgoaltaskchallenge")
    @FormUrlEncoded
    fun getChallengeList(
        @Header("Authorizations") token: String?,
        @Field("type") type: String = "challenge"
    ): Observable<BaseResponse<MentorGoalListResponseModel>>

    @POST("api/v1/mentor/listmenteegoaltaskchallenge")
    @FormUrlEncoded
    fun getMenteeGoalList(
        @Header("Authorizations") token: String? = "",
        @Field("goaltask_id") goaltask_id: String? = "",
        @Field("type") type: String = "goal"
    ): Observable<BaseResponse<AssignedMenteeList>>


    @POST("api/v1/mentor/assigngoaltaskchallenge")
    @FormUrlEncoded
    fun getAssignedGoal(
        @Header("Authorizations") token: String?,
        @Field("goaltask_id") goaltask_id: String,
        @Field("mentee_id") mentee_id: String,
        @Field("type") type: String = "goal"
    ): Observable<BaseResponse<MentorAssignedGoalListResponseModel>>


    @POST("api/v1/mentor/menteereports")
    @FormUrlEncoded
    fun getReport(
        @Header("Authorizations") token: String?,
        @Field("mentee_id") mentee_id: String
    ): Observable<BaseResponse<List<MentorReport>>>


    @POST("api/v1/mentor/assigngoaltaskchallenge")
    @FormUrlEncoded
    fun getAssignedTask(
        @Header("Authorizations") token: String?,
        @Field("goaltask_id") goaltask_id: String,
        @Field("mentee_id") mentee_id: String,
        @Field("type") type: String = "task"
    ): Observable<BaseResponse<MentorAssignedGoalListResponseModel>>

    @POST("api/v1/mentor/assigngoaltaskchallenge")
    @FormUrlEncoded
    fun getAssignedChallenge(
        @Header("Authorizations") token: String?,
        @Field("goaltask_id") goaltask_id: String,
        @Field("mentee_id") mentee_id: String,
        @Field("type") type: String = "challenge"
    ): Observable<BaseResponse<MentorAssignedGoalListResponseModel>>


    @POST("api/v1/mentor/delmenteegoaltaskchallenge")
    @FormUrlEncoded
    fun deleteAssignedGoal(
        @Header("Authorizations") token: String?,
        @Field("goaltask_id") goaltask_id: String,
        @Field("mentee_id") mentee_id: String,
        @Field("type") type: String = "goal"
    ): Observable<BaseResponse<Any>>


    @POST("api/v1/mentor/delmenteegoaltaskchallenge")
    @FormUrlEncoded
    fun deleteAssignedTask(
        @Header("Authorizations") token: String?,
        @Field("goaltask_id") goaltask_id: String,
        @Field("mentee_id") mentee_id: String,
        @Field("type") type: String = "Task"
    ): Observable<BaseResponse<Any>>


    @POST("api/v1/mentor/delmenteegoaltaskchallenge")
    @FormUrlEncoded
    fun deleteAssignedChallenge(
        @Header("Authorizations") token: String?,
        @Field("goaltask_id") goaltask_id: String,
        @Field("mentee_id") mentee_id: String,
        @Field("type") type: String = "challenge"
    ): Observable<BaseResponse<Any>>


    @POST("api/v1/mentor/listmeeting")
    @FormUrlEncoded
    fun getPastMeeting(
        @Header("Authorizations") token: String?,
        @Field("type") type: String = "past"
    ): Observable<BaseResponse<List<MentorPastMeeting>>>

    @POST("api/v1/mentor/logged_meeting")
    @FormUrlEncoded
    fun getViewSessionLog(
        @Header("Authorizations") token: String?,
        @Field("page") page: String = "",
        @Field("take") take: String = "10",
    ): Observable<BaseResponse<List<MentorPastMeeting>>>

    @POST("api/v1/mentor/listmeeting")
    @FormUrlEncoded
    fun getRequestedMeeting(
        @Header("Authorizations") token: String?,
        @Field("type") type: String = "requested"
    ): Observable<BaseResponse<List<MentorPastMeeting>>>


    @POST("api/v1/mentor/cancel_meeting")
    @FormUrlEncoded
    fun cancelMeeting(
        @Header("Authorizations") token: String?,
        @Field("meeting_id") meeting_id: String
    ): Observable<BaseResponse<Any>>

    @POST("api/v1/mentor/no_reschedule_meeting")
    @FormUrlEncoded
    fun noRescheduleMeeting(
        @Header("Authorizations") token: String?,
        @Field("meeting_id") meeting_id: String
    ): Observable<BaseResponse<Any>>


    @POST("api/v1/listreport")
    fun getMenteeReportList(
        @Header("Authorizations") token: String?
    ): Observable<BaseResponse<List<MenteeReportList?>?>>


    @POST("api/v1/mentor/forgotpassword")
    @FormUrlEncoded
    fun getOtpAtEmail(
        @Field("email") name: String
    ): Observable<BaseResponse<Any>>

    @GET("api/v1/mentor/system-messaging/index")
    fun systemMessaging(): Observable<BaseResponse<SystemMessage>>

    @GET("api/disclaimer/index")
    fun disclaimer(): Observable<DisclaimerModel>

    @GET("api/v1/mentor/faq/index")
    fun getFAQ(@Header("Authorizations") token: String?): Observable<MentorFAQModel>

    @POST("api/v1/mentor/resetpassword")
    @FormUrlEncoded
    fun sendOtp(
        @Field("email") email: String,
        @Field("password") password: String,
        @Field("otp") otp: String
    ): Observable<BaseResponse<Any>>

    @GET("api/v1/mentor/todaymeeting")
    fun fetchTodayMeetings(@Header("Authorizations") token: String): Observable<BaseResponse<List<TodaysMeetingModel>>>

    @POST("api/v1/mentor/logout")
    fun logoutUser(@Header("Authorizations") token: String? = ""): Observable<BaseResponse<Array<Any>>>

    @POST("api/v1/mentor/message-center/index")
    fun messageCenter(@Header("Authorizations") token: String? = ""): Observable<BaseResponse<MessageCenterResponse>>

    @GET("api/v1/mentor/get_timezone")
    fun fetchTimeZone(@Header("Authorizations") token: String? = ""): Observable<BaseResponse<TimeZoneModel>>

    @GET("api/v1/mentor/alllistmeeting")
    fun fetchAllListMeeting(@Header("Authorizations") token: String? = ""): Observable<MentorAllListMeeting>

    @GET("api/getVersionCode")
    fun getVersionCode(): Observable<BaseResponse<AppVersion>>

    @POST("api/v1/mentor/update_password")
    fun newChangePassword(
        @Header("Authorizations") token: String?,
        @Body changePasswordModel: NewChangePasswordModel
    ): Observable<BaseResponse<Any>>

    @POST("api/v1/mentor/update_password")
    fun changePassword(
        @Header("Authorizations") token: String?,
        @Body changePasswordModel: ChangePasswordModel
    ): Observable<BaseResponse<Any>>

    @POST("api/v1/mentor/delete_meeting")
    fun deleteSession(
        @Header("Authorizations") token: String?,
        @Body deleteSessionModel: DeleteSessionModel
    ): Observable<BaseResponse<Any>>

    companion object {

        fun create(baseUrl: String = BASE_URL): MentorApiService {


            var retrofit: Retrofit? = null

            retrofit ?: let {


                val builder = OkHttpClient.Builder()

                val client = builder
                    .connectTimeout(500, TimeUnit.SECONDS)
                    .writeTimeout(500, TimeUnit.SECONDS)
                    .readTimeout(500, TimeUnit.SECONDS)

                builder.addInterceptor(Interceptor { chain ->
                    val request = chain.request().newBuilder()
                        .addHeader("app_version", BuildConfig.VERSION_NAME)
                        .addHeader("platform", "android").build()
                    Log.d("Mytag", "create: ${request.headers} ")

                    chain.proceed(request)
                })

                if (BuildConfig.DEBUG) {
                    val interceptor = HttpLoggingInterceptor().apply {
                        setLevel(HttpLoggingInterceptor.Level.BODY)
                    }
                    builder.addInterceptor(interceptor)
                }

                retrofit = Retrofit.Builder()
                    .client(
                        client
                            .build()
                    )
                    .addCallAdapterFactory(
                        RxJava2CallAdapterFactory.create()
                    )
                    .addConverterFactory(
                        GsonConverterFactory.create()
                    )
                    .baseUrl(baseUrl)
                    .build()
            }

            return retrofit!!.create(MentorApiService::class.java)
        }
    }

}
