package com.tsic.ui.screen.receivevideocall

import android.app.KeyguardManager
import android.content.Context
import android.content.Intent
import android.media.Ringtone
import android.media.RingtoneManager
import android.net.ConnectivityManager
import android.net.Network
import android.net.NetworkCapabilities
import android.net.NetworkRequest
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.os.CountDownTimer
import android.util.Log
import android.view.WindowManager
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.SplashActivity
import com.tsic.data.local.prefs.KEY_LOGIN_MODE
import com.tsic.data.local.prefs.KEY_USER_ID
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.model.Status
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.data.remote.api.busy
import com.tsic.data.remote.api.finishUI
import com.tsic.data.remote.api.isCallDisconnect
import com.tsic.data.remote.api.isShowCallUIOneTime
import com.tsic.databinding.ActivityReceiveVideoCallBinding
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.MenteeModalBottomSheet.Companion.TAG
import com.tsic.ui.screen.videocallscreen.InitVideoCallRoom
import com.tsic.ui.screen.videocallscreen.InitVideoCallRoom.Companion
import com.tsic.ui.screen.videocallscreen.InitVideoCallSocket
import com.tsic.ui.screen.videocallscreen.RoomCallback
import com.tsic.ui.screen.videocallscreen.VideoCallActivity
import com.tsic.ui.screen.videocallscreen.showLowBandwidthAlert
import com.tsic.util.BROADCAST_SHOW_LOG_SESSION_POPUP
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.logoutForTnC
import com.tsic.util.extension.setStatusBarColor
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.schedulers.Schedulers
import org.jetbrains.anko.toast
import us.zoom.sdk.ZoomVideoSDK


