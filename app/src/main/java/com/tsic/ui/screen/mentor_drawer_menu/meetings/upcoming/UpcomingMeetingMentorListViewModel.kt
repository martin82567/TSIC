package com.tsic.ui.screen.mentor_drawer_menu.meetings.upcoming

import android.view.View
import androidx.databinding.ObservableField
import androidx.recyclerview.widget.LinearLayoutManager
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.model.mentor_api.AcceptedMeeting
import com.tsic.data.remote.api.MentorApiService
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.logoutForTnC
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers

class UpcomingMeetingMentorListViewModel(private val fragment: MentorUpcomingMeetingListFrag) {

    var listMeeting = ObservableField<List<AcceptedMeeting>>(emptyList())

    private var disposable: Disposable? = null
    private val apiService by lazy { MentorApiService.create() }

    private val userPrefs by lazy {
        fragment.context?.let { PreferenceHelper.customPrefs(it, USER_PREF) }
    }

    fun fetchMentorUpMeetingList() {
        fragment.activity?.apply {
            dismissKeyboard()
            if (!isDeviceOnline()) {
                fragment.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
                return
            }

            val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
            disposable = apiService.getMentorAcceptedMeeting(token)
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
                            fragment?.binding?.noSessionFound?.visibility =
                                if (result.data?.size == 0) View.VISIBLE else View.GONE
                            val tempList: List<AcceptedMeeting>? = result.data
                            fragment.binding?.rVMentee?.apply {
                                layoutManager = LinearLayoutManager(this.context)
                                adapter =
                                    tempList?.let { UpcomingMeetingMentorListAdapter(it, fragment) }
                            }


                        } else {
                            if (result.message == "Logged Out") {
                                fragment?.activity?.logoutForTnC()
                            } else {

                            fragment.showToast(result.message)
                            }
                            fragment.isBusyLoadingData(false)
                        }
                    },
                    { error ->
                        fragment.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
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
