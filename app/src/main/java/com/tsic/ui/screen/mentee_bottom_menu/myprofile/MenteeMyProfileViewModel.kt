package com.tsic.ui.screen.mentee_bottom_menu.myprofile


import android.content.SharedPreferences
import androidx.databinding.ObservableField
import com.tsic.R
import com.tsic.data.local.prefs.*
import com.tsic.data.local.prefs.PreferenceHelper.setData
import com.tsic.data.model.Status
import com.tsic.data.model.common.SystemMessage
import com.tsic.data.model.mentor_api.AffiliateSystemMessaging
import com.tsic.data.remote.api.MENTEE_IMAGE_URL
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.ui.base.BaseApplication
import com.tsic.ui.screen.chatdetails.ChatDetailsBinding
import com.tsic.ui.screen.mentor_bottom_menu.myprofile.dialog.DialogSessionReminder
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

class MenteeMyProfileViewModel(private val activity: MenteeMyProfileActivity) {


    var name = ObservableField<String>("")
    var profilePic = ObservableField<String>("avatar")
    var personEmail = ObservableField<String>("")
    var schoolAddress = ObservableField<String>("")
    var linkedAgency = ObservableField<String>("")
    var sumSessionLogged = ObservableField<String>("0")
    var scheduleSessionCount = ObservableField<String>("0")
    var mentorStaffChatCount = ObservableField<String>("0")
    var mentorMenteeChatCount = ObservableField<String>("0")
    var messageCenterCount = ObservableField<String>("0")
    var unreadTask = ObservableField<String>("0")
    var unreadGoal = ObservableField<String>("0")
    var bannerMsgList = mutableListOf<AffiliateSystemMessaging>()


    private var disposable: Disposable? = null
    private val apiService by lazy { MenteeApiService.create() }

    private val userPrefs: SharedPreferences? by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }


    fun getUserData(isShow: Boolean = false) {

        if (!activity.isDeviceOnline()) {
            activity.toast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        disposable =
            apiService.getUserData(userPrefs?.getString(KEY_AUTH_TOKEN, ""))
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe { activity.isBusyLoadingData(isShow) }
                .doAfterTerminate { activity.isBusyLoadingData(false) }
                .subscribe(
                    { result ->
                        if (result.status == Status.SUCCESS) {
                            result.data?.userDetails?.apply {
                                if (userPrefs != null) {
                                    setData(KEY_FIRST_NAME, firstname)
                                    setData(KEY_MIDDLE_NAME, middlename)
                                    setData(KEY_LAST_NAME, lastname)
                                    setData(KEY_EMAIL, email)
                                    setData(KEY_PROFILE_PIC, "$MENTEE_IMAGE_URL$image")
                                }

                                name.set("${firstname?.trim()} ${middlename?.trim()} ${lastname?.trim()}")
                                personEmail.set(email)
                                profilePic.set("$MENTEE_IMAGE_URL$image")
                                schoolAddress.set("${currentLivingDetails?.trim()} ${country?.trim()}")
                                linkedAgency.set("${linkedAgencyName?.trim()}")
                                sumSessionLogged.set(sum_mentor_session_log_count)
                                message_center_count.let {
                                    messageCenterCount.set(it.toString())
                                }
                                unreadTask.set(unread_task)
                                unreadGoal.set(unread_goal)
                                activity.binding?.contentLayout?.apply {
                                    when (session_log_label_no) {
                                        "2" -> {
                                            ivBadge.setImageResource(
                                                R.drawable.bronze_medal
                                            )
                                            tvBadge.text = sumSessionLogged.get()
                                        }
                                        "3" -> {
                                            ivBadge.setImageResource(
                                                R.drawable.silver_medal
                                            )
                                            tvBadge.text = sumSessionLogged.get()

                                        }
                                        "4" -> {
                                            ivBadge.setImageResource(
                                                R.drawable.gold_medal
                                            )
                                            tvBadge.text = sumSessionLogged.get()
                                        }
                                    }
                                }
                                //activity?.initUserDataOnNavHeader()
                                scheduleSessionCount.set(schedule_session_count)
                                schedule_session_count?.let {
                                    activity?.setMentorSessionBadge(it.toInt())
                                }
                                mentee_staff_chat_count?.let { activity?.setStuffChatBadge(it) }
                                mentorMenteeChatCount.set(mentor_mentee_chat_count)
                                affiliateSystemMessaging?.let {
                                    if (isNotEqual(it, bannerMsgList)) {
                                        bannerMsgList.clear()
                                        bannerMsgList.addAll(it)
                                    }
                                }
                                activity.adapter?.notifyDataSetChanged()



                                if (upcoming_meeting != null && BaseApplication.upComingMeetingId != upcoming_meeting?.id) {
                                    val dialogUpcoming = DialogSessionReminder(activity,activity.getString(R.string.header_reminder_upcoming_session),activity.getString(R.string.reminder_session,Utils.getSimplifiedDate(upcoming_meeting?.schedule_time)) + " " +  upcoming_meeting?.firstname + " " + upcoming_meeting?.lastname){}

                                    BaseApplication.upComingMeetingId = upcoming_meeting?.id
                                    dialogUpcoming?.show()

                                }
                            }

                        } else {
                            activity.isBusyLoadingData(false)
                            if (result.message == "Logged Out") {
                                activity.logoutForTnC()
                            } else {
                                activity.showToast(result.message)
                            }
//                            activity.showToast(result.message)
                        }
                    },
                    { error ->
                        activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
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
                                        message.copy(shouldVisible = true, message = message.message + "\n")
                                    }
                                } else {
                                    SystemMessage.Messaging()
                                }
                            }
                        } else {
                            if (result.message == "Logged Out") {
                                activity.logoutForTnC()
                            } else {
                                activity.showToast(result.message)
                            }
//                            activity.showToast(result.message)
                        }
                    },
                    { error ->
                        activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                    }
                )
    }

    fun updateProfile() {
        activity.dismissKeyboard()
        if (profilePic.get()?.isEmpty() == true || name.get()?.isEmpty() == true) {
            activity.showToast("Missing name or profile pic")
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
                "userimage",
                file.name ?: "",
                file.asRequestBody("multipart/form-data".toMediaType())
            )
        }
        val fName = (name.get() ?: "").substringBefore(" ")
        val mName = (name.get() ?: "").substringAfter(" ").substringBefore(" ", " ")
        val lName = (name.get() ?: "").substringAfterLast(" ")
        builder.addFormDataPart("firstname", fName)
        builder.addFormDataPart("middlename", mName)
        builder.addFormDataPart("lastname", lName)

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
                            activity.apply {
                                activity.toast("Image has been uploaded successfully")
                                if (userPrefs != null) {
                                    setData(KEY_FIRST_NAME, result.data?.firstname)
                                    setData(KEY_MIDDLE_NAME, result.data?.middlename)
                                    setData(KEY_LAST_NAME, result.data?.lastname)
                                    setData(
                                        KEY_PROFILE_PIC,
                                        "$MENTEE_IMAGE_URL${result.data?.image}"


                                    )
                                }
                                activity.initUserDataOnNavHeader()
                            }
                        } else {
                            if (result.message == "Logged Out") {
                                activity.logoutForTnC()
                            } else {
                                activity.showToast(result.message)
                            }
                            activity.isBusyLoadingData(false)
//                            activity.toast(result.message.toString())
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
    fun convertTime(value: String): String {
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
