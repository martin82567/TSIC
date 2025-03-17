package com.tsic.util.extension

import android.app.Activity
import android.app.Application
import com.tsic.SplashActivity
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.ui.base.BaseApplication
import com.tsic.util.MyAppGlideModule
import org.jetbrains.anko.clearTask
import org.jetbrains.anko.intentFor
import org.jetbrains.anko.newTask

fun Activity.logoutForTnC(){
    PreferenceHelper.clearSharedPreferences(
        PreferenceHelper.customPrefs(
            this,
            USER_PREF
        )
    )

    startActivity(
        intentFor<SplashActivity>().clearTask()
            .newTask()
    )
    finish()
}