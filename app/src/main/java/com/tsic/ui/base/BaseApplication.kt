package com.tsic.ui.base

/**
 * @author Kaiser Perwez
 */


import android.app.Application
import android.util.Log
import com.tsic.BuildConfig
import us.zoom.sdk.ZoomVideoSDK
import us.zoom.sdk.ZoomVideoSDKErrors
import us.zoom.sdk.ZoomVideoSDKInitParams
import us.zoom.sdk.ZoomVideoSDKRawDataMemoryMode
import us.zoom.sdk.ZoomVideoSDKSessionContext

class BaseApplication : Application() {
    companion object {
        var passedMeetingId: Int? = null
        var upComingMeetingId: Int? = null
    }


    override fun onCreate() {
        super.onCreate()
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
