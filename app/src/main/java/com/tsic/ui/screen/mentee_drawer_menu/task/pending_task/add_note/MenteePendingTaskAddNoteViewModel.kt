package com.tsic.ui.screen.mentee_drawer_menu.task.pending_task.add_note


import android.app.Activity
import android.content.Intent
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
import org.jetbrains.anko.toast

class MenteePendingTaskAddNoteViewModel(private val activity: MenteePendingtaskAddNoteActivity) {

    private var disposable: Disposable? = null
    private val apiService by lazy { MenteeApiService.create() }

    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }

    private val VALID: String = ""
    var title = ObservableField<String>("")
    var description = ObservableField<String>("")


    private fun getFieldsValidity(): String {
        var msg = VALID
        if (title.get()?.trim()?.isBlank() == true)
            msg = "Please enter title"
        if (description.get()?.trim()?.isBlank() == true)
            msg = "Please enter description"

        return msg
    }


    fun saveNote(goalId: String) {
        activity.dismissKeyboard()
        val validate: String? = getFieldsValidity()

        if (!validate.equals(VALID)) {
            validate?.let { activity.toast(it) }
            return
        }

        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        activity.isBusyLoadingData(true)

        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.sendNote(
            token,
            "task",
            title.get() ?: "",
            description.get() ?: "",
            goalId
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
                        title.set("")
                        description.set("")

                        activity.setResult(Activity.RESULT_OK, Intent())
                        activity.dismissKeyboard()
                        activity.finish()
                    } else {
                        if (result.message == "Logged Out") {
                            activity.logoutForTnC()
                        } else {
                            activity.showToast(result.message ?: "")
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
