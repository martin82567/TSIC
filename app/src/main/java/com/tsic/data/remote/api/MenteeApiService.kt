package com.tsic.data.remote.api

/**
 * @author Kaiser Perwez
 */

import android.util.Log
import com.tsic.BuildConfig
import com.tsic.data.model.AccessToken
import com.tsic.data.model.BaseResponse
import com.tsic.data.model.InitVideoChat
import com.tsic.data.model.common.MessageCenterResponse
import com.tsic.data.model.common.SystemMessage
import com.tsic.data.model.common.TimeZoneModel
import com.tsic.data.model.mentee_api.*
import com.tsic.data.model.mentor_api.MentorAllListMeeting
import com.tsic.data.model.mentor_api.MentorFAQModel
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

interface MenteeApiService {


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

    @POST("api/v1/user/check_waiver")
    fun loginMenteeWaiver(@Body loginModel: UserLoginWaiverModel): Observable<BaseResponse<UserResponseDetailsWaiver>>

    @POST("api/v1/mentor/check_waiver")
    fun loginMentorWaiver(@Body loginModel: UserLoginWaiverModel): Observable<BaseResponse<UserResponseDetailsWaiver>>


    @POST("api/v1/user/login")
    fun loginMentee(@Body loginModel: UserLoginModel): Observable<BaseResponse<UserResponseDetails>>

    @POST("api/v1/user/forgotpassword")
    @FormUrlEncoded
    fun forgetPassword(@Field("email") email: String): Observable<BaseResponse<Array<Any>>>

    @POST("api/v1/user/resetpassword")
    fun resetPassword(@Body resetPasswordModel: ForgotPasswordModel): Observable<BaseResponse<Array<Any>>>

    @POST("api/v1/user/update_password")
    fun changePassword(
        @Header("Authorizations") token: String?,
        @Body changePasswordModel: ChangePasswordModel
    ): Observable<BaseResponse<Any>>

    @POST("api/v1/user/update_password")
    fun newChangePassword(
        @Header("Authorizations") token: String?,
        @Body changePasswordModel: NewChangePasswordModel
    ): Observable<BaseResponse<Any>>

    @GET("api/v1/user/logout")
    fun logoutUser(@Header("Authorizations") token: String? = ""): Observable<BaseResponse<Array<Any>>>

    @GET("api/v1/user/get_timezone")
    fun fetchTimeZone(@Header("Authorizations") token: String? = ""): Observable<BaseResponse<TimeZoneModel>>

    @GET("api/v1/user/userdetails")
    fun getUserData(@Header("Authorizations") token: String? = ""): Observable<BaseResponse<UserResponseDetails>>

    @GET("api/v1/user/mentordetails")
    fun getMyMentorData(@Header("Authorizations") token: String? = ""): Observable<BaseResponse<MyMentorResponseDetails>>

    @GET("api/v1/getstaffs")
    fun getMyStaffList(@Header("Authorizations") token: String? = ""): Observable<BaseResponse<MyStaffListResponseDetails>>

    @GET("api/v1/faq/index")
    fun getFAQ(@Header("Authorizations") token: String?): Observable<MenteeFAQModel>

    @POST("api/v1/user/updateuserdetails")
    fun editProfile(
        @Header("Authorizations") token: String?,
        @Body requestBody: MultipartBody
    ): Observable<BaseResponse<UserDetails>>

    @GET("api/v1/getjob")
    fun getJobSearch(
        @Header("Authorizations") token: String? = "",
        @Query("search_text") searchKeyword: String
    ): Observable<BaseResponse<JobListResponseModel>>

    @GET("api/v1/jobdetails/{ID}")
    fun getJobDetails(
        @Header("Authorizations") token: String? = "",
        @Path("ID") jobId: String
    ): Observable<BaseResponse<JobDetailsModel>>

    @GET("api/v1/getappliedjob")
    fun getJobApplied(
        @Header("Authorizations") token: String? = ""
    ): Observable<BaseResponse<JobListResponseModel>>

    @POST("api/v1/job/apply")
    fun applyJob(
        @Header("Authorizations") token: String?,
        @Body requestBody: MultipartBody
    ): Observable<BaseResponse<Array<Any>>>

    @POST("api/v1/user/searchresource")
    fun getResourceSearchList(
        @Header("Authorizations") token: String? = "",
        @Body resourceSearchRequestModel: ResourceSearchRequestModel
    ): Observable<BaseResponse<ResourceSearchResponseModel>>

    @GET("api/v1/user/resourcedetails/{id}")
    fun getResourceDetailsById(
        @Header("Authorizations") token: String? = "",
        @Path("id") resourceId: String = ""
    ): Observable<BaseResponse<ResourceDetailsResponseModel>>

    @POST("api/v1/user/searchelearning")
    @FormUrlEncoded
    fun getLearningSearchList(
        @Header("Authorizations") token: String? = "",
        @Field("search_keyword") searchText: String? = ""
    ):
            Observable<BaseResponse<LearningSearchResponseModel>>

