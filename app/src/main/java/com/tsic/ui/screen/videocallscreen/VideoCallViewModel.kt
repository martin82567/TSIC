package com.tsic.ui.screen.videocallscreen

import android.util.Log
import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.*
import com.tsic.data.model.Status
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers

class VideoCallViewModel(private val activity: VideoCallActivity) {

    var callStatus = ObservableField("Calling...")
    var varInt = ObservableField(0)
    var varList = ObservableField<List<String>>(emptyList())
    var accessToken = ""
    val roomUserData = mutableListOf<String>()
    var receiverId = ""
    var receiverType = ""
    var uniqueName = ""
    var isReceiver = true
    var isDisconnectCall = false
    var type = "miss_call"
    private var disposable: Disposable? = null
    private val apiService by lazy { MenteeApiService.create() }


    val userPrefs by lazy {
        PreferenceHelper.getSharedPrefs(activity)
    }

    fun getAccessToken() {
        activity.dismissKeyboard()
        if (!activity.isDeviceOnline()) {
            activity.showToast("No internet connection.")
            return
        }
        isReceiver = false
        val loginType = userPrefs?.getString(KEY_LOGIN_MODE, "")
        receiverType = if (loginType == KEY_LOGIN_MENTOR) KEY_LOGIN_MENTEE else KEY_LOGIN_MENTOR
        val userId = userPrefs?.getInt(KEY_USER_ID, 0)
        disposable = apiService.initiateVideoChat(
            userId.toString(),
            loginType.toString(),
            receiverId,
            receiverType
        )
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe {
                activity.runOnUiThread { activity.isBusyLoadingData(true) }
            }
            .doAfterTerminate {
                activity.runOnUiThread { activity.isBusyLoadingData(false) }
            }
            .subscribe(
                { result ->
                    if (result.status == Status.SUCCESS) {
                        result.data?.let {
                            activity.totalsec = it.remaining_time.toLong()
                            if (activity.totalsec <= 10) {
                                activity.showToast("Video session Expired")
                                activity.finish()
                            } else {
                                getAccessToken(it.chat_code, it.unique_name)
                            }
                        }
                    } else {
                        activity.showToast(result.message ?: "Status false")
                        activity.finish()
                    }
                },
                { error ->
                    activity.showToast("Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                    activity.finish()
                }
            )
    }

    private fun getAccessToken(chatCode: String, uniqueName: String) {

        activity.dismissKeyboard()
        if (!activity.isDeviceOnline()) {
            activity.showToast("No internet connection.")
            return
        }
        if (isDisconnectCall) return
        isDisconnectCall = true
        activity.btnDisable()
        disposable = apiService.getAccessToken(chatCode, uniqueName)
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe {
                activity.runOnUiThread { activity.isBusyLoadingData(true) }
            }
            .doAfterTerminate {
                activity.runOnUiThread { activity.isBusyLoadingData(false) }
            }
            .subscribe(
                { result ->
                    activity.btnEnable()
                    if (result.status == Status.SUCCESS) {
                        result.data?.let {
                            val t = if (userPrefs?.getString(
                                    KEY_LOGIN_MODE,
                                    ""
                                ) == KEY_LOGIN_MENTOR
                            ) {
                                it.sender_accesstoken
                            } else {
                                it.receiver_accesstoken ?: it.sender_accesstoken
                            }
                            if (t.isNullOrBlank()) {
                                activity.showToast("Failed to connect room")
                                activity.finish()
                                return@let
                            }
                            accessToken = t
                            this.uniqueName = it.unique_name
                            activity.createdAt = it.created_at
                            activity.connectToRoom(this.uniqueName, accessToken)
                            if (activity.callFrom == "Web Call") {

                                roomUserData.addAll(it.unique_name.split("-"))
                                Log.d("TAG", "initUiAndListeners: $roomUserData")
                                activity.apply {
                                    initSocket?.startCalling(
                                        roomUserData[RECEIVER_TYPE],
                                        roomUserData[RECEIVER_ID],
                                        roomUserData[SENDER_TYPE],
                                        roomUserData[SENDER_ID],
                                    )
                                }
                            }

                        }
                    } else {
                        activity.showToast(result.message ?: "Status false")
                        activity.finish()
                    }
                },
                { error ->
                    activity.btnEnable()
                    activity.showToast("Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                    activity.finish()
                }
            )
    }

    fun callDisconnect() {
        activity.dismissKeyboard()
        if (!activity.isDeviceOnline()) {
            activity.showToast("No internet connection.")
            return
        }
        disposable = apiService.callDisconnect(uniqueName, type)
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe {
                activity.runOnUiThread { activity.isBusyLoadingData(true) }
            }
            .doAfterTerminate {
                activity.runOnUiThread { activity.isBusyLoadingData(false) }
            }
            .subscribe(
                { result ->
                    if (result.status == Status.SUCCESS) {
                        activity.finish()
                    } else {
                        //activity?.showToast(result.message ?: "Status false")
                        activity.finish()

                    }
                },
                { error ->
                    //activity?.showToast("Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                    activity.finish()
                }
            )
    }

    fun dispose() {
        disposable?.dispose()
    }

    fun onPause() = dispose()
    fun onStop() = dispose()
}