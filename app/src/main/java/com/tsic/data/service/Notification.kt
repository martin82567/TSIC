package com.tsic.data.service

import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.content.Context
import android.content.Intent
import android.content.SharedPreferences
import android.graphics.BitmapFactory
import android.media.RingtoneManager
import androidx.core.app.NotificationCompat
import com.tsic.R
import com.tsic.SplashActivity
import com.tsic.data.local.prefs.KEY_LOGIN_MENTOR
import com.tsic.data.local.prefs.KEY_LOGIN_MODE
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.remote.api.finishUI
import com.tsic.data.remote.api.isShowCallUIOneTime
import com.tsic.ui.common.ForegroundCheckTask
import com.tsic.ui.screen.mentee_bottom_menu.mychats.my_mentor_list.MenteeMyMentorListActivity
import com.tsic.ui.screen.mentee_bottom_menu.mychats.my_staff_list.MenteeMyStaffListActivity
import com.tsic.ui.screen.mentor_bottom_menu.mychats.my_mentee_list.MentorMyMenteeChatListActivity
import com.tsic.ui.screen.mentor_bottom_menu.mychats.my_staff_list.MentorMyStaffChatListActivity
import com.tsic.ui.screen.message_center.MessageCenterActivity
import com.tsic.ui.screen.receivevideocall.ReceiveVideoCallActivity
import com.tsic.util.TYPE_MENTEE
import com.tsic.util.TYPE_MENTOR

class Notification(val context: Context) {

    fun showChatNotification(
        title: String,
        body: String,
        id: Int,
        total: Int,
        type: String,
        channelId: String
    ) {
        val userPrefs: SharedPreferences? by lazy {
            PreferenceHelper.customPrefs(context, USER_PREF)
        }
        val intentNotif = Intent(
            context,
            if (userPrefs?.getString(
                    KEY_LOGIN_MODE,
                    ""
                ) == KEY_LOGIN_MENTOR
            ) if (type == TYPE_MENTEE)
                MentorMyMenteeChatListActivity::class.java
            else
                MentorMyStaffChatListActivity::class.java
            else if (type == TYPE_MENTOR)
                MenteeMyMentorListActivity::class.java
            else
                MenteeMyStaffListActivity::class.java
        )
        intentNotif.flags = Intent.FLAG_ACTIVITY_CLEAR_TOP

        val intentPending =
            PendingIntent.getActivity(context, 0, intentNotif, PendingIntent.FLAG_ONE_SHOT or PendingIntent.FLAG_IMMUTABLE)

        val uriDefaultSound = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION)
        val notifyImage = BitmapFactory.decodeResource(context.resources, R.drawable.app_logo)