    @POST("api/v1/journal/add")
    @FormUrlEncoded
    fun addJournal(
        @Header("Authorizations") token: String? = "",
        @Field("title") title: String,
        @Field("description") description: String
    ): Observable<JournalListResponse>

    @GET("api/v1/user/elearningdetails/{id}")
    fun getLearningDetailsById(
        @Header("Authorizations") token: String? = "",
        @Path("id") learningId: String
    ):
            Observable<BaseResponse<LearningDetailResponseModel>>

    @GET("api/v1/chat/agencies")
    fun getChatAgencyList(@Header("Authorizations") token: String? = ""): Observable<ChatResponse>

    @GET("api/v1/journal/list")
    fun getJournalList(@Header("Authorizations") token: String? = ""): Observable<JournalListResponse>

    @POST("api/v1/chat/my_chats")
    @FormUrlEncoded
    fun getMentorChatMessages(
        @Header("Authorizations") token: String? = "",
        @Field("mentor_id") mentorId: String = "",
        @Field("page") page: String = "0",
        @Field("take") take: String = "15"
    ): Observable<ChatResponse>


    @GET("api/v1/meeting/list")
    fun getMeetingList(@Header("Authorizations") token: String? = ""):
            Observable<BaseResponse<MeetingListResponse>>


    @GET("api/v1/meeting/upcoming")
    fun getMenteeUpcomingMeeting(
        @Header("Authorizations")
        token: String? = ""
    ):
            Observable<BaseResponse<UpcomingMeeting>>


    @GET("api/v1/meeting/past")
    fun getPastMenteeMeeting(
        @Header("Authorizations")
        token: String? = ""
    ): Observable<BaseResponse<PastMenteeMeetingResponse>>

    @POST("api/v1/meeting/accept")
    @FormUrlEncoded
    fun getRequestedMenteeMeeting(
        @Header("Authorizations") token: String? = "",
        @Field("meeting_id") meeting_id: String = " ",
        @Field("status_id") status_id: String = ""
    ): Observable<BaseResponse<Any>>


    @POST("api/v1/chat/staff_chat")
    @FormUrlEncoded
    fun getStaffChatMessages(
        @Header("Authorizations") token: String? = "",
        @Field("staff_id") staffId: String,
        @Field("page") page: String = "0",
        @Field("take") take: String = "15"
    ):
            Observable<ChatResponse>

    @POST("api/v1/user/getgoaltask")
    @FormUrlEncoded
    fun getPendingGoalData(
        @Header("Authorizations") token: String? = "",
        @Field("type") type: String = "goal",
        @Field("search_text") searchText: String = ""
    ): Observable<BaseResponse<GoalListResponseModel>>

    @POST("api/v1/user/getgoaltask")
    @FormUrlEncoded
    fun getPendingTaskData(
        @Header("Authorizations") token: String? = "",
        @Field("type") type: String = "task",
        @Field("search_text") searchText: String? = ""
    ): Observable<BaseResponse<TaskListResponseModel>>

    @POST("api/v1/user/getgoaltask")
    @FormUrlEncoded
    fun getPendingChallengeData(
        @Header("Authorizations") token: String? = "",
        @Field("type") type: String = "challenge",
        @Field("search_text") searchText: String = ""
    ): Observable<BaseResponse<ChallengeListResponseModel>>

    @POST("api/v1/user/goaltaskcompltelist")
    @FormUrlEncoded
    fun getCompletedGoalData(
        @Header("Authorizations") token: String? = "",
        @Field("type") type: String = "goal",
        @Field("search_text") searchText: String = ""
    ): Observable<BaseResponse<GoalListResponseModel>>

    @POST("api/v1/user/goaltaskcompltelist")
    @FormUrlEncoded
    fun getCompletedTaskData(
        @Header("Authorizations") token: String? = "",
        @Field("type") type: String = "task",
        @Field("search_text") searchText: String = ""
    ): Observable<BaseResponse<TaskListResponseModel>>

    @POST("api/v1/user/goaltaskcompltelist")
    @FormUrlEncoded
    fun getCompletedChallengeData(
        @Header("Authorizations") token: String? = "",
        @Field("type") type: String = "challenge",
        @Field("search_text") searchText: String = ""
    ): Observable<BaseResponse<ChallengeListResponseModel>>

    @POST("api/v1/user/getgoaltaskdetails")
    @FormUrlEncoded
    fun getGoalDetails(
        @Header("Authorizations") token: String? = "",
        @Field("assign_id") assignId: String?,
        @Field("type") type: String? = "goal"
    ): Observable<BaseResponse<ChallengeDetailsModel>>


    @GET("api/v1/user/resourcedetails/{id}")
    fun getResourceDetails(
        @Header("Authorizations") token: String? = "",
        @Path("id") resourceId: String
    ):
    // @Path("id") resourcesId: String):
            Observable<BaseResponse<ResourceDetail>>

