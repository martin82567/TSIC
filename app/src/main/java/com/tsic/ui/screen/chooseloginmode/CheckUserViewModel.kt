package com.tsic.ui.screen.chooseloginmode

import android.content.SharedPreferences
import android.util.Patterns
import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.*
import com.tsic.data.remote.api.UnifiedApiService
import com.tsic.ui.screen.login.LoginActivity
import com.tsic.data.model.login_api.UserCheckModel
import com.tsic.util.INTENT_KEY_EMAIL
import com.tsic.util.INTENT_KEY_LOGIN_MODE
import com.tsic.util.TYPE_MENTOR
import com.tsic.util.extension.dismissKeyboard
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers
import org.jetbrains.anko.*

class CheckUserViewModel(private val activity: ChooseLoginModeActivity) {
    private val unifiedApiService by lazy {
        UnifiedApiService.create()
    }
    private val VALID: String = ""
    var email = ObservableField("")
    var loginMode = ""
    private var disposable: Disposable? = null
    private val userPrefs: SharedPreferences? by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }
    private val firebaseToken = userPrefs?.getString(KEY_FIREBASE_TOKEN, "")

    private fun getFieldsValidity(): String {
        var msg = VALID
        if (email.get()?.trim()?.isBlank() == true)
            msg = "Error: \n You must provide an email address.Please try again with a valid email address. To validate your email address, please contact your local Take Stock in Children program."
        else if (!Patterns.EMAIL_ADDRESS.matcher(email.get()?.trim()).matches())
            msg = "Error: \n Your email address is invalid. Please check your email address and try again."
        /* else if (!activity.binding.contentLayout.cbTNC.isChecked)
             msg = "Error: \n Before Login You Must Check Your Disclaimer Box."*/
        /*else if (!(latitude > 0 && longitude > 0))
            msg = "We need location to validate your login"*/

        return msg
    }

     fun getUserDetails() {
         val validate: String? = getFieldsValidity()

         if (!validate.equals(VALID)) {
             activity.showToast(validate)
             return
         }
         val emailTxt = email.get();
         val userCheckData = UserCheckModel(
             email.get())
             disposable =
             unifiedApiService.checkUser(userCheckData)
                 .subscribeOn(Schedulers.io())
                 .observeOn(AndroidSchedulers.mainThread())
                 .doOnSubscribe { activity.isBusyLoadingData(true) }
                 .doAfterTerminate { activity.isBusyLoadingData(false) }
                 .subscribe(
                     { result ->
                         activity.isBusyLoadingData(false)
                         if (result.status) {
                             userPrefs?.apply {

                                 PreferenceHelper.setData(
                                     KEY_LOGIN_MODE,
                                     result.data?.userType
                                 )
                                 PreferenceHelper.setData(
                                     KEY_EMAIL,
                                     result.data?.email
                                 )
                                 PreferenceHelper.setData(KEY_USER_ID, result.data?.id)
                             }

                             activity.apply {
                                 activity.startActivity(
                                     activity.intentFor<LoginActivity>(
                                         INTENT_KEY_LOGIN_MODE to result.data?.userType,
                                         INTENT_KEY_EMAIL to result.data?.email
                                     ).clearTask()
                                         .newTask()
                                 )
//                                    if (result.data?.userDetails?.userType.equals("mentor")){
//                                        activity.startActivity(activity.intentFor<MentorMyProfil
//
//
//
//                                        eActivity>().clearTask()
//                                            .newTask())
//                                    }
//                                    else {
//                                        activity.startActivity(activity.intentFor<MenteeMyProfileActivity>().clearTask()
//                                            .newTask())
//                                    }
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

}