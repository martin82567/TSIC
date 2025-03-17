package com.tsic.ui.screen.mentor_drawer_menu.meetings.requested.addMeeting


import android.content.Intent
import android.content.pm.PackageManager
import android.net.Uri
import android.provider.CalendarContract
import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.model.mentor_api.MentorMyMenteeModel
import com.tsic.data.remote.api.MentorApiService
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.logoutForTnC
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers
import org.jetbrains.anko.alert
import org.jetbrains.anko.toast
import java.util.*


class MentorAddMeetingViewModel(private val activity: MentorAddMeetingActivity) {

    var menteeName = ObservableField("")
    var schoolLocation = ObservableField("")
    var menteeId = ""
    var schoolId = ""
    var title = ObservableField("Mentor Session")
    var description = ObservableField("")
    var schoolSpace = ObservableField("")
    var date = ObservableField("")
    var latitude = ObservableField("22.548")
    var longitude = ObservableField("88.353")
    var time = ObservableField("")
    var time12h = ObservableField("")
    var listMenteeNames = arrayListOf<String>()
    var listMenteeId = mutableListOf<String>()
    var listSchoolNames = arrayListOf<String>()
    var listMenteeSchoolNames = arrayListOf<String>()
    var listSchoolId = mutableListOf<String>()
    var listMentee = mutableListOf<MentorMyMenteeModel>()
    var listSessionMethodLocation = mutableMapOf<String, String>()
    var sessionMethodLocationKey = ObservableField("")
    var sessionMethodLocationValue = ObservableField("")

    var firstLaunch = true


    //    private var disposableone: Disposable? = null
    private var disposable: Disposable? = null
    private val apiService by lazy { MentorApiService.create() }

    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }

    private val VALID: String = ""


    private fun getFieldsValidity(): String {
        var msg = VALID
        when {
            title.get()?.trim()?.isBlank() == true -> msg = "Please Enter Agenda Name"
            menteeName.get()?.trim()?.isBlank() == true -> msg = "Please Select Assign To Dropdown"
            schoolLocation.get()?.trim()?.isBlank() == true -> msg =
                "Please Select The Session Location"
            sessionMethodLocationKey.get()?.trim()?.isBlank() == true -> msg =
                "Please Select Session Method Location"
            //schoolSpace.get()?.trim()?.isBlank() == true -> msg = "Please Enter Session Space"
            date.get()?.trim()?.isBlank() == true -> msg = "Please Select Date"
            time.get()?.trim()?.isBlank() == true -> msg = "Please Select Time"
        }
        return msg
    }

    fun fetchMenteeList() {
        activity.dismissKeyboard()

        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }
        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.fetchMenteeList(token)
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
                        if (result?.data?.isNotEmpty() == true) {
                            listMenteeNames.clear()
                            listMenteeId.clear()
                            listMenteeSchoolNames.clear()
                            result.data.map {
                                val v = ("${it.firstname} ${it.middlename} ${it.lastname}")
                                listMenteeNames.add(v)
                                listMenteeId.add("${it.id}")
                            }
                            result.data.let {
                                listMentee.addAll(it)
                            }

                            if (firstLaunch) {
                                firstLaunch = false
                            } else {
                                activity.menteeFetchList()
                            }
                        } else {
                            if (result.message == "Logged Out") {
                                activity.logoutForTnC()
                            } else {
                                activity.showToast(result.message.toString())
                            }
//                            activity.showToast("No Mentees found")
                        }
                    } else {
                        result.message?.let { activity.showToast(it) }
                    }
                },
                { error ->
                    activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                }
            )
    }

    fun getSessionMethodLocationList(menteeId: String) {
        activity.dismissKeyboard()

        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }


        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.getSessionMethodLocationList(token, menteeId)
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
                        if (result?.data?.sessionMethodLocation?.isNotEmpty() == true) {

                            result.data.sessionMethodLocation.map {
                                listSessionMethodLocation.put(
                                    it.method_value.toString(),
                                    it.id.toString()
                                )
                            }

                        } else {
                            activity.showToast("No Mentee Found")
                        }
                    } else {
                        result.message?.let { activity.showToast(it) }
                    }
                },
                { error ->
                    activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                }
            )
    }

    fun fetchSchoolList() {
        activity.dismissKeyboard()

        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        activity.isBusyLoadingData(true)


        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.fetchSchoolList(token)
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
                        if (result?.data?.isNotEmpty() == true) {
                            listSchoolNames.clear()
                            listSchoolId.clear()

                            result.data.map {
                                val v = it.name
                                listSchoolNames.add(v ?: "")
                                listSchoolId.add("${it.id}")
                            }

                            if (firstLaunch) {
                                firstLaunch = false
                            } else {
                                activity.schoolList()
                            }
                        } else {
                            activity.showToast("No Locations found")
                        }
                    } else {
                        result.message?.let { activity.showToast(it) }
                    }
                },
                { error ->
                    activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                }
            )
    }


    fun saveSession() {
        activity.dismissKeyboard()


        val validate: String? = getFieldsValidity()

        if (!validate.equals(VALID)) {
            validate?.let { activity.toast(it) }
            return
        }


        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        activity.isBusyLoadingData(true)


        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.addMeeting(
            token,
            title.get().toString(),
            description.get().toString(),
            sessionMethodLocationValue.get().toString(),
            schoolSpace.get().toString(),
            schoolId, schoolLocation.get().toString(),
            date.get().toString(),
            time.get().toString(),
            menteeId
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
                        /*activity.setResult(Activity.RESULT_OK, Intent())
                        activity.finish()*/
                    } else {
                        result.message?.let { activity.showToast(it) }
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
                    activity. startActivityForResult(intent, 101)
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

    fun calenderReminder() {
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


       /* val values = ContentValues().apply {
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
        ) return

        activity.contentResolver.insert(CalendarContract.Events.CONTENT_URI, values)*/
        val intent = Intent(Intent.ACTION_INSERT)
            .setData(CalendarContract.Events.CONTENT_URI)
            .putExtra(CalendarContract.EXTRA_EVENT_BEGIN_TIME, startMillis)
            .putExtra(CalendarContract.EXTRA_EVENT_END_TIME, endMillis)
            .putExtra(CalendarContract.Events.TITLE, title.get().toString())
            .putExtra(CalendarContract.Events.DESCRIPTION, description.get().toString())
            // .putExtra(CalendarContract.Events.EVENT_LOCATION, "TSIC")
            .putExtra(
                CalendarContract.Events.AVAILABILITY,
                CalendarContract.Events.AVAILABILITY_BUSY
            )
        //.putExtra(Intent.EXTRA_EMAIL, "rowan@example.com,trevor@example.com")
        activity.startActivity(intent)
        activity.finish()
        activity.dismissKeyboard()
    }

    fun dispose() {
        disposable?.dispose()
    }


    fun onResume() {
        fetchMenteeList()
        //   fetchSchoolList()
    }

    fun onPause() = dispose()
    fun onStop() = dispose()
}


