package com.tsic.ui.screen.receivevideocall

import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.Service
import android.content.Context
import android.content.Intent
import android.os.Build
import android.os.IBinder
import android.util.Log
import androidx.core.app.NotificationCompat
import androidx.core.content.ContextCompat
import com.tsic.R
import com.tsic.data.model.mentee_api.ZowodMsg
import com.tsic.ui.screen.videocallscreen.InitVideoCallRoom
import us.zoom.sdk.ZoomVideoSDK

class ReceiveVideoCallService : Service() {

    private val CHANNEL_ID = "ForegroundService"

    companion object {
        /* private val roomListener = object : Room.Listener {
             override fun onConnected(room: Room) {
             }

             override fun onConnectFailure(room: Room, twilioException: TwilioException) {
             }

             override fun onReconnecting(room: Room, twilioException: TwilioException) {
             }

             override fun onReconnected(room: Room) {
             }

             override fun onDisconnected(room: Room, twilioException: TwilioException?) {
             }

             override fun onParticipantConnected(room: Room, remoteParticipant: RemoteParticipant) {
             }

             override fun onParticipantDisconnected(
                 room: Room,
                 remoteParticipant: RemoteParticipant
             ) {
             }

             override fun onRecordingStarted(room: Room) {
             }

             override fun onRecordingStopped(room: Room) {
             }
         }*/

        fun startService(context: Context) {
            val startIntent = Intent(context, ReceiveVideoCallService::class.java)
            ContextCompat.startForegroundService(context, startIntent)
        }

        fun stopService(context: Context, isCheck: Boolean) {

            val stopIntent = Intent(context, ReceiveVideoCallService::class.java)
            Log.d("stopService", "stopService")
            if (!isCheck) {
                /* val connectOptionsBuilder =
                     ConnectOptions.Builder(accessToken)
                         .bandwidthProfile(
                             BandwidthProfileOptions(
                             VideoBandwidthProfileOptions.Builder().build()
                         ))
                         .roomName(roomName)
                 val room = Video.connect(context, connectOptionsBuilder.build(), roomListener)
                 Log.d("stopService", "inside $room")
                 Log.d("stopService", "inside $accessToken")
                 Log.d("stopService", "inside $roomName")
                 room.disconnect()

                 Log.d("stopService", "inside stopService")*/
                ZoomVideoSDK.getInstance().leaveSession(true)

            }
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
        startForeground(1, notification)
        //stopSelf();
        return START_NOT_STICKY
    }

    override fun onBind(intent: Intent): IBinder? {
        return null
    }

    private fun createNotificationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val serviceChannel = NotificationChannel(
                CHANNEL_ID, "Foreground Service Channel",
                NotificationManager.IMPORTANCE_DEFAULT
            )
            val manager = getSystemService(NotificationManager::class.java)
            manager!!.createNotificationChannel(serviceChannel)
        }
    }
}