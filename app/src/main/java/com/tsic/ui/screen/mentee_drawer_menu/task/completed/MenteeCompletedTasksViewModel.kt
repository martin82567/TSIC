package com.tsic.ui.screen.mentee_drawer_menu.task.completed


import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.model.mentee_api.TaskDatalist
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.logoutForTnC
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers

class MenteeCompletedTasksViewModel(private val fragment: MenteeCompletedTasksFrag) {

    var listCompleted = ObservableField<List<TaskDatalist>>(emptyList())


    private var disposable: Disposable? = null
    private val apiService by lazy { MenteeApiService.create() }

    private val userPrefs by lazy {
        fragment.context?.let { PreferenceHelper.customPrefs(it, USER_PREF) }
    }

    fun fetchCompletedTasks() {
        fragment.activity?.apply {
            dismissKeyboard()
            if (!isDeviceOnline()) {
                fragment.showToast("No internet connection.")
                return
            }

            val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
            disposable = apiService.getCompletedTaskData(token)
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe {
                    fragment.isBusyLoadingData(true)
                }
                .doAfterTerminate {
                    fragment.isBusyLoadingData(false)
                }
                .subscribe(
                    { result ->
                        if (result.status == Status.SUCCESS) {
                            result.data?.let {
                                listCompleted.set(result.data.datalist)
                            }
                        } else {
                            if (result.message == "Logged Out") {
                                fragment.activity?.logoutForTnC()
                            } else {
                                fragment.showToast(result.message)
                            }
                        }
                    },
                    { error ->
                        fragment.showToast(
                            error.message
                                ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later."
                        )
                    }
                )
        }
    }

    fun dispose() {
        disposable?.dispose()
    }

    fun onPause() = dispose()
    fun onStop() = dispose()
}