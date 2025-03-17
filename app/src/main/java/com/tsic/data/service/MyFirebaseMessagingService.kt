package com.tsic.data.service

/**
 * @author Kaiser Perwez
 */

import android.content.Intent
import android.content.SharedPreferences
import android.util.Log
import com.google.firebase.messaging.FirebaseMessagingService
import com.google.firebase.messaging.RemoteMessage
import com.tsic.R
import com.tsic.data.local.prefs.KEY_FIREBASE_TOKEN
import com.tsic.data.local.prefs.KEY_LOGIN_MODE
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.remote.api.busy
import com.tsic.data.remote.api.finishUI
import com.tsic.data.remote.api.isCallDisconnect
import com.tsic.data.remote.api.isShowCallUIOneTime
import com.tsic.data.remote.api.timestamp
import com.tsic.util.BROADCAST_END_CALL
import com.tsic.util.BROADCAST_SHOW_LOG_SESSION_POPUP
import com.tsic.util.TYPE_MENTEE
import com.tsic.util.TYPE_MENTEE_STAFF
import com.tsic.util.TYPE_MENTOR
import kotlin.math.log


class MyFirebaseMessagingService : FirebaseMessagingService() {
    private val TAG = "MyFirebaseMsgService"
    private val notification by lazy {
        Notification(this)
    }

    override fun onNewToken(token: String) {
        val userPrefs: SharedPreferences? by lazy {
            PreferenceHelper.customPrefs(this, USER_PREF)
        }
        userPrefs?.apply { PreferenceHelper.setData(KEY_FIREBASE_TOKEN, token) }

    }

    @ExperimentalStdlibApi
    override fun onMessageReceived(remoteMessage: RemoteMessage) {

        Log.d(TAG, "From: " + remoteMessage!!.from!!)

        // Check if message contains an empty data payload.
        if (remoteMessage.data.isEmpty()) return
        Log.d(TAG, "Message data payload: " + remoteMessage.data)

        var type = remoteMessage.data["type"]
        Log.d(TAG, "onMessageReceived: $type")
        if (type == "meeting") {
            val title = remoteMessage.data["title"] ?: ""
            val text = remoteMessage.data["message"] ?: ""
            val unread_chat = remoteMessage.data["unread_chat"] ?: "0"
            val unread_task = remoteMessage.data["unread_task"] ?: "0"
            notification.showTipStatusNotification(
                title,
                text,
                unread_chat.toInt() + unread_task.toInt(),
                remoteMessage.notification?.channelId ?: getString(R.string.app_name)
            )

        } else if (type == "message_center") {
            val title = remoteMessage.data["title"] ?: ""
            val text = remoteMessage.data["message"] ?: ""
            notification.showMessageNotification(
                title,
                text,
                remoteMessage.notification?.channelId ?: getString(R.string.app_name)
            )

        } else if (type == "miss_call") {
            val title = remoteMessage.data["title"] ?: ""
            Log.d(TAG, "onMessageReceived: misscall title $title")
            val sender_name = remoteMessage.data["sender_name"] ?: ""
            timestamp = remoteMessage.data["timestamp"]?.toLong() ?: 0
            val message = remoteMessage.data["message"] ?: ""
            disconnectCall()
            notification.showMissCallNotification(
                sender_name,
                "You have a miss call", 110,
                remoteMessage.notification?.channelId ?: getString(R.string.app_name)
            )
            disconnectCall()

        } else if (type == "denied_call") {
            /*val title = remoteMessage.data["title"] ?: ""
            Log.d(TAG, "onMessageReceived: denied title $title")
            val sender_name = remoteMessage.data["sender_name"] ?: ""
            timestamp = remoteMessage.data["timestamp"]?.toLong() ?: 0
            val message = remoteMessage.data["message"] ?: ""*/
            sendBroadcast(Intent(BROADCAST_END_CALL))
            disconnectCall()
        } else if (type == "video_chat") {
            val title = remoteMessage.data["title"] ?: ""
            Log.d(TAG, "onMessageReceivedttile: $title")
            if (title == "Video Call Cancel") {
                Log.d(TAG, "onMessageReceivedttilesecond: $title")
                timestamp = remoteMessage.data["timestamp"]?.toLong() ?: 0
                disconnectCall()
            } else {
                val timestamp1 = remoteMessage.data["timestamp"]?.toLong() ?: 0
                if (timestamp < timestamp1) {
                    if (!busy) {
                        val text = remoteMessage.data["sender_name"] ?: ""
                        val receiverAccessToken = remoteMessage.data["receiver_accesstoken"] ?: ""
                        val roomName = remoteMessage.data["unique_name"] ?: ""
                        val roomSid = remoteMessage.data["room_sid"] ?: ""
                        val remainingTime: String = remoteMessage.data["remaining_time"] ?: ""
                        val createdAt: String = remoteMessage.data["created_at"] ?: ""
                        val callFrom: String = remoteMessage.data["call_from"] ?: ""

                        notification.showVideoCallNotification(
                            "$text Calling",
                            "You have a video call request. Tap to accept",
                            text,
                            receiverAccessToken,
                            roomName,
                            roomSid, remainingTime,
                            createdAt, callFrom
                        )
                    }
                }
            }

        } else if (type == "meeting_notification" || type == "goal_notification" || type == "task_notification") {
            val title = remoteMessage.data["title"] ?: ""
            val message = remoteMessage.data["message"] ?: ""
            val unread_chat = remoteMessage.data["unread_chat"] ?: "0"
            val unread_task = remoteMessage.data["unread_task"] ?: "0"
            val notificationId = when (type) {
                "meeting_notification" -> 102
                "goal_notification" -> 103
                else -> 104
            }
            notification.showMeetingNotification(
                title,
                message,
                unread_chat.toInt() + unread_task.toInt(),
                notificationId,
                remoteMessage.notification?.channelId ?: getString(R.string.app_name)
            )

        } else if (type == "twilio_chat") {
            val title = remoteMessage.data["title"] ?: ""
            val message = remoteMessage.data["message"] ?: ""
            var fromWhere = remoteMessage.data["comes_from"] ?: ""
            val unread_chat = remoteMessage.data["unread_chat"] ?: "1"
            if (fromWhere.lowercase() == "staff") {
                val v = PreferenceHelper.customPrefs(this, USER_PREF)?.getString(KEY_LOGIN_MODE, "")
                fromWhere = "${v}_staff"
            }
            val notificationId = when (fromWhere) {
                TYPE_MENTOR -> 105
                TYPE_MENTEE -> 106
                TYPE_MENTEE_STAFF -> 107
                else -> 108
            }
            notification.showChatNotification(
                title,
                message,
                notificationId,
                unread_chat.toInt(), fromWhere,
                remoteMessage.notification?.channelId ?: getString(R.string.app_name)
            )

        }

    }

    private fun disconnectCall() {
        finishUI = true
        isCallDisconnect = true
        isShowCallUIOneTime = 1

    }


}
