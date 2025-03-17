package com.tsic

import android.app.Activity
import android.content.ActivityNotFoundException
import android.content.Intent
import android.content.SharedPreferences
import android.content.res.Configuration
import android.net.Uri
import android.os.Bundle
import android.os.Handler
import android.util.Log
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.appcompat.app.AppCompatDelegate
import com.google.firebase.iid.FirebaseInstanceId
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.KEY_DARK_MODE
import com.tsic.data.local.prefs.KEY_FIREBASE_TOKEN
import com.tsic.data.local.prefs.KEY_LOGIN_MENTOR
import com.tsic.data.local.prefs.KEY_LOGIN_MODE
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.PreferenceHelper.setData
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.remote.api.DEBUG
import com.tsic.data.remote.api.MentorApiService
import com.tsic.ui.screen.chooseloginmode.ChooseLoginModeActivity
import com.tsic.ui.screen.mentee_bottom_menu.myprofile.MenteeMyProfileActivity
import com.tsic.ui.screen.mentor_bottom_menu.myprofile.MentorMyProfileActivity
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.schedulers.Schedulers
import kotlinx.android.synthetic.main.activity_splash.root_layout
import org.jetbrains.anko.alert
import org.jetbrains.anko.configuration
import org.jetbrains.anko.startActivity
import org.jetbrains.anko.toast


class SplashActivity : AppCompatActivity() {

    val SPLASH_TIMEOUT = 2000L
    private val userPrefs: SharedPreferences? by lazy {
        PreferenceHelper.customPrefs(this, USER_PREF)
    }
    private val mentorApiService by lazy {
        MentorApiService.create()
    }
    private val handler = Handler()
    private val runnable by lazy {
        Runnable {
            if (userPrefs?.getString(KEY_AUTH_TOKEN, "").isNullOrBlank()) {
                startActivity<ChooseLoginModeActivity>()
            } else {
                if (userPrefs?.getString(KEY_LOGIN_MODE, "").equals(KEY_LOGIN_MENTOR)) {
                    startActivity<MentorMyProfileActivity>()
                } else {
                    startActivity<MenteeMyProfileActivity>()
                }
            }
            finish()
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_splash)
//        AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_YES)

        when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
            Configuration.UI_MODE_NIGHT_NO -> {
                if (userPrefs?.getString(KEY_DARK_MODE, "") == "0" ||
                    userPrefs?.getString(KEY_DARK_MODE, "") == ""
                ) {
                    root_layout?.setBackgroundResource(R.drawable.bg_all_white)
                } else {
                    root_layout?.setBackgroundResource(R.drawable.bg3)
                    AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_YES)
                }
            } // Night mode is not active, we're using the light theme
            Configuration.UI_MODE_NIGHT_YES -> {
                root_layout?.setBackgroundResource(R.drawable.bg3)
            } // Night mode is active, we're using dark theme
        }

    }

    override fun onResume() {
        super.onResume()
    //    checkForUpdates()
        if (false/*DEBUG*/)
            fetchFirebaseToken()
        else
            checkForUpdates()
    }

    /*private  fun fetchRemoteConfig(){
        val remoteConfig: FirebaseRemoteConfig = Firebase.remoteConfig
        val configSettings = remoteConfigSettings {
            minimumFetchIntervalInSeconds = 3600
        }
        remoteConfig.setConfigSettingsAsync(configSettings)
    }*/
    private fun fetchFirebaseToken() {
        FirebaseInstanceId.getInstance().instanceId.addOnSuccessListener { instanceIdResult ->
            instanceIdResult?.token?.let {
                userPrefs?.apply {
                    setData(KEY_FIREBASE_TOKEN, it)
                    Log.d("FIREBASE_TOKEN-->", it)
                    //sendTokenToServer(it)
                    handler.postDelayed(runnable, SPLASH_TIMEOUT)
                }
            }
        }
    }

    private fun checkForUpdates() {
        var versionCode = 0
        val disposable =
            mentorApiService.getVersionCode()
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe { }
                .doAfterTerminate { }
                .subscribe(
                    { result ->
                        if (result.status) {
                            result?.data?.appVersion?.let {
                                versionCode = it.versionCode
                                if (versionCode > BuildConfig.VERSION_CODE) {
                                    //alert and update
                                    alert(
                                        "A new version is available. Please update now.",
                                        "Update Available"
                                    ) {
                                        positiveButton("Update") {
                                            val uri =
                                                Uri.parse("http://play.google.com/store/apps/details?id=$packageName")
                                            val myAppLinkToMarket = Intent(Intent.ACTION_VIEW, uri)
                                            try {
                                                startActivity(myAppLinkToMarket)
                                                finish()
                                            } catch (e: ActivityNotFoundException) {
                                                Toast.makeText(
                                                    this@SplashActivity,
                                                    "Unable to find market app",
                                                    Toast.LENGTH_LONG
                                                ).show()
                                            }

                                        }
                                        isCancelable = false
                                    }.show()
                                } else {
                                    //continue
                                    fetchFirebaseToken()
                                }
                            }

                        } else {
                            showToast(result.message)
                            finish()
                        }
                    },
                    { error ->
                        showToast(error.message)
                        finish()
                    }
                )
    }

    /*private fun checkForUpdates() {
        // Creates instance of the manager.
        val appUpdateManager = AppUpdateManagerFactory.create(this)
        // Returns an intent object that you use to check for an update.
        val appUpdateInfoTask = appUpdateManager.appUpdateInfo


        // Checks that the platform will allow the specified type of update.
        appUpdateInfoTask?.addOnSuccessListener { appUpdateInfo ->
            val availability = appUpdateInfo.updateAvailability()
            if ((availability == UpdateAvailability.UPDATE_AVAILABLE || availability == UpdateAvailability.DEVELOPER_TRIGGERED_UPDATE_IN_PROGRESS)
                // For a flexible update, use AppUpdateType.FLEXIBLE
                && appUpdateInfo.isUpdateTypeAllowed(AppUpdateType.IMMEDIATE)
            ) {
                appUpdateManager.startUpdateFlowForResult(
                    // Pass the intent that is returned by 'getAppUpdateInfo()'.
                    appUpdateInfo,
                    // Or 'AppUpdateType.FLEXIBLE' for flexible updates.
                    AppUpdateType.IMMEDIATE,
                    // The current activity making the update request.
                    this,
                    // Include a request code to later monitor this update request.
                    UPDATE_REQUEST_CODE
                )
            } else
                fetchFirebaseToken()
        }
        appUpdateInfoTask?.addOnFailureListener {
            OnFailureListener {
                showToast(it.message)
            }
        }

    }
*/
    /*override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == UPDATE_REQUEST_CODE) {
            if (resultCode == RESULT_OK) {
                fetchFirebaseToken()
            } else {
                //("Update flow failed! Result code: $resultCode")
                // If the update is cancelled or fails,
                // you can request to start the update again.
                checkForUpdates()
            }
        }
    }*/
    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 101) {
            if (resultCode == Activity.RESULT_OK) {
                if (data?.getStringExtra("finish_activity") ?: "1" == "1")
                    finish()

            }
        }
    }

    fun showToast(msg: String?) {
        msg?.let {
            toast(it).show()
        }
    }

    override fun onPause() {
        super.onPause()
        handler.removeCallbacks(runnable)
    }

}
