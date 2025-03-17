package com.tsic.ui.screen.mentor_bottom_menu.myprofile


import android.content.Intent
import android.util.Log
import androidx.databinding.ObservableField
import androidx.lifecycle.MutableLiveData
import com.tsic.R
import com.tsic.data.local.prefs.*
import com.tsic.data.local.prefs.PreferenceHelper.setData
import com.tsic.data.model.Status
import com.tsic.data.model.common.SystemMessage
import com.tsic.data.model.mentor_api.AffiliateSystemMessaging
import com.tsic.data.model.mentor_api.TodaysMeetingModel
import com.tsic.data.remote.api.MENTOR_IMAGE_URL
import com.tsic.data.remote.api.MentorApiService
import com.tsic.ui.base.BaseApplication
import com.tsic.ui.screen.chatdetails.ChatDetailsBinding
import com.tsic.ui.screen.chatdetails.ChatDetailsBinding.setData
import com.tsic.ui.screen.mentor_bottom_menu.myprofile.dialog.DialogSessionReminder
import com.tsic.ui.screen.mentor_bottom_menu.mysessions.MentorMySessionsActivity
import com.tsic.util.Utils
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.logoutForTnC
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import org.jetbrains.anko.toast
import java.io.File
import java.text.SimpleDateFormat
import java.util.*

class MentorMyProfileViewModel(private val activity: MentorMyProfileActivity) {

