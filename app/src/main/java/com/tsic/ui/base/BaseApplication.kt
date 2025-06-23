package com.tsic.ui.base

/**
 * @author Kaiser Perwez
 */


import android.app.Application
import android.util.Log
import androidx.lifecycle.DefaultLifecycleObserver
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.LifecycleObserver
import androidx.lifecycle.LifecycleOwner
import androidx.lifecycle.OnLifecycleEvent
import androidx.lifecycle.ProcessLifecycleOwner
import com.google.firebase.FirebaseApp
import com.tsic.BuildConfig
import us.zoom.sdk.ZoomVideoSDK
import us.zoom.sdk.ZoomVideoSDKErrors
import us.zoom.sdk.ZoomVideoSDKInitParams

class BaseApplication : Application() {
    companion object {
        var passedMeetingId: Int? = null
        var upComingMeetingId: Int? = null
    }


    override fun onCreate() {
        super.onCreate()
        ProcessLifecycleOwner.get().lifecycle.addObserver(AppVisibilityTracker)
        FirebaseApp.initializeApp(this)
        val zoomSdk = ZoomVideoSDK.getInstance()
        val initParams = ZoomVideoSDKInitParams().apply {
            domain = "https://zoom.us" // Required
            enableLog = BuildConfig.DEBUG // Optional for debugging
            logFilePrefix = "ZOOM" // Optional for debugging
        }
        val initResult = zoomSdk.initialize(this, initParams)
        if (initResult == ZoomVideoSDKErrors.Errors_Success) {

            Log.d("ZOOM", "onCreate: zoomSdkInitialized ")
            Log.d("ZOOM", "onCreate: sdk version is - ${zoomSdk.sdkVersion}")

        } else {
            Log.e("ZOOM", "onCreate: Failed zoomSdkInitialized ", )
        }
    }
}

object AppVisibilityTracker : DefaultLifecycleObserver {

    var isAppInForeground = false
        private set

    override fun onStart(owner: LifecycleOwner) {
        isAppInForeground = true
    }

    override fun onStop(owner: LifecycleOwner) {
        isAppInForeground = false
    }
}
