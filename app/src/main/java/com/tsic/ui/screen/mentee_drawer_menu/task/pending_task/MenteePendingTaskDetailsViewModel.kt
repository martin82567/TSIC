package com.tsic.ui.screen.mentee_drawer_menu.task.pending_task


import androidx.databinding.ObservableField
import com.jaiselrahman.filepicker.model.MediaFile
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.model.mentee_api.DataDetail
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
import java.util.*

class MenteePendingTaskDetailsViewModel(private val activity: MenteePendingTaskDetailsActivity) {

    private var disposable: Disposable? = null
    private val apiService by lazy { MenteeApiService.create() }

    val details = ObservableField(DataDetail())

    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }
    val token by lazy { userPrefs?.getString(KEY_AUTH_TOKEN, "") }


    fun getTaskDetails(taskId: String) {
        activity.dismissKeyboard()
        if (!activity.isDeviceOnline()) {
            activity.showToast("No internet connection.")
            return
        }

        disposable = apiService.getTaskDetails(token, taskId)
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
                        result.data?.datadetails?.apply {
                            details.set(this)
                            activity.initGoalSubmitButton()
                        }
                    } else {
                        if (result.message == "Logged Out") {
                            activity.logoutForTnC()
                        } else {
                            activity.showToast(result.message)
                        }
                    }
                },
                { error ->
                    activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                }
            )
    }

    fun modifyTaskStatus() {

        if (!activity.isDeviceOnline()) {
            activity.toast("No internet connection.")
            return
        }

        disposable =
            apiService.actionBeginComplete(
                token,
                "task",
                "${details.get()?.datastatus?.plus(1)}",
                details.get()?.id.toString()
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
                        if (result.status) {
                            activity.showToast(result.message.toString())
                            getTaskDetails(activity.taskId)
                        }
                        else{
                            if (result.message == "Logged Out") {
                                activity.logoutForTnC()
                            } else {
                                activity.showToast(result.message)
                            }
                        }
                    },
                    { error ->
                        activity.showToast("Some error occurred. Please try again")
                    }
                )

    }

    fun uploadFile(listPics: ArrayList<MediaFile>?) {
        if (!activity.isDeviceOnline()) {
            activity.toast("No internet connection.")
            return
        }

        val builder = MultipartBody.Builder()

        builder.setType(MultipartBody.FORM)
        builder.addFormDataPart("type", "task")
        builder.addFormDataPart("id", details.get()?.id.toString())

        listPics?.forEach {
            var file = File(it.path)
            builder.addFormDataPart(
                "files[]",
                file.name,
                file.asRequestBody("multipart/form-data".toMediaType())
            )
        }

        val requestBody = builder.build()

        disposable =
            apiService.uploadMedia(
                token,
                requestBody
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
                        if (result.status) {
                            getTaskDetails(details.get()?.assignId?.toString() ?: "")
                        }
                        else{
                            if (result.message == "Logged Out") {
                                activity.logoutForTnC()
                            } else {
                                activity.showToast(result.message)
                            }
                        }
                    },
                    { error ->
                        activity.showToast("Some error occurred. Please try again")
                    }
                )
    }

    fun dispose() {
        disposable?.dispose()
    }

    fun onPause() = dispose()
    fun onStop() = dispose()
}
