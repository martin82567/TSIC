package com.tsic.ui.screen.mentor_drawer_menu.meetings.view_session_log

import android.view.View
import androidx.databinding.ObservableField
import androidx.recyclerview.widget.LinearLayoutManager
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.model.mentor_api.MentorPastMeeting
import com.tsic.data.remote.api.MentorApiService
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.logoutForTnC
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers

class ViewSessionLogViewModel(private val activity: ViewSessionLogActivity) {

    var listPastMeeting = ObservableField<List<MentorPastMeeting>>(emptyList())

    private var disposable: Disposable? = null
    private val apiService by lazy { MentorApiService.create() }
    var currentPage = 0
    var isCalling = false
    var tempList = mutableListOf<MentorPastMeeting?>()

    private val userPrefs by lazy {
        activity?.let { PreferenceHelper.customPrefs(it, USER_PREF) }
    }

    fun fetchViewSessionLogList() {
        activity?.apply {
            dismissKeyboard()
            if (!isDeviceOnline()) {
                showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
                return
            }

            val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
            disposable = apiService.getViewSessionLog(token,currentPage.toString())
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe {
                    isBusyLoadingData(true)
                }
                .doAfterTerminate {
                    isBusyLoadingData(false)
                }
                .subscribe(
                    { result ->
                        if (result.status == Status.SUCCESS) {
                            isCalling = false
                            if (currentPage == 0)
                                tempList.clear()
                            result.data?.let { tempList.addAll(it) }
                            activity?.adapter?.notifyDataSetChanged()
                            /*binding?.rvSession?.apply {
                                layoutManager = LinearLayoutManager(this.context)
                                setHasFixedSize(true)
                                tempList?.size?.let { setItemViewCacheSize(it) }
                                adapter =
                                    tempList?.let { ViewSessionLogListAdapter(it, activity) }
                            }*/

                        } else {
                            if (result.message == "Logged Out") {
                                activity?.logoutForTnC()
                            } else {
                                result.message?.let { showToast(it) }
                            }
                            isBusyLoadingData(false)
                        }
                    },
                    { error ->
                        showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
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