    @POST("api/v1/user/getgoaltaskdetails")
    @FormUrlEncoded
    fun getTaskDetails(
        @Header("Authorizations") token: String? = "",
        @Field("assign_id") assignId: String?,
        @Field("type") type: String? = "task"
    ): Observable<BaseResponse<ChallengeDetailsModel>>

    @POST("api/v1/user/getgoaltaskdetails")
    @FormUrlEncoded
    fun getChallengeDetails(
        @Header("Authorizations") token: String? = "",
        @Field("assign_id") assignId: String?,
        @Field("type") type: String? = "challenge"
    ): Observable<BaseResponse<ChallengeDetailsModel>>


    @POST("api/v1/meeting/saverequest")
    @FormUrlEncoded
    fun getRequestedNote(
        @Header("Authorizations") token: String? = "",
        @Field("meeting_id") meetingId: String?,
        @Field("note") note: String?
    ): Observable<BaseResponse<Any>>

    @POST("api/v1/user/actiongoaltask")
    @FormUrlEncoded
    fun actionBeginComplete(
        @Header("Authorizations") token: String? = "",
        @Field("type") type: String?,
        @Field("status") status: String?,
        @Field("id") id: String?
    ): Observable<BaseResponse<Any>>

    @POST("api/v1/user/notesavegoaltask")
    @FormUrlEncoded
    fun sendNote(
        @Header("Authorizations") token: String? = "",
        @Field("type") type: String?,
        @Field("title") title: String?,
        @Field("note") note: String?,
        @Field("id") id: String?
    ): Observable<BaseResponse<NoteAddedModel>>

    @GET("api/v1/user/filedeletegoaltask/{ID}")
    fun deleteMedia(
        @Header("Authorizations") token: String? = "",
        @Path("ID") id: String?
    ): Observable<BaseResponse<Any>>

    @GET("api/v1/system-messaging/index")
    fun systemMessaging(): Observable<BaseResponse<SystemMessage>>


    @POST("api/v1/user/filesavegoaltask")
    fun uploadMedia(
        @Header("Authorizations") token: String?,
        @Body requestBody: MultipartBody
    ): Observable<BaseResponse<UserFileUpload>>

    @POST("api/v1/createreport")
    fun createMenteeReport(
        @Header("Authorizations") token: String?,
        @Body requestBody: MultipartBody
    ): Observable<BaseResponse<Any>>


    @POST("api/v1/user/forgotpassword")
    @FormUrlEncoded
    fun getOtpAtEmail(
        @Field("email") name: String
    ): Observable<BaseResponse<Any>>


    @POST("api/v1/user/creategoaltask")
    @FormUrlEncoded
    fun addSelfGoal(
        @Header("Authorizations") token: String? = "",
        @Field("name") title: String,
        @Field("description") description: String,
        @Field("start_date") dateFrom: String,
        @Field("end_date") dateTo: String,
        @Field("type") type: String = "goal"
    ): Observable<BaseResponse<Any>>

    @POST("api/videochat/initiate_chat")
    @FormUrlEncoded
    fun initiateVideoChat(
        @Field("sender_id") sender_id: String,
        @Field("sender_type") sender_type: String,
        @Field("receiver_id") receiver_id: String,
        @Field("receiver_type") receiver_type: String
    ): Observable<BaseResponse<InitVideoChat>>

    @POST("api/v1/message-center/index")
    fun messageCenter(@Header("Authorizations") token: String? = ""): Observable<BaseResponse<MessageCenterResponse>>

    @POST("api/videochat/generate_room")
    @FormUrlEncoded
    fun getAccessToken(
        @Field("chat_code") chat_code: String,
        @Field("unique_name") unique_name: String,
    ): Observable<BaseResponse<AccessToken>>

    @POST("api/videochat/disconnect_room")
    @FormUrlEncoded
    fun callDisconnect(
        @Field("unique_name") unique_name: String,
        @Field("disconnect_type") disconnect_type: String = "miss_call"
    ): Observable<BaseResponse<Any>>
    @POST("api/videochat/denied_call")
    @FormUrlEncoded
    fun callDenied(
        @Field("unique_name") unique_name: String,
        @Field("denied_by") denied_by: Int,
        @Field("denied_by_type") denied_by_type: String
    ): Observable<BaseResponse<Any>>
    @GET("api/v1/meeting/alllist")
    fun fetchAllListMeeting(@Header("Authorizations") token: String? = ""): Observable<MenteeAllList>

    companion object {

        fun create(baseUrl: String = BASE_URL): MenteeApiService {


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
                    .client(client
                        .build())
                    .addCallAdapterFactory(
                        RxJava2CallAdapterFactory.create()
                    )
                    .addConverterFactory(
                        GsonConverterFactory.create()
                    )
                    .baseUrl(baseUrl)
                    .build()
            }

            return retrofit!!.create(MenteeApiService::class.java)
        }
    }

}
