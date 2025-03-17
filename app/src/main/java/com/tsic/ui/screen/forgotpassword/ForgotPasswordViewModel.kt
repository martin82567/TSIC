package com.tsic.ui.screen.forgotpassword


import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.remote.api.UnifiedApiService
import com.tsic.ui.screen.chooseloginmode.ChooseLoginModeActivity
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers
import org.jetbrains.anko.clearTask
import org.jetbrains.anko.intentFor
import org.jetbrains.anko.newTask


class ForgotPasswordViewModel(private val activity: ForgotPasswordActivity) {

    private val VALID: String = ""

    var email = ObservableField<String>("")
    var password = ObservableField<String>("")
    var newPassword = ObservableField<String>("")
    var otp = ObservableField<String>("")

    private var disposable: Disposable? = null
    private val apiService by lazy { UnifiedApiService.create() }  // api service will change and have a common api service

    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }


    private fun getFieldsValidity(): String {
        var msg = VALID
        if (password.get()?.trim()?.isBlank() == true)
            msg = "Please enter password"
        if (newPassword.get()?.trim()?.isBlank() == true)
            msg = "Please retype pass"
        if (otp.get()?.trim()?.isBlank() == true)
            msg = "Please enter otp"

        return msg
    }


    fun resetPassword() {
        activity.dismissKeyboard()
        val msg = getFieldsValidity()
        if (msg != VALID) {
            activity.showToast(msg)
            return
        }
        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }


        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.resetPassword(
            email.get()?.toString() ?: "",
            password.get()?.toString() ?: "",
            otp.get()?.toString() ?: ""
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
                        activity.startActivity(activity.intentFor<ChooseLoginModeActivity>().clearTask().newTask())

                    } else {
                        activity.showToast(result.message.toString())
                    }
                },
                { error ->
                    activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                }
            )
    }

    fun dispose() {
        disposable?.dispose()
    }
    fun onPause() = dispose()
    fun onStop() = dispose()
}

