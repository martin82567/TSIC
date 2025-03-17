package com.tsic.ui.screen.mentee_bottom_menu.mymeeting.upcoming

import android.view.View
import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.model.mentee_api.UpcomingMeetingResponse
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.logoutForTnC
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers

class UpcomingMeetingListViewModel(private val fragment: MenteeUpcomingMeetingListFrag) {

    var listMeeting = ObservableField<List<UpcomingMeetingResponse>>(emptyList())

    private var disposable: Disposable? = null
    private val apiService by lazy { MenteeApiService.create() }

    private val userPrefs by lazy {
        fragment.context?.let { PreferenceHelper.customPrefs(it, USER_PREF) }
    }

    fun fetchMenteeUpMeetingList() {
        fragment.activity?.apply {
            dismissKeyboard()
            if (!isDeviceOnline()) {
                fragment.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
                return
            }

            val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
            disposable = apiService.getMenteeUpcomingMeeting(token)
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
                                if (result.data?.listMenteeMeeting?.size == 0) View.VISIBLE else View.GONE
                            result.data?.listMenteeMeeting?.let {
                                listMeeting.set(it)
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