class ReceiveVideoCallActivity : AppCompatActivity() {
    //declarations
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityReceiveVideoCallBinding>(
            this,
            R.layout.activity_receive_video_call
        )
    }
    val initSocket: InitVideoCallSocket? by lazy { InitVideoCallSocket(this) }
    val userPrefs by lazy {
        PreferenceHelper.getSharedPrefs(this)
    }

    private var ringtone: Ringtone? = null
    var lastToast = ""
    val SENDER_TYPE = 0
    val SENDER_ID = 1
    val RECEIVER_TYPE = 2
    val RECEIVER_ID = 3

    private val roomCallback = object : RoomCallback {
        override fun onConnected(isRemoteParticipantPresent: Boolean) {}

        override fun onReconnected() {}

        override fun onReconnecting() {}

        override fun onConnectFailure() {}

        override fun onDisconnected() {
            if (binding?.viewModel?.isReceiver == false)
                binding?.viewModel?.callDisconnect()
            if (lastToast != "End Call") {
                showToast("End Call")
                sendBroadcast(Intent(BROADCAST_SHOW_LOG_SESSION_POPUP))
                finish()
                busy = false
                lastToast = "End Call"
            }
        }

        override fun onParticipantConnected() {
            Log.e("ZOOM", "onParticipantConnected")
        }

        override fun onParticipantDisconnected() {
            Log.e("ZOOM", "onParticipantDisconnected")
        }

        override fun onVideoTrackSubscribed(userId: String) {
        }
    }

    private fun createRingtone() {
        try {
            val notification: Uri = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_RINGTONE)
            ringtone = RingtoneManager.getRingtone(applicationContext, notification)
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.P) {
                ringtone?.isLooping = true
            }
            ringtone?.play()
        } catch (e: Exception) {
            Log.e("ReceiveVideoCallActivity", "Error creating ringtone: ${e.message}")
        }
    }

    private val initVideoCallRoom by lazy {
        InitVideoCallRoom(this, roomCallback)
    }

    //  var isCallDisconnect = true
    //private var room: Room? = null
    private var token: String? = null
    private var roomName: String? = null
    private var isClicked: Boolean = false
    private val zoomSdk = ZoomVideoSDK.getInstance()

    /*    private val roomListener = object : Room.Listener {vcdbh
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

            override fun onParticipantDisconnected(room: Room, remoteParticipant: RemoteParticipant) {
            }

            override fun onRecordingStarted(room: Room) {
            }

            override fun onRecordingStopped(room: Room) {
            }
        }*/
    private val apiService by lazy { MenteeApiService.create() }
    val userId: Int?
        get() = userPrefs?.getInt(KEY_USER_ID, 0)
    val loginType: String?
        get() = userPrefs?.getString(KEY_LOGIN_MODE, "")
    fun showToast(msg: String) {
        toast(msg)
    }

    override fun onStart() {
        super.onStart()
        createRingtone()

        val connectivityManager = getSystemService(Context.CONNECTIVITY_SERVICE) as ConnectivityManager
        val networkRequest = NetworkRequest.Builder().build()

        connectivityManager.registerNetworkCallback(networkRequest, object : ConnectivityManager.NetworkCallback() {

            override fun onLost(network: Network) {
                super.onLost(network)
                showLowBandwidthAlert(this@ReceiveVideoCallActivity)
            }

            override fun onLosing(network: Network, maxMsToLive: Int) {
                super.onLosing(network, maxMsToLive)
                showLowBandwidthAlert(this@ReceiveVideoCallActivity)
            }

            override fun onCapabilitiesChanged(
                network: Network,
                capabilities: NetworkCapabilities
            ) {
                super.onCapabilitiesChanged(network, capabilities)
                val linkDownstreamBandwidthKbps = capabilities.linkDownstreamBandwidthKbps
                if (linkDownstreamBandwidthKbps < 1000) {
                 //   showLowBandwidthAlert(this@ReceiveVideoCallActivity)
                }
            }
        })
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        window.addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON)
        setStatusBarColor(R.color.colorStatusTranslucentGreen)
        busy = true
        ringtone?.play()
        val name = intent?.getStringExtra("name") ?: ""
        token = intent?.getStringExtra("token") ?: ""
        roomName = intent?.getStringExtra("roomName") ?: ""
        val remainingTime: String = intent?.getStringExtra("remainingTime") ?: ""
        val createdAt: String = intent?.getStringExtra("created_at") ?: ""
        val callFrom: String = intent?.getStringExtra("call_from") ?: ""

        setDisconnectClock(remainingTime)
        binding?.apply {
            textView5.text = name
            btnAccept.setOnClickListener {
                isShowCallUIOneTime = 1
                finishUI = true
                isCallDisconnect = false
                Intent(
                    this@ReceiveVideoCallActivity,
                    VideoCallActivity::class.java
                ).also {
                    it.putExtra("token", token)
                    it.putExtra("roomName", roomName)
                    it.putExtra("remainingTime", remainingTime)
                    it.putExtra("created_at", createdAt)
                    it.putExtra("call_from", callFrom)
                    startActivity(it)
                }
                ringtone?.stop()
                isClicked = true
                finish()

            }
            btnCancel.setOnClickListener {
                isCallDisconnect = true
                isClicked = true
                isShowCallUIOneTime = 1
                finishUI = true
                ringtone?.stop()
                disconnectToRoom(callFrom == "web")
            }
        }
        ReceiveVideoCallService.startService(this)

    }

    private fun callDisconnect(sendDeniedWebhook: Boolean = false) {

        dismissKeyboard()
        if (!isDeviceOnline()) {
            toast("No internet connection.")
            return
        }
        Log.d("MyTag", "callDisconnect: $roomName ")
        if (sendDeniedWebhook) {
            Log.d(TAG, "call cancel: Web Call")

            roomName?.split("-")?.also {
                Log.d(TAG, "call cancel: ${it.size} $it")
                Log.d(TAG, "call cancel: $initSocket")
                if (it.isNotEmpty()) {
                    initSocket?.endBeforeReceived(
                        it[RECEIVER_TYPE],
                        it[RECEIVER_ID],
                        it[SENDER_TYPE],
                        it[SENDER_ID],
                        true
                    )
                }

            }
        }
        val disposable = apiService.callDenied(roomName ?: "", userId ?: 0,  loginType ?: "mentor")
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe {

            }
            .doAfterTerminate {

            }
            .subscribe(
                { result ->
                    if (result.status == Status.SUCCESS) {
                        busy = false
                        finish()
                    } else {
                        if (result.message == "Logged Out") {
                            logoutForTnC()
                        } else {
                            toast(result.message.toString())
                        }
                        finish()
                    }
                },
                { error ->
                    toast("Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                    finish()
                }
            )
    }

    fun disconnectToRoom(sendDeniedWebhook: Boolean = false) {

        /* val connectOptionsBuilder =
             ConnectOptions.Builder(accessToken)
                 .bandwidthProfile(
                     BandwidthProfileOptions(
                         VideoBandwidthProfileOptions.Builder().build()
                     ))
                 .roomName(roomName)

         room = Video.connect(this, connectOptionsBuilder.build(), roomListener)*/
        Log.d("MyTag", "disconnectToRoom: $roomName")
        callDisconnect(sendDeniedWebhook)
        zoomSdk.leaveSession(true)
        finish()
    }


    private fun setDisconnectClock(remainingTime: String) {
        val remainingCounter: Long =
            if (remainingTime.toLong() > 52) 48000 else remainingTime.toLong() - 4
        object : CountDownTimer(remainingCounter, 1000) {
            override fun onTick(millisUntilFinished: Long) {
                if (finishUI) {
                    ringtone?.stop()
                    busy = false
                    finish()
                }
            }

            override fun onFinish() {
                if (isCallDisconnect) {
                    busy = false
                    finish()
                }
            }
        }.start()

    }

    override fun onAttachedToWindow() {
        super.onAttachedToWindow()
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O_MR1) {
            setShowWhenLocked(true)
            setTurnScreenOn(true)
            val keyguardManager = getSystemService(Context.KEYGUARD_SERVICE) as KeyguardManager
            keyguardManager.requestDismissKeyguard(this, null)
        } else {
            this.window.addFlags(
                WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON or
                        WindowManager.LayoutParams.FLAG_DISMISS_KEYGUARD or
                        WindowManager.LayoutParams.FLAG_SHOW_WHEN_LOCKED or
                        WindowManager.LayoutParams.FLAG_TURN_SCREEN_ON
            )
        }

    }

    override fun onPause() {
        super.onPause()
        ringtone?.stop()
    }

    override fun onBackPressed() {}

    override fun onResume() {
        super.onResume()
        if (isShowCallUIOneTime == 1) {
            Intent(this@ReceiveVideoCallActivity, SplashActivity::class.java).also {
                startActivity(it)
            }
            finish()
        }
        ringtone?.play()
    }

    override fun onDestroy() {
        if (!isClicked) {
            disconnectToRoom()
            //  uniqueName?.let { callDisconnect(it) }
        }
        ReceiveVideoCallService.stopService(this, isClicked)
        super.onDestroy()
    }
}