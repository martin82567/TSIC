package com.tsic.data.service
/*

import android.Manifest
import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.app.Service
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.graphics.BitmapFactory
import android.location.Location
import android.media.RingtoneManager
import android.os.Build
import android.os.CountDownTimer
import android.os.IBinder
import android.util.Log
import androidx.core.app.ActivityCompat
import androidx.core.app.NotificationCompat
import androidx.core.content.ContextCompat
import com.tsic.R
import com.tsic.SplashActivity
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.model.mentor_api.TodaysMeetingModel
import com.tsic.data.remote.api.MentorApiService
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers

class FetchLocationService : Service() {

    private val CHANNEL_ID = "FetchLocationService"
    val todayMeetingModelList = mutableListOf<TodaysMeetingModel>()
    val todayMeetingModelListTemp = mutableListOf<TodaysMeetingModel>()
    var notificationSend = true

    companion object {
        var lat = 0.0
        var lon = 0.0


        fun startLocationService(context: Context) {
            val startIntent = Intent(context, FetchLocationService::class.java)
            ContextCompat.startForegroundService(context, startIntent)
        }

        fun stopLocationService(context: Context) {
            val stopIntent = Intent(context, FetchLocationService::class.java)
            context.stopService(stopIntent)

        }


    }

    override fun onCreate() {
        super.onCreate()
        if (ActivityCompat.checkSelfPermission(
                this,
                Manifest.permission.ACCESS_FINE_LOCATION
            ) != PackageManager.PERMISSION_GRANTED && ActivityCompat.checkSelfPermission(
                this,
                Manifest.permission.ACCESS_COARSE_LOCATION
            ) != PackageManager.PERMISSION_GRANTED
        )
            return


        fetchTodayMeetings(
            PreferenceHelper.customPrefs(this, USER_PREF)
                ?.getString(KEY_AUTH_TOKEN, "").toString()
        )
        object : CountDownTimer(60000, 5000) {
            override fun onTick(millisUntilFinished: Long) {
                var token = PreferenceHelper.customPrefs(this@FetchLocationService, USER_PREF)
                    ?.getString(KEY_AUTH_TOKEN, "").toString()
                if (token == "")
                    this@FetchLocationService.stopSelf()
                val gpsTracker = GpsTracker(this@FetchLocationService)
                if (gpsTracker.canGetLocation()) {
                    lat = gpsTracker.getLatitude()
                    lon = gpsTracker.getLongitude()
                } else {
                    this@FetchLocationService.stopSelf()
                    //gpsTracker.showSettingsAlert()
                }
                todayMeetingModelList?.forEachIndexed { index, todaysMeetingModel ->
                    var distance = calculateDistance(
                        todaysMeetingModel.latitude,
                        todaysMeetingModel.longitude,
                        lat,
                        lon
                    )
                    if (distance < 1.6 && notificationSend) {
                        notificationSend = false
                        notificationForLocationReminder(
                            "TSIC",
                            "Today You have a session in ${todaysMeetingModel.school_name} school",
                            todaysMeetingModel.id
                        )
                    } else {
                        if (distance > 1.6)
                            notificationSend = true
                    }
                    Log.d(
                        "TAG",
                        "onTick: dis: $distance  ${todaysMeetingModel.latitude} ${todaysMeetingModel.longitude} $lat $lon"
                    )

                }

            }

            override fun onFinish() {
                var token = PreferenceHelper.customPrefs(this@FetchLocationService, USER_PREF)
                    ?.getString(KEY_AUTH_TOKEN, "").toString()
                if (token == "")
                    this@FetchLocationService.stopSelf()
                fetchTodayMeetings(token)
                start()
            }
        }.start()

    }

    override fun onStartCommand(intent: Intent?, flags: Int, startId: Int): Int {
        //do heavy work on a background thread
        createNotificationChannel()
        val notification = NotificationCompat.Builder(this, CHANNEL_ID)
            .setContentTitle("TSIC")
            .setContentText("Accessing your location")
            .setSmallIcon(R.drawable.tsic)
            .setSound(null)
            .setVibrate(null)
            .build()
        startForeground(878, notification)
        //stopSelf();
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


    fun calculateDistance(lat1: Double, lon1: Double, lat2: Double, lon2: Double): Float {
        val startPoint = Location("locationA")
        startPoint.latitude = lat1
        startPoint.longitude = lon1

        val endPoint = Location("locationB")
        endPoint.latitude = lat2
        endPoint.longitude = lon2

        return startPoint.distanceTo(endPoint).div(1000)
    }

    private fun fetchTodayMeetings(token: String) {
        val disposable: Disposable = MentorApiService.create().fetchTodayMeetings(token)
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe {
            }
            .doAfterTerminate {
            }
            .subscribe { result ->
                if (result.status == Status.SUCCESS) {
                    result.data?.apply {
                        if (this.isNotEmpty() && isNotEqual(this, todayMeetingModelList)) {
                            todayMeetingModelList?.clear()
                            todayMeetingModelList.addAll(this)
                            notificationSend = true
                        }
                    }
                }
            }
    }

    private fun notificationForLocationReminder(
        title: String,
        body: String,
        channelId: String
    ) {
        val intentNotify = Intent(this, SplashActivity::class.java).also {
            it.flags = Intent.FLAG_ACTIVITY_CLEAR_TOP
        }
        val pendingIntent =
            PendingIntent.getActivity(this, 0, intentNotify, PendingIntent.FLAG_ONE_SHOT)
        val uriDefaultSound = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION)
        val notifyImage = BitmapFactory.decodeResource(resources, R.drawable.app_logo)
        val builder =
            NotificationCompat.Builder(applicationContext, channelId)
                .setSmallIcon(R.drawable.tsic)
                .setLargeIcon(notifyImage)
                .setContentTitle(title)
                .setContentText(body)
                .setContentInfo("")
                .setPriority(NotificationCompat.PRIORITY_HIGH)
                .setAutoCancel(true)
                .setTicker(getString(R.string.app_name))
                .setSound(uriDefaultSound)
                .setContentIntent(pendingIntent)
                .setStyle(
                    NotificationCompat.BigTextStyle()
                        .bigText(body)
                )
        val notificationManager =
            getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                channelId,
                "CHANNEL_NAME",
                NotificationManager.IMPORTANCE_DEFAULT
            )
            channel.description = body.toUpperCase()
            channel.setShowBadge(true)
            notificationManager.createNotificationChannel(channel)
        }
        notificationManager.notify(161, builder.build())
    }

    private fun <T> isNotEqual(first: List<T>, second: List<T>): Boolean {
        if (first.size != second.size) {
            return true
        }
        return !first.zip(second).all { (x, y) -> x == y }
    }
}

*/

