package com.tsic.ui.screen.videocallscreen

import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.Service
import android.content.Context
import android.content.Intent
import android.os.Build
import android.os.IBinder
import android.util.Log
import androidx.annotation.RequiresApi
import androidx.core.app.NotificationCompat
import androidx.core.content.ContextCompat
import com.tsic.R
import us.zoom.sdk.ZoomVideoSDK

class VideoCallService : Service() {

    private val CHANNEL_ID = "VideoCallService"

    companion object {
        var serviceRoom = ZoomVideoSDK.getInstance()
        fun startService(context: Context) {
            val startIntent = Intent(context, VideoCallService::class.java)
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                context.startForegroundService(startIntent)
            } else{
                ContextCompat.startForegroundService(context, startIntent)
            }
        }

        fun stopService(context: Context) {
            val stopIntent = Intent(context, VideoCallService::class.java)
            Log.d("stopService", "stopService: $serviceRoom")
            serviceRoom?.leaveSession(true)
            context.stopService(stopIntent)
        }

    }

    override fun onStartCommand(intent: Intent?, flags: Int, startId: Int): Int {
        //do heavy work on a background thread
        createNotificationChannel()
        val notification = NotificationCompat.Builder(this, CHANNEL_ID)
            .setContentTitle("TSIC")
            .setContentText("Accessing your camera and microphone for video call")
            .setSmallIcon(R.drawable.tsic)
            .build()
        startForeground(904, notification)
        return START_NOT_STICKY
    }

    override fun onBind(intent: Intent): IBinder? {
        return null
    }

    private fun createNotificationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val serviceChannel = NotificationChannel(
                CHANNEL_ID, CHANNEL_ID,
                NotificationManager.IMPORTANCE_DEFAULT
            )
            val manager = getSystemService(NotificationManager::class.java)
            manager!!.createNotificationChannel(serviceChannel)
        }
    }
}