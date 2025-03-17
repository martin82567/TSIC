package com.tsic.ui.screen.mentor_drawer_menu.meetings


import android.os.Build
import android.view.View
import androidx.annotation.RequiresApi
import androidx.databinding.ObservableField
import androidx.recyclerview.widget.LinearLayoutManager
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.model.mentor_api.DeleteSessionModel
import com.tsic.data.model.mentor_api.MentorAllListMeeting
import com.tsic.data.model.mentor_api.MentorPastMeeting
import com.tsic.data.remote.api.MentorApiService
import com.tsic.ui.screen.mentor_drawer_menu.meetings.adapter.AllUpcomingMeetingMentorListAdapter
import com.tsic.ui.screen.mentor_drawer_menu.meetings.adapter.MentorAllPastMeetingListAdapter
import com.tsic.ui.screen.mentor_drawer_menu.meetings.adapter.MentorAllRequestedMeetingListAdapter
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.logoutForTnC
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers
import org.jetbrains.anko.indeterminateProgressDialog
import java.text.SimpleDateFormat
import java.time.LocalDate
import java.time.format.DateTimeFormatter

class MentorMyMeetingViewModel(private val activity: MentorMyMeetingActivity) {

    var varString = ObservableField("")
    var varInt = ObservableField(0)
    var varList = ObservableField<List<String>>(emptyList())

    var upcomingDateVsDataMap = mutableMapOf<LocalDate?, ArrayList<MentorAllListMeeting.Data.Upcoming?>>()
    var pastDateVsDataMap = mutableMapOf<LocalDate?, ArrayList<MentorAllListMeeting.Data.Past?>>()
    var requestedDateVsDataMap = mutableMapOf<LocalDate?, ArrayList<MentorPastMeeting?>>()