    var name = ObservableField<String>("")
    var phoneNumber = ObservableField<String>("")
    var personalEmail = ObservableField<String>("")
    var personaladdress = ObservableField<String>("")
    var profilePic = ObservableField<String>("")
    var sessionsLogged = ObservableField<String>("")
    var affiliateName = ObservableField<String>("")
    var mentorMenteeChatCount = ObservableField<String>("")
    var messageCenterCount = ObservableField<String>("0")
    var scheduleSessionCount = ObservableField<String>("")
    var bannerMsgList = mutableListOf<AffiliateSystemMessaging>()
    private var disposable: Disposable? = null
    private val apiService by lazy { MentorApiService.create() }


    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }

    private val listMeetings = mutableListOf<TodaysMeetingModel>()

    private val upcomingMeetingDate = MutableLiveData("")
    private val passedMeetingDate = MutableLiveData("")
    //val upcomingMeetingStateDate : LiveData<String> = upcomingMeetingDate


    fun fetchData(isShow: Boolean = false) {
        activity.dismissKeyboard()
        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.getMentorProfileData(token)
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe {
                activity.isBusyLoadingData(isShow)
            }
            .doAfterTerminate {
                activity.isBusyLoadingData(false)
            }
            .subscribe(
                { result ->
                    if (result.status == Status.SUCCESS) {
                        result.data?.apply {
                            if (userPrefs != null) {
                                setData(KEY_FIRST_NAME, firstname)
                                setData(KEY_MIDDLE_NAME, middlename)
                                setData(KEY_LAST_NAME, lastname)
                                setData(KEY_EMAIL, email)
                                setData(KEY_PHONE, phone)
                                setData(
                                    KEY_PROFILE_PIC,
                                    "$MENTOR_IMAGE_URL$image"
                                )
                                setData(KEY_ADDRESS, address)


                            }
                            affiliateSystemMessaging?.let {
                                if (isNotEqual(it, bannerMsgList)) {
                                    bannerMsgList.clear()
                                    bannerMsgList.addAll(it)
                                }
                            }
                            activity.adapter?.notifyDataSetChanged()
                            name.set("${firstname?.trim()} ${middlename?.trim()} ${lastname?.trim()}")
                            personalEmail.set(email)
                            phoneNumber.set(phone)
                            profilePic.set("$MENTOR_IMAGE_URL$image")
                            personaladdress.set(address)
                            affiliateName.set(linkedAgencyName)
                            mentorMenteeChatCount.set(mentor_mentee_chat_count)
                            sessionsLogged.set(session_log_count)
                            scheduleSessionCount.set(schedule_session_count)
                            schedule_session_count?.let {
                                activity?.setMentorSessionBadge(it.toInt())
                            }
                            mentor_staff_chat_count?.let { activity?.setBadge(it) }
                            message_center_count?.let { messageCenterCount.set(it.toString()) }
                            activity.binding?.contentLayout?.apply {
                                when (sessionLogLabelNo) {
                                    "2" -> {
                                        ivBadge.setImageResource(
                                            R.drawable.bronze_medal
                                        )
                                        tvBadge.text = sessionsLogged.get()
                                    }
                                    "3" -> {
                                        ivBadge.setImageResource(
                                            R.drawable.silver_medal
                                        )
                                        tvBadge.text = sessionsLogged.get()
                                    }
                                    "4" -> {
                                        ivBadge.setImageResource(
                                            R.drawable.gold_medal
                                        )
                                        tvBadge.text = sessionsLogged.get()
                                    }
                                }
                            }

//                            upcomingMeetingDate.value = upcoming_meeting?.schedule_time
//                            val dialogUpcoming =
//                                upcomingMeetingDate.value?.let {
//                                    DialogSessionReminder(
//                                        activity,
//                                         activity.getString(R.string.header_reminder_upcoming_session),
//                                        activity.getString(R.string.reminder_session,it)+ upcoming_meeting?.firstname + upcoming_meeting?.lastname
//                                    )
//                                }



                            if (upcoming_meeting != null && BaseApplication.upComingMeetingId != upcoming_meeting?.id) {

                                val dialogUpcoming = DialogSessionReminder(activity,activity.getString(R.string.header_reminder_upcoming_session),activity.getString(R.string.reminder_session, Utils.getSimplifiedDate(upcoming_meeting?.schedule_time)) + " " +  upcoming_meeting?.firstname + " " + upcoming_meeting?.lastname){}

                                BaseApplication.upComingMeetingId = upcoming_meeting?.id
                                dialogUpcoming?.show()

                            }


//                            passedMeetingDate.value = past_meeting?.schedule_time
//                            val dialogPassed =
//                                passedMeetingDate.value?.let {
//                                    DialogSessionReminder(
//                                        activity,
//                                        activity.getString(R.string.header_reminder_log_session),
//                                        activity.getString(R.string.scheduled_session,it)
//                                    )
//                                }

                            if (past_meeting != null && BaseApplication.passedMeetingId != past_meeting?.id) {
                                val dialogPassed = DialogSessionReminder(activity,activity.getString(R.string.header_reminder_log_session),activity.getString(R.string.scheduled_session, Utils.getSimplifiedDate(past_meeting?.schedule_time))+ " " +past_meeting?.firstname + " " + past_meeting?.lastname){
                                    activity.startActivity(Intent(activity,MentorMySessionsActivity::class.java))
                                }
                                BaseApplication.passedMeetingId = past_meeting?.id
                                dialogPassed.show()
                        }
                    }
//                        activity?.showMessage(false)


                } else {
                activity.isBusyLoadingData(false)
                if (result.message == "Logged Out") {
                    activity.logoutForTnC()
                } else {
                    activity.showToast(result.message)
                }
            }
    },
    {
        error ->
        activity.showToast(
            error.message
                ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later."
        )
    }
    )
}

