package com.tsic.ui.screen.mentee_bottom_menu.mychats.my_mentor_list

import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.model.mentee_api.MyMentorDetails
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.logoutForTnC
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers

class MenteeMyMentorListViewModel(private val activity: MenteeMyMentorListActivity) {

    var listMentor = ObservableField<List<MyMentorDetails>>(emptyList())


    var id = ObservableField("")
    var name = ObservableField<String>("")
    var profilePic = ObservableField<String>("avatar")
    var lastSessionDate = ObservableField<String>("")
    var sessionCount = ObservableField<String>("0")

    private var disposable: Disposable? = null
    private val apiService by lazy { MenteeApiService.create() }

    private val userPrefs by lazy {
        activity.let { PreferenceHelper.customPrefs(it, USER_PREF) }
    }

    fun fetchMyMentorList() {
        activity.apply {
            dismissKeyboard()
            if (!isDeviceOnline()) {
                activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
                return
            }

            val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
            disposable = apiService.getMyMentorData(token)
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
                        if (result.status == Status.SUCCESS) {
                            result.data?.listMyMentor?.let {
                                listMentor.set(it)
                                /*id.set(it[0].id?.toString())
                                name.set("${it[0].firstname} ${it[0].middlename} ${it[0].lastname}")
                                profilePic.set("${MentorApiService.MENTOR_IMAGE_URL}${it[0].imageUser}")
                                lastSessionDate.set("${it[0].last_SessionDate}")
                                sessionCount.set("${it[0].sessionCount}")*/
                            }
                        } else {
                            if (result.message == "Logged Out") {
                                activity.logoutForTnC()
                            } else {
                                activity.showToast(result.message)
                            }
                            activity.isBusyLoadingData(false)
                        }
                    },
                    { error ->
                        activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
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
