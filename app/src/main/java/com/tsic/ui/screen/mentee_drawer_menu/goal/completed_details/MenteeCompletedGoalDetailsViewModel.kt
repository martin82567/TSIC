package com.tsic.ui.screen.mentee_drawer_menu.goal.completed_details


import androidx.databinding.ObservableField
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

class MenteeCompletedGoalDetailsViewModel(private val activity: MenteeCompletedGoalDetailsActivity) {

    private var disposable: Disposable? = null
    private val apiService by lazy { MenteeApiService.create() }

    val details = ObservableField(DataDetail())

    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }
    val token by lazy { userPrefs?.getString(KEY_AUTH_TOKEN, "") }


    fun getGoalDetails(goalId: String) {
        activity.dismissKeyboard()
        if (!activity.isDeviceOnline()) {
            activity.showToast("No internet connection.")
            return
        }

        disposable = apiService.getGoalDetails(token, goalId)
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

    fun dispose() {
        disposable?.dispose()
    }

    fun onPause() = dispose()
    fun onStop() = dispose()
}