fun getSystemMessage() {

    if (!activity.isDeviceOnline()) {
        activity.toast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
        return
    }

    disposable =
        apiService.systemMessaging()
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe { }
            .doAfterTerminate { }
            .subscribe(
                { result ->
                    if (result.status == Status.SUCCESS) {
                        result.data?.messaging?.apply {
                            activity?.binding?.contentLayout?.model = if (!isNullOrEmpty()) {
                                get(0).let { message ->
                                    message.copy(
                                        shouldVisible = true,
                                        message = message.message + "\n"
                                    )
                                }
                            } else {
                                SystemMessage.Messaging()
                            }

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

fun fetchMeetings() {
    val token = userPrefs?.getString(KEY_AUTH_TOKEN, "") ?: ""
    disposable = apiService.fetchTodayMeetings(token)
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
                    result.data?.apply {
                        listMeetings.clear()
                        if (this.isNotEmpty()) {
                            listMeetings.addAll(this)
                        }

                    }
                } else {
                    activity.isBusyLoadingData(false)
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

fun updateProfile() {
    activity.dismissKeyboard()
    if (profilePic.get()?.isEmpty() == true) {
        activity.showToast("Missing profile pic")
        return
    }

    if (!activity.isDeviceOnline()) {
        activity.toast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
        return
    }

    val builder = MultipartBody.Builder()

    builder.setType(MultipartBody.FORM)

    val picUrl = profilePic.get() ?: ""
    if (picUrl.startsWith("/")) {
        val file = File(profilePic.get() ?: "")

        builder.addFormDataPart(
            "image",
            file.name ?: "",
            file.asRequestBody("multipart/form-data".toMediaType())
        )
    }

    val fName = (name.get() ?: "").substringBefore(" ")
    val mName = (name.get() ?: "").substringAfter(" ").substringBefore(" ", " ")
    val lName = (name.get() ?: "").substringAfterLast(" ")
    val mPhone = (phoneNumber.get() ?: "")
    val mEmail = (personalEmail.get() ?: "")
    builder.addFormDataPart("firstname", fName)
    builder.addFormDataPart("middlename", mName)
    builder.addFormDataPart("lastname", lName)
    builder.addFormDataPart("phone", mPhone)
    builder.addFormDataPart("email", mEmail)


    val requestBody = builder.build()

    disposable =
        apiService.editProfile(userPrefs?.getString(KEY_AUTH_TOKEN, ""), requestBody)
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe { activity.isBusyLoadingData(true) }
            .doAfterTerminate { activity.isBusyLoadingData(false) }
            .subscribe(
                { result ->
                    if (result.status) {
                        activity.toast("Image has been uploaded successfully")
                        activity.apply {
                            if (userPrefs != null) {
                                setData(KEY_FIRST_NAME, result.data?.firstname)
                                setData(KEY_MIDDLE_NAME, result.data?.middlename)
                                setData(KEY_LAST_NAME, result.data?.lastname)
                                setData(KEY_ADDRESS, result.data?.address)
                                setData(KEY_PHONE, result.data?.phone)
                                setData(KEY_EMAIL, result.data?.email)
                                setData(
                                    KEY_PROFILE_PIC,
                                    "$MENTOR_IMAGE_URL${result.data?.image}"
                                )
                            }
                            activity.initUserDataOnNavHeader()
                        }
                    } else {
                        activity.isBusyLoadingData(false)
                        if (result.message == "Logged Out") {
                            activity.logoutForTnC()
                        } else {
                            activity.showToast(result.message)
                        }
                    }
                },
                { error ->
                    activity.toast("Some error occured.")
                }
            )
}

private fun <T> isNotEqual(first: List<T>, second: List<T>): Boolean {
    if (first.size != second.size) {
        return true
    }
    return !first.zip(second).all { (x, y) -> x == y }
}

fun dispose() {
    disposable?.dispose()
}

fun convertTime(value: String?): String {
    val sdf = getSimpleDateFormat("yyyy-MM-dd HH:mm:ss")
    val date = sdf.parse(value)
    return getSimpleDateFormat("MM-dd-yyyy h:mm a").format(date)
}

private fun getSimpleDateFormat(pattern: String): SimpleDateFormat {
    val sdf = SimpleDateFormat(pattern, Locale.US)
    sdf.timeZone = SimpleTimeZone.getTimeZone(ChatDetailsBinding.timezoneOffset)
    return sdf
}

fun onPause() = dispose()
fun onStop() = dispose()

}

