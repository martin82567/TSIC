package com.tsic.data.remote.api

/**
 * @author Kaiser Perwez
 */

import android.util.Log
import com.tsic.BuildConfig
import com.tsic.data.model.BaseResponse
import com.tsic.data.model.login_api.UserCheckModel
import com.tsic.data.model.login_api.UserCheckResponseDetails
import com.tsic.data.model.login_api.UnifiedUserLoginModel
import com.tsic.data.model.login_api.UserResponseDetails
import io.reactivex.Observable
import okhttp3.Interceptor
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.adapter.rxjava2.RxJava2CallAdapterFactory
import retrofit2.converter.gson.GsonConverterFactory
import retrofit2.http.Body
import retrofit2.http.Field
import retrofit2.http.FormUrlEncoded
import retrofit2.http.POST
import java.util.concurrent.TimeUnit

interface UnifiedApiService {

    @POST("api/v1/check_user")
    fun checkUser(@Body checkModel: UserCheckModel): Observable<BaseResponse<com.tsic.data.model.mentee_api.UserDetails?>>


    @POST("api/v1/unified/login")
    fun login(@Body loginModel: UnifiedUserLoginModel): Observable<BaseResponse<UserResponseDetails>>


    @POST("api/v1/unified/forgotpassword")
    @FormUrlEncoded
    fun forgetPassword(@Field("email") email: String): Observable<BaseResponse<Any>>

    @POST("api/v1/unified/resetpassword")
    @FormUrlEncoded
    fun resetPassword(
        @Field("email") email: String,
        @Field("password") password: String,
        @Field("otp") otp: String
    ): Observable<BaseResponse<Any>>


    companion object {

        fun create(baseUrl: String = BASE_URL): UnifiedApiService {


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

            return retrofit!!.create(UnifiedApiService::class.java)
        }
    }
}
