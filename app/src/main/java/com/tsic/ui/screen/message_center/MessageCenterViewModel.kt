package com.tsic.ui.screen.message_center


import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.*
import com.tsic.data.model.Status
import com.tsic.data.model.common.MessageCenterResponse
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.data.remote.api.MentorApiService
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.logoutForTnC
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers


class MessageCenterViewModel(private val activity: MessageCenterActivity) {


    var message = ObservableField<List<MessageCenterResponse.Message>>(emptyList())


    private var disposable: Disposable? = null
    private val apiService by lazy { MentorApiService.create() }
    private val apiMenteeService by lazy { MenteeApiService.create() }  // api service will change and have a common api service

    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }


    fun messageCenter() {
        activity.dismissKeyboard()
        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }


        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        val loginType = userPrefs?.getString(KEY_LOGIN_MODE, "")
        if (loginType == KEY_LOGIN_MENTOR)
            disposable = apiService.messageCenter(
                token
            )
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe {
                    activity.isBusyLoadingData(true)
                }
                .doAfterTerminate {
                    activity.isBusyLoadingData(false)
                }
                .subscribe(
                    { result ->
                        if (result.status == Status.SUCCESS) {
                            result?.data?.messages?.let {
                                message.set(it)
                            }

                        } else {
                            if (result.message == "Logged Out") {
                                activity.logoutForTnC()
                            } else {

                                activity.showToast(result.message.toString())
                            }
                        }
                    },
                    { error ->
                        activity.showToast(
                            error.message
                                ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later."
                        )
                    }
                )
        else disposable = apiMenteeService.messageCenter(
            token
        )
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe {
                activity.isBusyLoadingData(true)
            }
            .doAfterTerminate {
                activity.isBusyLoadingData(false)
            }
            .subscribe(
                { result ->
                    if (result.status == Status.SUCCESS) {
                        result?.data?.messages?.let {
                            message.set(it)
                        }

                    } else {
                        if (result.message == "Logged Out") {
                            activity.logoutForTnC()
                        } else {
                            activity.showToast(result.message.toString())
                        }
                    }
                },
                { error ->
                    activity.showToast(
                        error.message
                            ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later."
                    )
                }
            )

    }

    fun dispose() {
        disposable?.dispose()
    }

    fun onPause() = dispose()
    fun onStop() = dispose()
}