        val builder =
            NotificationCompat.Builder(context, channelId)
                .setSmallIcon(R.drawable.tsic)//use a white icon with transparency inside. Just like an outline icon
                //   .setColor(resources.getColor(R.color.material_blue_gray_800))
                .setLargeIcon(notifyImage)
                .setContentTitle(title)
                .setContentText(body)
                // .setContentText(remoteMessage.getNotification().getBody())
                .setContentInfo("")
                .setPriority(NotificationCompat.PRIORITY_HIGH)
                .setAutoCancel(true)
                .setTicker(context.getString(R.string.app_name))
                .setSound(uriDefaultSound)
                .setNumber(total)
                .setContentIntent(intentPending)
                .setStyle(
                    NotificationCompat.BigTextStyle()
                        .bigText(body)
                )
        val notificationManager =
            context.getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                channelId,
                "Chat",
                NotificationManager.IMPORTANCE_DEFAULT
            )
            channel.description = body.toUpperCase()
            channel.setShowBadge(true)
            notificationManager.createNotificationChannel(channel)
        }
        notificationManager.notify(id, builder.build())

    }

    fun showMissCallNotification(
        title: String,
        body: String,
        id: Int,
        channelId: String
    ) {

        val intentNotif = Intent(
            context,
            SplashActivity::class.java
        )
        intentNotif.flags = Intent.FLAG_ACTIVITY_CLEAR_TOP

        val intentPending =
            PendingIntent.getActivity(context, 0, intentNotif, PendingIntent.FLAG_ONE_SHOT or PendingIntent.FLAG_IMMUTABLE)

        val uriDefaultSound = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION)
        val notifyImage = BitmapFactory.decodeResource(context.resources, R.drawable.app_logo)

        val builder =
            NotificationCompat.Builder(context, channelId)
                .setSmallIcon(R.drawable.tsic)//use a white icon with transparency inside. Just like an outline icon
                //   .setColor(resources.getColor(R.color.material_blue_gray_800))
                .setLargeIcon(notifyImage)
                .setContentTitle(title)
                .setContentText(body)
                // .setContentText(remoteMessage.getNotification().getBody())
                .setContentInfo("")
                .setPriority(NotificationCompat.PRIORITY_HIGH)
                .setAutoCancel(true)
                .setTicker(context.getString(R.string.app_name))
                .setSound(uriDefaultSound)
                .setNumber(1)
                .setContentIntent(intentPending)
                .setStyle(
                    NotificationCompat.BigTextStyle()
                        .bigText(body)
                )
        val notificationManager =
            context.getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                channelId,
                "MissCall",
                NotificationManager.IMPORTANCE_DEFAULT
            )
            channel.description = body.toUpperCase()
            channel.setShowBadge(true)
            notificationManager.createNotificationChannel(channel)
        }
        notificationManager.notify(id, builder.build())

    }

    fun showTipStatusNotification(
        title: String,
        body: String,
        total: Int,
        channelId: String
    ) {

        val intentNotif = Intent(context, SplashActivity::class.java)
        intentNotif.flags = Intent.FLAG_ACTIVITY_CLEAR_TOP

        val intentPending =
            PendingIntent.getActivity(context, 0, intentNotif, PendingIntent.FLAG_ONE_SHOT or PendingIntent.FLAG_IMMUTABLE)

        val uriDefaultSound = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION)
        val notifyImage = BitmapFactory.decodeResource(context.resources, R.drawable.app_logo)

        val builder =
            NotificationCompat.Builder(context, channelId)
                .setSmallIcon(R.drawable.tsic)//use a white icon with transparency inside. Just like an outline icon
                //   .setColor(resources.getColor(R.color.material_blue_gray_800))
                .setLargeIcon(notifyImage)
                .setContentTitle(title)
                .setContentText(body)
                // .setContentText(remoteMessage.getNotification().getBody())
                .setContentInfo("")
                .setPriority(NotificationCompat.PRIORITY_HIGH)
                .setAutoCancel(true)
                .setTicker(context.getString(R.string.app_name))
                .setSound(uriDefaultSound)
                .setNumber(total)
                .setContentIntent(intentPending)
                .setStyle(
                    NotificationCompat.BigTextStyle()
                        .bigText(body)
                )
        val notificationManager =
            context.getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                channelId,
                "TipStatus",
                NotificationManager.IMPORTANCE_DEFAULT
            )
            channel.description = body.toUpperCase()
            channel.setShowBadge(true)
            notificationManager.createNotificationChannel(channel)
        }
        notificationManager.notify(1, builder.build())

    }

