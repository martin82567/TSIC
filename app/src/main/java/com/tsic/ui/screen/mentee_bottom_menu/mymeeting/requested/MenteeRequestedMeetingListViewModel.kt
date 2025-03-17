package com.tsic.ui.screen.mentee_bottom_menu.mymeeting.requested

import android.content.Intent
import android.provider.CalendarContract
import android.view.View
import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.model.mentee_api.RequestedMenteeMeeting
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.logoutForTnC
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers
import java.text.DateFormat
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*

class MenteeRequestedMeetingListViewModel(private val fragment: MenteeRequestedMeetingListFrag) {


    var varString = ObservableField("")
    var varInt = ObservableField(0)
    var meetingList = ObservableField<List<RequestedMenteeMeeting?>>(listOf())

    var varExtraString = ""

    private var disposable: Disposable? = null
    private val apiService by lazy { MenteeApiService.create() }

    private val userPrefs by lazy {
        fragment.context?.let { PreferenceHelper.customPrefs(it, USER_PREF) }
    }

    fun fetchMeetingList() {
        fragment.activity?.dismissKeyboard()

        meetingList.set(listOf())
        /*if (!fragment?.activity?.isDeviceOnline()) {
            fragment?.activity?.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }*/

        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.getMeetingList(token)
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
                            if (result.data?.meetingList?.size == 0) View.VISIBLE else View.GONE
                        meetingList.set(result?.data?.meetingList)
                    } else {
                        if (result.message == "Logged Out") {
                            fragment?.activity?.logoutForTnC()
                        } else {
                            fragment.showToast(result.message)
                        }
                    }
                },
                { error ->

                }
            )
    }


    fun acceptMeeting(meetingId: String) {
        fragment.activity?.apply {
            dismissKeyboard()
            if (!isDeviceOnline()) {
                fragment.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
                return
            }
        }

        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.getRequestedMenteeMeeting(
            token,
            meetingId,
            "1"
        )
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
                        fragment.showToast("Accepted")
                    } else {
                        fragment.showToast(result.message)
                        fragment.isBusyLoadingData(false)
                    }
                },
                { error ->
                    fragment.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                }
            )
    }

    fun calenderReminder(
        date: String,
        time: String,
        title: String,
        description: String
    ) {
        var mDate = date.split("-")
        val df: DateFormat = SimpleDateFormat("hh:mm aa")
        val outputformat: DateFormat = SimpleDateFormat("HH:mm")
        var date: Date? = null
        var output: String? = null
        try {
            date = df.parse(time)
            output = outputformat.format(date)

        } catch (pe: ParseException) {
            pe.printStackTrace()
        }
        var mTime = output?.split(":")
        val startMillis: Long = Calendar.getInstance().run {
            mTime?.get(0)?.toInt()?.let {
                set(
                    mDate[2].toInt(),
                    mDate[0].toInt() - 1,
                    mDate[1].toInt(),
                    it,
                    mTime[1].toInt()
                )
            }
            timeInMillis
        }
        val endMillis: Long = startMillis + 3600000


       /* val values = ContentValues().apply {
            put(CalendarContract.Events.DTSTART, startMillis)
            put(CalendarContract.Events.DTEND, endMillis)
            put(CalendarContract.Events.TITLE, title)
            put(CalendarContract.Events.DESCRIPTION, description)
            put(CalendarContract.Events.CALENDAR_ID, 3)
            put(CalendarContract.Events.EVENT_TIMEZONE, "EDT")
        }
        if (ActivityCompat.checkSelfPermission(
                fragment.activity!!,
                Manifest.permission.WRITE_CALENDAR
            ) != PackageManager.PERMISSION_GRANTED
        ) return

        fragment.activity!!.contentResolver.insert(CalendarContract.Events.CONTENT_URI, values)*/
        val intent = Intent(Intent.ACTION_INSERT)
            .setData(CalendarContract.Events.CONTENT_URI)
            .putExtra(CalendarContract.EXTRA_EVENT_BEGIN_TIME, startMillis)
            .putExtra(CalendarContract.Events.TITLE, title)
            .putExtra(CalendarContract.Events.DESCRIPTION, description)
            // .putExtra(CalendarContract.Events.EVENT_LOCATION, "TSIC")
            .putExtra(
                CalendarContract.Events.AVAILABILITY,
                CalendarContract.Events.AVAILABILITY_BUSY
            )
        //.putExtra(Intent.EXTRA_EMAIL, "rowan@example.com,trevor@example.com")
        fragment.activity!!.startActivity(intent)
    }

    /* fun fetchMenteePastMeetingList() {
         fragment.activity?.apply {
             dismissKeyboard()
             if (!isDeviceOnline()) {
                 fragment.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
                 return
             }

             val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
             disposable = apiService.getPastMenteeMeeting(token)
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




                             result.data?.meeting?.let {
                                 meetingList.set(it)
                             }
                         } else {
                             fragment.showToast(result.message)
                             fragment.isBusyLoadingData(false)
                         }
                     },
                     { error ->
                         fragment.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                     }
                 )
         }


     }*/

    fun dispose() {
        disposable?.dispose()
    }

    fun onResume() = fetchMeetingList()
    fun onPause() = dispose()
    fun onStop() = dispose()
}
