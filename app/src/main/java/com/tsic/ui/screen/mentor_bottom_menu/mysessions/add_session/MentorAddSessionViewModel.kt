package com.tsic.ui.screen.mentor_bottom_menu.mysessions.add_session


import android.app.Activity
import android.content.Intent
import android.opengl.Visibility
import android.util.Log
import android.view.View
import android.widget.CheckBox
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
import org.jetbrains.anko.toast

class MentorAddSessionViewModel(private val activity: MentorAddSessionActivity) {

    var menteeName = ObservableField("")
    var menteeId = ""
    var note = ObservableField("")
    var date = ObservableField("")
    var timeFrom = ObservableField("")
    var sessionTypeKey = ObservableField("")
    var sessionMethodLocationKey = ObservableField("")
    var sessionTypeValue = ObservableField("")
    var sessionMethodLocationValue = ObservableField("")

    var listMenteeNames = arrayListOf<String>()
    var durationSession = ""
    var listMenteeId = mutableListOf<String>()
    var listSessionMethodLocation = mutableMapOf<String, String>()
    var firstLaunch = true

    var isNoShow = ObservableField<Boolean>()


    //    private var disposableone: Disposable? = null
    private var disposable: Disposable? = null
    private val apiService by lazy { MentorApiService.create() }

    var listSession = ObservableField<List<MentorMyMenteeModel?>>(listOf())


    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }

    private val VALID: String = ""


    private fun getFieldsValidity(): String {
        var msg = VALID
        when {
            menteeName.get()?.trim()?.isBlank() == true -> msg = "Please Select Mentee Name"
            date.get()?.trim()?.isBlank() == true -> msg = "Please Select Date"
            timeFrom.get()?.trim()?.isBlank() == true && isNoShow.get() != true-> msg = "Please Select Session Duration"
            sessionMethodLocationKey.get()?.trim()?.isBlank() == true -> msg =
                "Please Select Session Method Location"
            sessionTypeKey.get()?.trim()?.isBlank() == true -> msg = "Please Select Session Type"
            note.get()?.trim()?.isBlank() == true -> msg = "Please Enter A Short Note"

        }
        return msg
    }

    fun fetchMenteeList() {
        activity.dismissKeyboard()

        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        listSession.set(listOf())

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
                            result.data.map {
                                val v = ("${it.firstname} ${it.middlename} ${it.lastname}")
                                listMenteeNames.add(v)
                                listMenteeId.add("${it.id}")
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
                                activity.showToast("No List Found")
                            }
                        }
                    } else {
                        result.message?.let { activity.showToast(it) }
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

    fun getSessionMethodLocationList() {
        activity.dismissKeyboard()

        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        listSession.set(listOf())

        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.getSessionMethodLocationList(token)
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
                    activity.showToast(
                        error.message
                            ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later."
                    )
                }
            )
    }


    fun disposetwo() {
        disposable?.dispose()
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
        disposable = apiService.addSession(
            token,
            note.get().toString(),
            date.get().toString(),
            menteeId, "",
           timeDuration = if(isNoShow.get()== true) "0" else timeFrom.get().toString(),
            sessionMethodLocationValue.get().toString(),
            sessionTypeValue.get().toString(),
            if (isNoShow.get() == true) 1 else 0


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

                        activity.setResult(Activity.RESULT_OK, Intent())
                        activity.finish()
                        activity.dismissKeyboard()
                    } else {
                        result.message?.let { activity.showToast(it) }
                        Log.e(">>","add session  ... ${result.message}")
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


    fun disposeone() {
        disposable?.dispose()
    }


    fun onResume() = fetchMenteeList()
    fun onPause() = disposeone()
    fun onStop() = disposeone()
}