fun showMessageNotification(
        title: String,
        body: String,
        channelId: String
    ) {

        val intentNotif = Intent(context, MessageCenterActivity::class.java)
        intentNotif.flags = Intent.FLAG_ACTIVITY_CLEAR_TOP

        val intentPending =
            PendingIntent.getActivity(context, 0, intentNotif, PendingIntent.FLAG_ONE_SHOT or PendingIntent.FLAG_IMMUTABLE)

        val uriDefaultSound = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION)
        val notifyImage = BitmapFactory.decodeResource(context.resources, R.drawable.app_logo)

        val builder =
            NotificationCompat.Builder(context, channelId)
                .setSmallIcon(R.drawable.tsic)//use a white icon with transparency inside. Just like an outline icon
                //   .setColor(resources.getColor(R.color.material_blue_gray_800))
                .setLargeIcon(notifyImage)
                .setContentTitle(title)
                .setContentText(body)
                // .setContentText(remoteMessage.getNotification().getBody())
                .setContentInfo("")
                .setPriority(NotificationCompat.PRIORITY_HIGH)
                .setAutoCancel(true)
                .setTicker(context.getString(R.string.app_name))
                .setSound(uriDefaultSound)
                .setContentIntent(intentPending)
                .setStyle(
                    NotificationCompat.BigTextStyle()
                        .bigText(body)
                )
        val notificationManager =
            context.getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                channelId,
                "TipStatus",
                NotificationManager.IMPORTANCE_DEFAULT
            )
            channel.description = body.toUpperCase()
            channel.setShowBadge(true)
            notificationManager.createNotificationChannel(channel)
        }
        notificationManager.notify(31, builder.build())

    }

    fun showMeetingNotification(
        title: String,
        body: String,
        total: Int,
        notificationId: Int,
        channelId: String
    ) {

        val intentNotif = Intent(context, SplashActivity::class.java)
        intentNotif.flags = Intent.FLAG_ACTIVITY_CLEAR_TOP

        val intentPending =
            PendingIntent.getActivity(context, 0, intentNotif, PendingIntent.FLAG_ONE_SHOT or PendingIntent.FLAG_IMMUTABLE)

        val uriDefaultSound = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION)
        val notifyImage = BitmapFactory.decodeResource(context.resources, R.drawable.app_logo)

        val builder =
            NotificationCompat.Builder(context, channelId)
                .setSmallIcon(R.drawable.tsic)//use a white icon with transparency inside. Just like an outline icon
                //   .setColor(resources.getColor(R.color.material_blue_gray_800))
                .setLargeIcon(notifyImage)
                .setContentTitle(title)
                .setContentText(body)
                // .setContentText(remoteMessage.getNotification().getBody())
                .setContentInfo("")
                .setPriority(NotificationCompat.PRIORITY_HIGH)
                .setAutoCancel(true)
                .setTicker(context.getString(R.string.app_name))
                .setSound(uriDefaultSound)
                .setNumber(total)
                .setContentIntent(intentPending)
                .setStyle(
                    NotificationCompat.BigTextStyle()
                        .bigText(body)
                )
        val notificationManager =
            context.getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                channelId,
                "Meeting",
                NotificationManager.IMPORTANCE_DEFAULT
            )
            channel.description = body.toUpperCase()
            channel.setShowBadge(true)
            notificationManager.createNotificationChannel(channel)
        }
        notificationManager.notify(notificationId, builder.build())

    }

    fun showVideoCallNotification(
        title: String,
        body: String,
        name: String,
        token: String,
        roomName: String,
        roomSid: String,
        remainingTime: String,
        createdAt: String,
        callFrom: String
    ) {
        val foregroud = ForegroundCheckTask().execute(context).get()
        finishUI = false
        isShowCallUIOneTime = 0
        if (foregroud) {
            val intentNotif = Intent(context, ReceiveVideoCallActivity::class.java)
            intentNotif.flags = Intent.FLAG_ACTIVITY_NEW_TASK
            intentNotif.putExtra("token", token)
            intentNotif.putExtra("roomName", roomName)
            intentNotif.putExtra("roomSid", roomSid)
            intentNotif.putExtra("name", name)
            intentNotif.putExtra("remainingTime", remainingTime)
            intentNotif.putExtra("created_at", createdAt)
            intentNotif.putExtra("call_from", callFrom)
            context.startActivity(intentNotif)

        } else {
            val intentNotif = Intent(context, ReceiveVideoCallActivity::class.java)
            intentNotif.flags = Intent.FLAG_ACTIVITY_NEW_TASK
            intentNotif.putExtra("token", token)
            intentNotif.putExtra("roomName", roomName)
            intentNotif.putExtra("roomSid", roomSid)
            intentNotif.putExtra("name", name)
            intentNotif.putExtra("remainingTime", remainingTime)
            intentNotif.putExtra("created_at", createdAt)
            intentNotif.putExtra("call_from", callFrom)
            context.startActivity(intentNotif)

        }
    }



}