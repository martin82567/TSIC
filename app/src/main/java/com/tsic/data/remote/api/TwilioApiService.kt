package com.tsic.data.remote.api

/**
 * @author Kaiser Perwez
 */

import android.util.Log
import com.tsic.BuildConfig
import com.tsic.data.model.BaseResponse
import com.tsic.data.model.common.TwilioAccessTokenModel
import io.reactivex.Observable
import okhttp3.Interceptor
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.adapter.rxjava2.RxJava2CallAdapterFactory
import retrofit2.converter.gson.GsonConverterFactory
import retrofit2.http.Field
import retrofit2.http.FormUrlEncoded
import retrofit2.http.POST
import java.util.concurrent.TimeUnit

interface TwilioApiService {

    @POST("api/chat/get_access_token")
    @FormUrlEncoded
    fun fetchAccessToken(
        @Field("user_type") userType: String? = "",
        @Field("user_id") userId: String? = "",
        @Field("user_name") userName: String? = "",
    ): Observable<TwilioAccessTokenModel>

    @POST("api/chat/channel_id_update")
    @FormUrlEncoded
    fun saveChannelSid(
        @Field("channel_sid") channelSid: String? = "",
        @Field("chat_code") chatCode: String? = "",
        @Field("chat_type") chatType: String? = "",
    ): Observable<BaseResponse<Any>>

    @POST("api/chat/send_nofication")
    @FormUrlEncoded
    fun sendNotification(
        @Field("sender_name") senderName: String? = "",
        @Field("comes_from") comes_from: String? = "",
        @Field("user_type") userType: String? = "",
        @Field("user_id") userId: String? = "",
        @Field("message") message: String? = "",
    ): Observable<BaseResponse<Any>>

    companion object {

        fun create(baseUrl: String = BASE_URL): TwilioApiService {

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

            return retrofit!!.create(TwilioApiService::class.java)
        }
    }

}
