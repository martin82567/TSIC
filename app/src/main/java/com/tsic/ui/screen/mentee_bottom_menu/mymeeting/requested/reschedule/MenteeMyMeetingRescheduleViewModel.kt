package com.tsic.ui.screen.mentee_bottom_menu.mymeeting.requested.reschedule


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
import org.jetbrains.anko.indeterminateProgressDialog

class MenteeMyMeetingRescheduleViewModel(private val activity: MenteeMyMeetingRescheduleActivity) {

    var note = ObservableField("")
    var varInt = ObservableField(0)
    var varList = ObservableField<List<String>>(emptyList())

    var varExtraString = ""

    private var disposable: Disposable? = null
    private val apiService by lazy { MenteeApiService.create() }

    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }

    fun rescheduleSession(id: String) {
        activity.dismissKeyboard()
        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        val dialog = activity.indeterminateProgressDialog("Please Wait...").apply {
            setCancelable(false)
        }
        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.getRequestedNote(token, id, note.get())
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe {
                activity.runOnUiThread { dialog.show() }
            }
            .doAfterTerminate {
                activity.runOnUiThread { dialog.dismiss() }
            }
            .subscribe(
                { result ->
                    if (result.status == Status.SUCCESS) {
                        result.data?.let {
                            activity.showToast("Reschedule request has been sent successfully")
                            activity.finish()
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

    fun onResume() = dispose()
    fun onPause() = dispose()
    fun onStop() = dispose()
}

