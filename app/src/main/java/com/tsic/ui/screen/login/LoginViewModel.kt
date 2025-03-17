package com.tsic.ui.screen.login


import android.content.SharedPreferences
import android.text.method.LinkMovementMethod
import android.text.method.ScrollingMovementMethod
import android.util.Patterns
import android.widget.Toast
import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.*
import com.tsic.data.local.prefs.PreferenceHelper.setData
import com.tsic.data.model.Status
import com.tsic.data.model.common.SystemMessage
import com.tsic.data.model.login_api.UnifiedUserDetailsModel
import com.tsic.data.model.login_api.UnifiedUserLoginModel
import com.tsic.data.model.mentee_api.UserLoginModel
import com.tsic.data.model.mentee_api.UserLoginWaiverModel
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.data.remote.api.MentorApiService
import com.tsic.data.remote.api.UnifiedApiService
import com.tsic.ui.screen.changepassword.ChangePasswordActivity
import com.tsic.ui.screen.chatdetails.ChatDetailsBinding
import com.tsic.ui.screen.forgotpassword.ForgotPasswordActivity
import com.tsic.ui.screen.mentee_bottom_menu.myprofile.MenteeMyProfileActivity
import com.tsic.ui.screen.mentor_bottom_menu.myprofile.MentorMyProfileActivity
import com.tsic.util.INTENT_KEY_EMAIL
import com.tsic.util.TYPE_MENTEE
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers
import org.jetbrains.anko.*
import java.text.SimpleDateFormat
import java.util.*
import kotlin.apply

class LoginViewModel(private val activity: LoginActivity) {