    private var disposable: Disposable? = null
    private val apiService by lazy { MentorApiService.create() }
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
                        upcomingDateVsDataMap = mutableMapOf<LocalDate?, ArrayList<MentorAllListMeeting.Data.Upcoming?>>()
                        pastDateVsDataMap = mutableMapOf<LocalDate?, ArrayList<MentorAllListMeeting.Data.Past?>>()
                        requestedDateVsDataMap = mutableMapOf<LocalDate?, ArrayList<MentorPastMeeting?>>()
                        result.data?.let {
                            activity.binding?.contentLayout?.rvAwaitingMenteeConfirmation?.apply {
                                setHasFixedSize(true)
                                layoutManager = LinearLayoutManager(
                                    activity,
                                    LinearLayoutManager.HORIZONTAL,
                                    false
                                )

                                result.data.requested?.let {
                                    if (it.size != 0) {
                                        it.iterator().forEach {
                                            val key = LocalDate.parse(
                                                it?.scheduleTime,
                                                DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss")
                                            )
                                            var list: ArrayList<MentorPastMeeting?>
                                            if (requestedDateVsDataMap.containsKey(key)) {
                                                list = requestedDateVsDataMap.get(key)!!
                                                list.add(it)
                                            } else {
                                                list = ArrayList()
                                                list.add(it)
                                            }
                                            requestedDateVsDataMap.put(
                                                key, list
                                            )
                                        }
                                        if(!isCardViewDisplayed) {

                                            activity.binding?.contentLayout?.tvAwaitingMenteeConfirmation?.visibility =
                                                View.GONE
                                            activity.binding?.contentLayout?.tvAwaitingMenteeConfirmationViewAll?.visibility =
                                                View.GONE
                                            activity.binding?.contentLayout?.rvAwaitingMenteeConfirmation?.visibility =
                                                View.GONE
                                        }
                                    }
                                    adapter = MentorAllRequestedMeetingListAdapter(
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
                                        if (it.size != 0) {
                                            it.iterator().forEach {
                                                val key = LocalDate.parse(
                                                    it?.scheduleTime,
                                                    DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss")
                                                )
                                                var list: ArrayList<MentorAllListMeeting.Data.Upcoming?>
                                                if (upcomingDateVsDataMap.containsKey(key)) {
                                                    list = upcomingDateVsDataMap.get(key)!!
                                                    list.add(it)
                                                } else {
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
                                        AllUpcomingMeetingMentorListAdapter(
                                            it,
                                            activity
                                        )
                                    }
                            }
                            activity.binding?.contentLayout?.rvScheduledSessionPassedViewAll?.apply {
                                layoutManager = LinearLayoutManager(
                                    activity,
                                    LinearLayoutManager.HORIZONTAL,
                                    false
                                )
                                setHasFixedSize(true)
                                setItemViewCacheSize(50)
                                adapter = result.data.past?.let {
                                    if (it.size != 0) {
                                        it.iterator().forEach {
                                            val key = LocalDate.parse(
                                                it?.scheduleTime,
                                                DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss")
                                            )
                                            var list: ArrayList<MentorAllListMeeting.Data.Past?>
                                            if (pastDateVsDataMap.containsKey(key)) {
                                                list = pastDateVsDataMap.get(key)!!
                                                list.add(it)
                                            } else {
                                                list = ArrayList()
                                                list.add(it)
                                            }
                                            pastDateVsDataMap.put(
                                                key, list
                                            )
                                        }
                                        if(!isCardViewDisplayed) {

                                            activity.binding?.contentLayout?.tvScheduledSessionPassed?.visibility =
                                                View.GONE
                                            activity.binding?.contentLayout?.tvScheduledSessionPassedViewAll?.visibility =
                                                View.GONE
                                            activity.binding?.contentLayout?.rvScheduledSessionPassedViewAll?.visibility =
                                                View.GONE
                                        }
                                    }
                                    MentorAllPastMeetingListAdapter(it)
                                }
                            }
                        }
                        activity.binding?.contentLayout?.calendarLayout?.calendarView?.notifyCalendarChanged()
                    } else {
                        if (result.message == "Lo" +
                            "" +
                            "gged Out") {
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

    @RequiresApi(Build.VERSION_CODES.O)
    fun cancelMeeting(meetingId: String) {

        val dialog = activity.indeterminateProgressDialog("Loading data...").apply {
            setCancelable(false)
        }
        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.cancelMeeting(token, meetingId)
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
//                        activity.deleteReminder()
                        activity.showToast("deleted successfully")
                        fetchAllData()
                        if(activity.modalBottomSheet!=null && (activity.modalBottomSheet!!.isVisible)){
                            activity.modalBottomSheet!!.dismiss()
                        }


                    } else {
                        activity.showToast(result.message)
                        activity.runOnUiThread { dialog.dismiss() }
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

    @RequiresApi(Build.VERSION_CODES.O)
    fun deleteSession(meetingId: String) {

        val dialog = activity.indeterminateProgressDialog("Loading data...").apply {
            setCancelable(false)
        }
        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.deleteSession(token, DeleteSessionModel(meeting_id = meetingId))
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
//                        activity.deleteReminder()
                        activity.showToast("deleted successfully")
                        fetchAllData()
                        if(activity.modalBottomSheet!=null && (activity.modalBottomSheet!!.isVisible)){
                            activity.modalBottomSheet!!.dismiss()
                        }



                    } else {
                        activity.showToast(result.message)
                        activity.runOnUiThread { dialog.dismiss() }
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

    @RequiresApi(Build.VERSION_CODES.O)
    fun noRescheduleMeeting(meetingId: String) {
        val dialog = activity.indeterminateProgressDialog("Loading data...").apply {
            setCancelable(false)
        }
        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.noRescheduleMeeting(token, meetingId)
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
                        fetchAllData()
                        if(activity.modalBottomSheet!=null && (activity.modalBottomSheet!!.isVisible)){
                            activity.modalBottomSheet!!.dismiss()
                        }

                    } else {
                        activity.showToast(result.message)
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
