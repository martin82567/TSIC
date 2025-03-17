package com.tsic.ui.screen.mentee_bottom_menu.mymeeting


import android.os.Build
import android.view.View
import androidx.annotation.RequiresApi
import androidx.databinding.ObservableField
import androidx.recyclerview.widget.LinearLayoutManager
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.model.mentee_api.MenteeAllList
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.adapter.MenteeAllRequestedMeetingListAdapter
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.adapter.UpcomingAllMeetingListAdapter
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.logoutForTnC
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers
import org.jetbrains.anko.indeterminateProgressDialog
import java.time.LocalDate
import java.time.format.DateTimeFormatter

class MenteeMyMeetingViewModel(private val activity: MenteeMyMeetingActivity) {

    var varString = ObservableField("")
    var varInt = ObservableField(0)
    var varList = ObservableField<List<String>>(emptyList())

    var varExtraString = ""
    var upcomingDateVsDataMap = mutableMapOf<LocalDate?, ArrayList<MenteeAllList.Data.Upcoming?>>()
    var requestedDateVsDataMap = mutableMapOf<LocalDate?, ArrayList<MenteeAllList.Data.Requested?>>()

    private var disposable: Disposable? = null
    private val apiService by lazy { MenteeApiService.create() }

    var isCardViewDisplayed:Boolean=false


    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }

    @RequiresApi(Build.VERSION_CODES.O)
    fun fetchAllData() {
        activity.dismissKeyboard()
        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        val dialog = activity.indeterminateProgressDialog("Loading data...").apply {
            setCancelable(false)
        }
        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.fetchAllListMeeting(token)
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
                        upcomingDateVsDataMap = mutableMapOf<LocalDate?, ArrayList<MenteeAllList.Data.Upcoming?>>()
                        requestedDateVsDataMap = mutableMapOf<LocalDate?, ArrayList<MenteeAllList.Data.Requested?>>()
                        result.data?.let {
                            activity.binding?.contentLayout?.rvAwaitingMenteeConfirmation?.apply {
                                layoutManager = LinearLayoutManager(
                                    activity,
                                    LinearLayoutManager.HORIZONTAL,
                                    false
                                )
                                adapter =
                                    result.data.requested?.let {
                                        if (it.size!=0) {
                                            it.iterator().forEach {
                                                val key = LocalDate.parse(
                                                    it?.scheduleTime,
                                                    DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss")
                                                )
                                                var list: ArrayList<MenteeAllList.Data.Requested?>
                                                if (requestedDateVsDataMap.containsKey(key)) {
                                                    list = requestedDateVsDataMap.get(key)!!
                                                    list.add(it)
                                                }
                                                else{
                                                    list = ArrayList()
                                                    list.add(it)
                                                }
                                                requestedDateVsDataMap.put(
                                                    key, list
                                                )
                                                if(!isCardViewDisplayed) {

                                                    activity.binding?.contentLayout?.tvAwaitingMenteeConfirmation?.visibility =
                                                        View.GONE
                                                    activity.binding?.contentLayout?.tvAwaitingMenteeConfirmationViewAll?.visibility =
                                                        View.GONE
                                                    activity.binding?.contentLayout?.rvAwaitingMenteeConfirmation?.visibility =
                                                        View.GONE
                                                    activity.binding?.contentLayout?.rvAwaitingSessionOccurrence?.visibility =
                                                        View.GONE
                                                }

                                            }
                                            adapter = MenteeAllRequestedMeetingListAdapter(
                                                it,
                                                activity
                                            )
//                                            activity.binding?.contentLayout?.tvAwaitingMenteeConfirmation?.visibility =
//                                                View.VISIBLE
//                                            activity.binding?.contentLayout?.tvAwaitingMenteeConfirmationViewAll?.visibility =
//                                                View.VISIBLE
//                                            activity.binding?.contentLayout?.rvAwaitingMenteeConfirmation?.visibility =
//                                                View.VISIBLE
                                        }
                                        MenteeAllRequestedMeetingListAdapter(
                                            it,
                                            activity
                                        )
                                    }
                            }
                            activity.binding?.contentLayout?.rvAwaitingSessionOccurrence?.apply {
                                layoutManager = LinearLayoutManager(
                                    activity,
                                    LinearLayoutManager.HORIZONTAL,
                                    false
                                )

                                adapter =
                                    result.data.upcoming?.let {
                                        if (it.size!=0) {
                                            it.iterator().forEach {
                                                val key = LocalDate.parse(
                                                    it?.scheduleTime,
                                                    DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss")
                                                )
                                                var list: ArrayList<MenteeAllList.Data.Upcoming?>
                                                if (upcomingDateVsDataMap.containsKey(key)) {
                                                    list = upcomingDateVsDataMap.get(key)!!
                                                    list.add(it)

                                                }
                                                else{
                                                    list = ArrayList()
                                                    list.add(it)
                                                }
                                                upcomingDateVsDataMap.put(
                                                    key, list
                                                )
                                            }
                                            if(!isCardViewDisplayed) {

                                                activity.binding?.contentLayout?.tvAwaitingSessionOccurrence?.visibility =
                                                    View.GONE
                                                activity.binding?.contentLayout?.tvAwaitingSessionOccurrenceViewAll?.visibility =
                                                    View.GONE
                                                activity.binding?.contentLayout?.rvAwaitingSessionOccurrence?.visibility =
                                                    View.GONE
                                            }

                                        }
                                        UpcomingAllMeetingListAdapter(
                                            it,
                                            activity
                                        )
                                    }
                            }
                            /* activity.binding?.contentLayout?.rvScheduledSessionPassedViewAll?.apply {
                                layoutManager = LinearLayoutManager(
                                    activity,
                                    LinearLayoutManager.HORIZONTAL,
                                    false
                                )

                                adapter = result.data.past?.let {
                                    MentorAllPastMeetingListAdapter(it)
                                }
                            }*/
                        }
                        activity.binding?.contentLayout?.calendarLayout?.calendarView?.notifyCalendarChanged()
                    } else {
                        if (result.message == "Logged Out") {
                            activity.logoutForTnC()
                        } else {
                            activity.showToast(result.message)
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

