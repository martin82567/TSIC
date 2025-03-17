package com.tsic.ui.screen.mentee_bottom_menu.myuploads.upload_report

import android.app.Activity
import android.content.Intent
import android.content.SharedPreferences
import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.logoutForTnC
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import org.jetbrains.anko.toast
import java.io.File

class MenteeUploadReportViewModel(private val activity: MenteeUploadReportActivity) {
    var imageUpload =
        ObservableField<String>("camera") //use word "camera" for camera-image as placeholder
    var reportName = ObservableField<String>("")
    private var disposable: Disposable? = null
    private val apiService by lazy { MenteeApiService.create() }


    private val userPrefs: SharedPreferences? by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }

    private val VALID: String = ""

    private fun getFieldsValidity(): String {
        var msg = VALID
        if (reportName.get()?.trim()?.isBlank() == true)
            msg = "Please enter report name"
        if (imageUpload.get()?.trim()?.isBlank() == true)
            msg = "Please add image"




        return msg
    }


    fun uploadReport() {
        activity.dismissKeyboard()
/*        if (imageUpload.get()?.isEmpty() == true || reportName.get()?.isEmpty() == true) {
            activity.showToast("Missing title or report pic")
            return
        }*/


        val validate: String? = getFieldsValidity()

        if (!validate.equals(VALID)) {
            validate?.let { activity.toast(it) }
            return
        }

        if (!activity.isDeviceOnline()) {
            activity.toast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }


        activity.isBusyLoadingData(true)


        val builder = MultipartBody.Builder()

        builder.setType(MultipartBody.FORM)

        val picUrl = imageUpload.get() ?: ""
        if (picUrl.startsWith("/")) {
            val file = File(imageUpload.get() ?: "")

            builder.addFormDataPart(
                "image",
                file.name ?: "",
                file.asRequestBody("multipart/form-data".toMediaType())
            )
        }

        builder.addFormDataPart("name", reportName.get().toString())
        val requestBody = builder.build()
        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")

        disposable =
            apiService.createMenteeReport(token, requestBody)
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe { activity.isBusyLoadingData(true) }
                .doAfterTerminate { activity.isBusyLoadingData(false) }
                .subscribe(
                    { result ->
                        if (result.status == Status.SUCCESS) {
                            activity.toast("Successfull")
                            activity.setResult(Activity.RESULT_OK, Intent())
                            activity.finish()
                            activity?.dismissKeyboard()

                        } else {
                            activity.isBusyLoadingData(false)
                            if (result.message == "Logged Out") {
                                activity.logoutForTnC()
                            } else {
                                activity.showToast(result.message.toString())
                            }
//                            activity.toast(result.message.toString())
                        }
                    },
                    { error ->
                        activity.toast("Some error occured.")
                    }
                )
    }

    fun dispose() {
        disposable?.dispose()
    }

    // fun onResume() = activity.showToast("View model resumed")

    fun onPause() = dispose()
    fun onStop() = dispose()

}