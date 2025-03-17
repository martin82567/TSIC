package com.tsic.ui.screen.changepassword


import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.model.mentee_api.ChangePasswordModel
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.data.remote.api.MentorApiService
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers
import org.jetbrains.anko.indeterminateProgressDialog

class ChangePasswordViewModel(private val activity: ChangePasswordActivity) {


    private val VALID: String = ""


    var current_password = ObservableField("")
    var new_password = ObservableField("")
    var retype_password = ObservableField("")

    private var disposable: Disposable? = null
    private val apiService by lazy { MenteeApiService.create() }
    private val apiServiceMentor by lazy { MentorApiService.create() }

    val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }


    private fun getFieldsValidity(): String {
        var msg = VALID
        val pwd1 = new_password.get()?.trim()
        val pwd2 = retype_password.get()?.trim()
        if (pwd1?.isBlank() == true)
            msg = "Please enter password"
        else if (pwd2?.isBlank() == true)
            msg = "Retype password"
        else if (pwd2?.length!! < 6)
            msg = "Password length must be greater than 5 characters"
        else if (pwd1 != pwd2)
            msg = "password doesn't match"

        return msg
    }


    fun getchangePass() {
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
        val dialog = activity.indeterminateProgressDialog("Please Wait...").apply {
            setCancelable(false)
        }
        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.changePassword(
            token, ChangePasswordModel(
                current_password.get().toString(),
                new_password.get().toString(),
                new_password.get().toString()
            )
        )
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe {
                activity?.runOnUiThread { dialog?.show() }
            }
            .doAfterTerminate {
                activity?.runOnUiThread { dialog?.dismiss() }
            }
            .subscribe(
                { result ->
                    if (result.status == Status.SUCCESS) {
                        activity.showToast("Password Changed")
                        activity?.finish()
                    } else {
                        activity.showToast(result.message.toString())
                    }
                },
                { error ->
                    activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                }
            )
    }

    fun getchangePassMentor() {
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
        val dialog = activity.indeterminateProgressDialog("Please Wait...").apply {
            setCancelable(false)
        }
        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiServiceMentor.changePassword(
            token, ChangePasswordModel(
                current_password.get().toString(),
                new_password.get().toString(),
                new_password.get().toString()
            )
        )
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe {
                activity?.runOnUiThread { dialog?.show() }
            }
            .doAfterTerminate {
                activity?.runOnUiThread { dialog?.dismiss() }
            }
            .subscribe(
                { result ->
                    if (result.status == Status.SUCCESS) {
                        activity.showToast("Password Changed")
                        activity?.finish()
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
