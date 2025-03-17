package com.tsic.ui.screen.mentor_drawer_menu.meetings.requested.editMeeting


import android.content.ContentUris
import android.content.Intent
import android.content.pm.PackageManager
import android.net.Uri
import android.provider.CalendarContract
import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.remote.api.MentorApiService
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.logoutForTnC
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers
import org.jetbrains.anko.alert
import java.util.*

class MentorEditMeetingViewModel(private val activity: MentorEditMeetingActivity) {

    var menteeName = ObservableField("")
    var schoolLocation = ObservableField("")

    var title = ObservableField("")
    var description = ObservableField("")
    var schoolSpace = ObservableField("")
    var methodLocation = ObservableField("")
    var date = ObservableField("")
    var time = ObservableField("")
    var time12h = ObservableField("")


    //    private var disposableone: Disposable? = null
    private var disposable: Disposable? = null
    private val apiService by lazy { MentorApiService.create() }

    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }

    private val VALID: String = ""


    private fun getFieldsValidity(): String {
        var msg = VALID
        if (date.get()?.trim()?.isBlank() == true)
            msg = "Please select date"
        if (time.get()?.trim()?.isBlank() == true)
            msg = "Please select time"

        return msg
    }


    fun rescheduleMeeting(meetingId: String, id: String) {

        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.rescheduleMeeting(
            token,
            id,
            meetingId,
            date.get().toString(),
            time.get().toString()
        )
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
                        /*activity.alert(
                            "Rescheduled successfully",
                            "Message fro TSIC"
                        ) {
                            okButton {*/
                        // activity.deleteReminder(title.get().toString())
                        checkCalender()

                        /*activity.alert("Rescheduled successfully. Link Rescheduled Session to save it to system calendar") {
                            isCancelable = false
                            positiveButton("Yes") {

                            }
                            negativeButton("No") {
                                activity.finish()
                            }
                        }.show()*/
                        //}
                        // }.show()

                    } else {
                        if (result.message == "Logged Out") {
                            activity.logoutForTnC()
                        } else {
                            activity.showToast(result.message.toString())
                        }

                        activity.isBusyLoadingData(false)
                    }
                },
                { error ->
                    activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                }
            )

    }

    private fun checkCalender() {
        if (appInstalledOrNot("com.google.android.calendar")) {
            activity.calendarPermission()
        } else {
            activity.alert("Please install google calender") {
                isCancelable = false
                positiveButton("Accept") {
                    val intent = Intent(Intent.ACTION_VIEW)
                    intent.data = Uri.parse("market://details?id=com.google.android.calendar")
                    activity.startActivityForResult(intent, 101)
                    activity.alert("Link newly created Session to save it to system calendar") {
                        isCancelable = false
                        positiveButton("Yes") {
                            //activity.calendarPermission()
                            checkCalender()
                        }
                        negativeButton("No") {
                            activity.finish()
                            activity.dismissKeyboard()
                        }
                    }.show()
                    //activity.finish()
                }
                negativeButton("Ignore") {
                    activity.finish()
                    activity.dismissKeyboard()
                }
            }.show()
        }
    }

    private fun appInstalledOrNot(uri: String): Boolean {
        val pm: PackageManager = this.activity.packageManager
        return try {
            pm.getPackageInfo(uri, PackageManager.GET_ACTIVITIES)
            true
        } catch (e: PackageManager.NameNotFoundException) {
            false
        }
        //return false
    }

    fun calenderReminder(eventId: Long) {
        var mDate = date.get().toString().split("-")
        var mTime = time.get().toString().split(":")
        val startMillis: Long = Calendar.getInstance().run {
            set(
                mDate[2].toInt(),
                mDate[0].toInt() - 1,
                mDate[1].toInt(),
                mTime[0].toInt(),
                mTime[1].toInt()
            )
            timeInMillis
        }
        val endMillis: Long = startMillis + 3600000
/*

        val values = ContentValues().apply {
            put(CalendarContract.Events.DTSTART, startMillis)
            put(CalendarContract.Events.DTEND, endMillis)
            put(CalendarContract.Events.TITLE, title.get().toString())
            put(CalendarContract.Events.DESCRIPTION, description.get().toString())
            put(CalendarContract.Events.CALENDAR_ID, 3)
            put(CalendarContract.Events.EVENT_TIMEZONE, "EDT")
        }
        if (ActivityCompat.checkSelfPermission(
                activity,
                Manifest.permission.WRITE_CALENDAR
            ) != PackageManager.PERMISSION_GRANTED
        )
            return

        activity.contentResolver.insert(CalendarContract.Events.CONTENT_URI, values)*/
        activity.alert("Rescheduled successfully. Link Rescheduled Session to save it to system calendar") {
            isCancelable = false
            positiveButton("Yes") {
                val intent = Intent(Intent.ACTION_INSERT)
                    .setData(CalendarContract.Events.CONTENT_URI)
                    .putExtra(CalendarContract.EXTRA_EVENT_BEGIN_TIME, startMillis)
                    .putExtra(CalendarContract.EXTRA_EVENT_END_TIME, endMillis)
                    .putExtra(
                        CalendarContract.Events.TITLE,
                        this@MentorEditMeetingViewModel.title.get().toString()
                    )
                    .putExtra(CalendarContract.Events.DESCRIPTION, description.get().toString())
                    .putExtra(
                        CalendarContract.Events.AVAILABILITY,
                        CalendarContract.Events.AVAILABILITY_BUSY
                    )
                activity.startActivity(intent)
                if (eventId != -1L)
                    deleteCalenderEvent(eventId)
                else
                    activity?.finish()
                activity?.dismissKeyboard()
            }
            negativeButton("No") {
                if (eventId != -1L)
                    deleteCalenderEvent(eventId)
                else
                    activity?.finish()
                activity?.dismissKeyboard()
            }
        }.show()


    }

    private fun deleteCalenderEvent(eventId: Long) {
        activity.alert("Delete old copy of this session event from Calendar?") {
            isCancelable = false
            positiveButton("Open Calendar") {
                val uri: Uri =
                    ContentUris.withAppendedId(CalendarContract.Events.CONTENT_URI, eventId)
                val intent = Intent(Intent.ACTION_VIEW)
                    .setData(uri)
                activity.startActivity(intent)
                activity?.finish()
                activity?.dismissKeyboard()
            }
            negativeButton("Keep it ") {
                activity.finish()
                activity.dismissKeyboard()
            }
        }.show()
    }

    fun dispose() {
        disposable?.dispose()
    }


    fun onPause() = dispose()
    fun onStop() = dispose()
}