    private val menteeApiService by lazy {
        MenteeApiService.create()
    }
    private val mentorApiService by lazy {
        MentorApiService.create()
    }
    private val unifiedApiService by lazy {
        UnifiedApiService.create()
    }
    private val userPrefs: SharedPreferences? by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }

    private var disposable: Disposable? = null

    private val VALID: String = ""

    var email: String = ""
    var is_waiver_acknowledged=0
    var password = ObservableField("")
    var disclamerId = 0
    var loginMode = ""
    var status = ""
    var message=""
    private val firebaseToken = userPrefs?.getString(KEY_FIREBASE_TOKEN, "")
    private var deviceType = "android"

    var latitude: Double = 0.0
    var longitude: Double = 0.0
    private fun getFieldsValidity(): String {
        var msg = VALID
        if (password.get()?.trim()?.isBlank() == true)
            msg = "Error: \n You must enter your password. If you do not know your password you may use the forgot password feature below."
        else if (password.get()?.trim()?.length!! < 6)
            msg = "Password should contain at least 6 characters"
       /* else if (!activity.binding.contentLayout.cbTNC.isChecked)
            msg = "Error: \n Before Login You Must Check Your Disclaimer Box."*/
        /*else if (!(latitude > 0 && longitude > 0))
            msg = "We need location to validate your login"*/

        return msg
    }

    fun loginWaiver() {

        val loginDataWaiver = UserLoginWaiverModel(
            email,
        )

        if (loginMode == TYPE_MENTEE) loginMenteeWaiver(loginDataWaiver) else loginMentorWaiver(loginDataWaiver)
        //loginMenteeWaiver(loginDataWaiver)

    }

    private fun loginMenteeWaiver(loginDataWaiver: UserLoginWaiverModel) {

        disposable =
            menteeApiService.loginMenteeWaiver(loginDataWaiver)
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe { }
                .doAfterTerminate { }
                .subscribe(
                    { result ->
                        if (result.status == Status.SUCCESS) {
                            status= result.status.toString()
                            message= result.message.toString()

                            activity?.showMessageWaiver(true)



                        } else {
                            status= result.status.toString()
                            message= result.message.toString()
                            activity?.showMessageWaiver(false)
                        }
                    },
                    { error ->
                        activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                    }
                )

    }


    private fun loginMentorWaiver(loginDataWaiver: UserLoginWaiverModel) {

        disposable =
            menteeApiService.loginMentorWaiver(loginDataWaiver)
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe { }
                .doAfterTerminate { }
                .subscribe(
                    { result ->
                        if (result.status == Status.SUCCESS) {
                            status= result.status.toString()
                            message= result.message.toString()

                            activity?.showMessageWaiver(true)



                        } else {
                            status= result.status.toString()
                            message= result.message.toString()
                            activity?.showMessageWaiver(false)
                        }
                    },
                    { error ->
                        activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                    }
                )

    }

    fun login(msgDisplay: Boolean) {

        if(msgDisplay){
            is_waiver_acknowledged=0
        }else{
            is_waiver_acknowledged=1
        }


        val validate: String? = getFieldsValidity()

        if (!validate.equals(VALID)) {
            activity.showToast(validate)
            return
        }
        val loginData = UnifiedUserLoginModel(
            email,
            password.get(),
            loginMode,
            firebaseToken,
            disclamerId.toString(),
            deviceType,
            latitude.toString(),
            longitude.toString(),
            is_waiver_acknowledged
        )


        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        loginUnified(loginData)


    }

    private fun loginUnified(loginData: UnifiedUserLoginModel) {

        disposable =
            unifiedApiService.login(loginData)
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe { activity.isBusyLoadingData(true) }
                .doAfterTerminate { activity.isBusyLoadingData(false) }
                .subscribe(
                    { result ->
                        activity.isBusyLoadingData(false)
                        if (result.status) {
                            userPrefs?.apply {
                                setData(KEY_SUBMIT_TOKEN, result.data?.submitedTipsToken)
                                setData(KEY_AUTH_TOKEN, result.data?.token)
                                setData(KEY_LOGIN_MODE, result.data?.userDetails?.userType)
                                setData(KEY_FIRST_NAME, result.data?.userDetails?.firstname)
                                setData(KEY_MIDDLE_NAME, result.data?.userDetails?.middlename)
                                setData(KEY_LAST_NAME, result.data?.userDetails?.lastname)
                                setData(KEY_EMAIL, result.data?.userDetails?.email)
                                setData(KEY_PROFILE_PIC, result.data?.userDetails?.image)
                                setData(KEY_USER_ID, result.data?.userDetails?.id)
                                setData(KEY_LOGIN_MODE, result.data?.userType)

                            }
                            if (result.data?.userDetails?.isNew == 1) {
                                activity.apply {
                                    startActivity<ChangePasswordActivity>()
                                }
                            } else {
                                activity.apply {
                                    if (result.data?.userType.equals("mentor")){
                                        activity.startActivity(activity.intentFor<MentorMyProfileActivity>().clearTask()
                                            .newTask())
                                    }
                                    else {
                                        activity.startActivity(activity.intentFor<MenteeMyProfileActivity>().clearTask()
                                            .newTask())
                                    }
                                    finish()
                                    dismissKeyboard()
                                }
                            }
                        } else {

                            activity.alert(result.message.toString())
                            {
                                okButton { }
                            }.show()
                        }
                    },
                    { error ->
                        activity.alert("Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                        {
                            okButton { }
                        }.show()
                        activity.isBusyLoadingData(false)
                    }
                )
    }


    fun getSystemMessage() {
        if (loginMode == TYPE_MENTEE) getMenteeSystemMessage() else getMentorSystemMessage()
    }

    private fun loginMentee(loginData: UserLoginModel) {

        disposable =
            menteeApiService.loginMentee(loginData)
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe { activity.isBusyLoadingData(true) }
                .doAfterTerminate { activity.isBusyLoadingData(false) }
                .subscribe(
                    { result ->
                        activity.isBusyLoadingData(false)
                        if (result.status) {
                            userPrefs?.apply {
                                setData(KEY_SUBMIT_TOKEN, result.data?.submitedTipsToken)
                                setData(KEY_AUTH_TOKEN, result.data?.token)
                                setData(KEY_LOGIN_MODE, result.data?.userDetails?.userType)
                                setData(KEY_FIRST_NAME, result.data?.userDetails?.firstname)
                                setData(KEY_MIDDLE_NAME, result.data?.userDetails?.middlename)
                                setData(KEY_LAST_NAME, result.data?.userDetails?.lastname)
                                setData(KEY_EMAIL, result.data?.userDetails?.email)
                                setData(KEY_PROFILE_PIC, result.data?.userDetails?.image)
                                setData(KEY_USER_ID, result.data?.userDetails?.id)
                                setData(KEY_LOGIN_MODE, KEY_LOGIN_MENTEE)

                            }
                            if (result.data?.userDetails?.isNew == 1) {
                                activity.apply {
                                    startActivity<ChangePasswordActivity>()
                                }
                            } else {
                                activity.apply {
                                    activity.startActivity(
                                        activity.intentFor<MenteeMyProfileActivity>().clearTask()
                                            .newTask()
                                    )
                                    finish()
                                    dismissKeyboard()
                                }
                            }
                        } else {

                            activity.alert(result.message.toString())
                            {
                                okButton { }
                            }.show()
                        }
                    },
                    { error ->
                        activity.alert("Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                        {
                            okButton { }
                        }.show()
                        activity.isBusyLoadingData(false)
                    }
                )
    }

    private fun loginMentor(loginData: UserLoginModel) {
        disposable =
            mentorApiService.loginMentor(loginData)
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe { activity.isBusyLoadingData(true) }
                .doAfterTerminate {
                    activity.isBusyLoadingData(
                        false
                    )
                }
                .subscribe(
                    { result ->
                        activity.isBusyLoadingData(false)
                        if (result.status) {
                            //password.set("")
                            userPrefs?.apply {
                                setData(KEY_AUTH_TOKEN, result.data?.token)
                                setData(KEY_FIRST_NAME, result.data?.userDetails?.firstname)
                                setData(KEY_MIDDLE_NAME, result.data?.userDetails?.middlename)
                                setData(KEY_LAST_NAME, result.data?.userDetails?.lastname)
                                setData(KEY_EMAIL, result.data?.userDetails?.email)
                                setData(KEY_PROFILE_PIC, result.data?.userDetails?.image)
                                setData(KEY_USER_ID, result.data?.userDetails?.id)
                                setData(KEY_LOGIN_MODE, KEY_LOGIN_MENTOR)
                            }

                            activity.apply {
                                startActivity(
                                    intentFor<MentorMyProfileActivity>().clearTask().newTask()
                                )
                                finish()
                                dismissKeyboard()
                            }

                        } else {
                            activity.alert(result.message.toString())
                            {
                                okButton { }
                            }.show()
                        }


                    },
                    { error ->
                        activity.alert("Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                        {
                            okButton { }
                        }.show()
                        activity.isBusyLoadingData(false)
                    }
                )
    }

    fun getOtpAtEmail(email: String) {
        val service = unifiedApiService
            .forgetPassword(email)

        disposable = service
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

                        activity.startActivity<ForgotPasswordActivity>(INTENT_KEY_EMAIL to email)

                        //  activity.showToast("A One-Time-Password (OTP) password has been sent to your email address on file. You will need the OTP to establish your new password on the next screen. The OTP will expire in 30 minutes.")
                    } else {
                        result.message?.let { activity.showToast(it) }
                    }
                },
                { error ->
                    activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                }
            )
    }

    fun getMenteeSystemMessage() {

        if (!activity.isDeviceOnline()) {
            activity.toast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        disposable =
            menteeApiService.systemMessaging()
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe { }
                .doAfterTerminate { }
                .subscribe(
                    { result ->
                        if (result.status == Status.SUCCESS) {
                            result.data?.messaging?.passSystemMessageToBinding()
                        } else {
                            activity.showToast(result.message)
                        }
                    },
                    { error ->
                        activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                    }
                )
    }

    private fun List<SystemMessage.Messaging>.passSystemMessageToBinding() {
        activity?.binding?.contentLayout?.model = if (!isNullOrEmpty()) {
            get(0).let { message ->
                message.copy(shouldVisible = true, message = message.message + "\n")
            }
        } else {
            SystemMessage.Messaging()
        }
    }

    fun getMentorSystemMessage() {

        if (!activity.isDeviceOnline()) {
            activity.toast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        disposable =
            mentorApiService.systemMessaging()
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe { }
                .doAfterTerminate { }
                .subscribe(
                    { result ->
                        if (result.status == Status.SUCCESS) {
                            result.data?.messaging?.passSystemMessageToBinding()
                        } else {
                            activity.showToast(result.message)
                        }
                    },
                    { error ->
                        activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                    }
                )
    }

    fun disclaimer() {

        if (!activity.isDeviceOnline()) {
            activity.toast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }
        val dialog = activity.indeterminateProgressDialog("Loading data...").apply {
            setCancelable(false)
        }

        disposable =
            mentorApiService.disclaimer()
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe {dialog.show() }
                .doAfterTerminate {dialog.dismiss() }
                .subscribe(
                    { result ->
                        if (result.status == Status.SUCCESS) {
                            result.data.disclamer.apply {
                                activity.setTextTNC(statement.toString())
                                activity.url=url.toString()
                                disclamerId=id?:0
                            }
                        } else {
                            activity.showToast(result.message)
                        }
                    },
                    { error ->
                        activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                    }
                )
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

    fun dispose() {
        disposable?.dispose()
    }



}
