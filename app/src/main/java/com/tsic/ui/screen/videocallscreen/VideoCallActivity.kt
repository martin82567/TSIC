package com.tsic.ui.screen.videocallscreen

/**
 * @author Kaiser Perwez
 */

import android.Manifest
import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.content.IntentFilter
import android.content.pm.PackageManager
import android.media.AudioManager
import android.os.Build
import android.os.Bundle
import android.os.Handler
import android.util.Log
import android.view.View
import android.view.WindowManager
import androidx.appcompat.app.AppCompatActivity
import androidx.core.app.ActivityCompat
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.data.local.prefs.KEY_FIRST_NAME
import com.tsic.data.local.prefs.KEY_LOGIN_MENTOR
import com.tsic.data.local.prefs.KEY_LOGIN_MODE
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.remote.api.busy
import com.tsic.databinding.ActivityVideoCallScreenBinding
import com.tsic.util.BROADCAST_END_CALL
import com.tsic.util.BROADCAST_SHOW_LOG_SESSION_POPUP
import kotlinx.android.synthetic.main.content_video_call_screen.primaryVideoView
import org.jetbrains.anko.toast
import us.zoom.sdk.ZoomVideoSDK
import us.zoom.sdk.ZoomVideoSDKSessionContext
import us.zoom.sdk.ZoomVideoSDKVideoAspect
import us.zoom.sdk.ZoomVideoSDKVideoResolution


class VideoCallActivity : AppCompatActivity(), RoomCallback {


    private val TAG = "video call"
    private val CAMERA_MIC_PERMISSION_REQUEST_CODE = 1
    internal var showTime = false
    var totalsec: Long = 30 * 60
    var createdAt: String = ""
    var callFrom: String = ""
    var isCallDisconnect = true
    val SENDER_TYPE = 0
    val SENDER_ID = 1
    val RECEIVER_TYPE = 2
    val RECEIVER_ID = 3
    val zoomSdk = ZoomVideoSDK.getInstance()


    /* private var localAudioTrack: LocalAudioTrack? = null
     private var localVideoTrack: LocalVideoTrack? = null*/
    private val initVideoCallRoom by lazy {
        InitVideoCallRoom(this, this)
    }
    private val timer by lazy {
        TimerClass(this)
    }

    /*    private val cameraCapturer by lazy {
            CameraCapturerCompat()
        }
       private val videoCallSocket by lazy { InitSocket().initSocket(VIDEO_URL) }
   */
    //declarations
    internal val binding by lazy {
        DataBindingUtil.setContentView<ActivityVideoCallScreenBinding>(
            this,
            R.layout.activity_video_call_screen
        )
    }
    var initSocket: InitVideoCallSocket? = null
    private val handler = Handler()
    private val runnable by lazy {
        Runnable {
            binding?.contentLayout?.viewModel?.getAccessToken()
        }
    }
    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(this, USER_PREF)
    }
    private val username: String?
        get() = userPrefs?.getString(KEY_FIRST_NAME, "")

    /*
        val username = "${userPrefs?.getString(KEY_FIRST_NAME, "")} ${
            userPrefs?.getString(
                KEY_MIDDLE_NAME,
                ""
            )
        } ${
            userPrefs?.getString(
                KEY_LAST_NAME, ""
            )
        } "*/
    private val deniedCallBroadcastReceiver = object : BroadcastReceiver() {
        override fun onReceive(context: Context?, intent: Intent?) {
            showToast("Call Disconnected")
            finish()
        }
    }

    private fun setSeesionDeniedBroadcastReceiver() {
        val filter = IntentFilter(BROADCAST_END_CALL)
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            registerReceiver(deniedCallBroadcastReceiver, filter, RECEIVER_NOT_EXPORTED)
        } else {
            registerReceiver(deniedCallBroadcastReceiver, filter)
        }
        //  registerReceiver(sessionLogBroadcastReceiver, IntentFilter(BROADCAST_SHOW_LOG_SESSION_POPUP))
    }

    //    methods
    override fun onStart() {
        super.onStart()
        setSeesionDeniedBroadcastReceiver()
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        val audioManager = getSystemService(Context.AUDIO_SERVICE) as AudioManager
        audioManager.mode = AudioManager.MODE_IN_CALL
        audioManager.isSpeakerphoneOn = true
        volumeControlStream = AudioManager.STREAM_VOICE_CALL
        requestPermissionForCameraAndMicrophone()
        initUiAndListeners()
        VideoCallService.startService(this)
    }

    private fun initSocket() {
        initSocket = InitVideoCallSocket(this@VideoCallActivity)
    }


    override fun onAttachedToWindow() {
        super.onAttachedToWindow()
        window.addFlags(
            WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON or
                    WindowManager.LayoutParams.FLAG_DISMISS_KEYGUARD or
                    WindowManager.LayoutParams.FLAG_SHOW_WHEN_LOCKED or
                    WindowManager.LayoutParams.FLAG_TURN_SCREEN_ON
        )
    }

    //
    private fun initUiAndListeners() {
        window.addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON)
        binding?.contentLayout?.viewModel = VideoCallViewModel(this)
        binding?.contentLayout?.viewModel?.receiverId = intent?.getStringExtra(
            "receiver_id"
        ) ?: ""

        val roomName = intent?.getStringExtra(
            "roomName"
        ) ?: ""

        binding?.contentLayout?.viewModel?.uniqueName = roomName
        totalsec = (intent?.getStringExtra(
            "remainingTime"
        ) ?: "${60}").toLong()
        createdAt = intent?.getStringExtra(
            "created_at"
        ) ?: ""
        callFrom = intent?.getStringExtra(
            "call_from"
        ) ?: ""

        if (roomName != "") {
            callFrom = "web"
        }
        if (callFrom == "web") {
            initSocket()
            //   val roomName = intent?.getStringExtra("roomName") ?: ""
            val data = roomName.split("-")
            Log.d(TAG, "initUiAndListeners: $roomName")
            initSocket?.getTimer(
                data[RECEIVER_TYPE],
                data[RECEIVER_ID],
                data[SENDER_TYPE],
                data[SENDER_ID]
            )
        }
        if (callFrom == "Web Call") {
            initSocket()
        }
        binding?.btnDisconnect?.setOnClickListener {
            Log.d(TAG, "initUiAndListeners: $callFrom")
            if (callFrom == "Web Call") {
                Log.d(TAG, "call cancel: Web Call")

                binding?.contentLayout?.viewModel?.roomUserData?.also {
                    Log.d(TAG, "call cancel: ${it.size} $it")
                    Log.d(TAG, "call cancel: $initSocket")
                    if (it.size != 0) {
                        initSocket?.endBeforeReceived(
                            it[RECEIVER_TYPE],
                            it[RECEIVER_ID],
                            it[SENDER_TYPE],
                            it[SENDER_ID]
                        )
                    }

                }
            }
            if (isCallDisconnect) {
                binding?.btnDisconnect?.isClickable = false
                binding?.contentLayout?.viewModel?.isReceiver = false
                if (binding?.contentLayout?.viewModel?.uniqueName == "")
                    finish()
                else
                    binding?.contentLayout?.viewModel?.callDisconnect()
                isCallDisconnect = false
                binding?.contentLayout?.viewModel?.callStatus?.set("Disconnecting...")
            } else {
                binding?.contentLayout?.viewModel?.callDisconnect()
                finish()
            }

        }


    }

    override fun onBackPressed() {}

    private fun requestPermissionForCameraAndMicrophone() {
        if (ActivityCompat.shouldShowRequestPermissionRationale(this, Manifest.permission.CAMERA) ||
            ActivityCompat.shouldShowRequestPermissionRationale(
                this,
                Manifest.permission.RECORD_AUDIO
            ) || ActivityCompat.shouldShowRequestPermissionRationale(
                this,
                Manifest.permission.MODIFY_AUDIO_SETTINGS
            )
        ) {
            showToast("Camera and Microphone permissions needed.")
        } else {
            ActivityCompat.requestPermissions(
                this,
                arrayOf(
                    Manifest.permission.CAMERA,
                    Manifest.permission.RECORD_AUDIO,
                    Manifest.permission.MODIFY_AUDIO_SETTINGS
                ),
                CAMERA_MIC_PERMISSION_REQUEST_CODE
            )
        }
    }

    override fun onRequestPermissionsResult(
        requestCode: Int,
        permissions: Array<String>,
        grantResults: IntArray
    ) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        if (requestCode == CAMERA_MIC_PERMISSION_REQUEST_CODE) {
            var cameraAndMicPermissionGranted = true

            for (grantResult in grantResults) {
                cameraAndMicPermissionGranted = cameraAndMicPermissionGranted and
                        (grantResult == PackageManager.PERMISSION_GRANTED)
            }

            if (cameraAndMicPermissionGranted) {
                createAudioAndVideoTracks()

                if (binding?.contentLayout?.viewModel?.receiverId == "") {
                    connectToRoom(
                        intent?.getStringExtra(
                            "roomName"
                        ) ?: "", intent?.getStringExtra(
                            "token"
                        ) ?: ""
                    )
                    isCallDisconnect = false
                } else {
                    val loginType =
                        binding?.contentLayout?.viewModel?.userPrefs?.getString(KEY_LOGIN_MODE, "")
                    val delay: Long = if (loginType == KEY_LOGIN_MENTOR) 4000 else 5500
                    handler.postDelayed(runnable, delay)
                }
            } else {
                showToast("Camera and Microphone permissions needed.")
            }
        }
    }


    private fun createAudioAndVideoTracks() {
        zoomSdk.videoHelper.startVideo()
        Log.d(TAG, "createAudioAndVideoTracks: ")
        zoomSdk.session.mySelf.videoCanvas.subscribe(
            binding?.contentLayout?.thumbnailVideoView,
            ZoomVideoSDKVideoAspect.ZoomVideoSDKVideoAspect_PanAndScan,
            ZoomVideoSDKVideoResolution.ZoomVideoSDKResolution_Auto
        )


    }

    fun showToast(msg: String) {
        toast(msg)
    }

    fun isBusyLoadingData(isBusy: Boolean) {}

    fun connectToRoom(roomName: String, accessToken: String) {

        val connectOptionsBuilder = ZoomVideoSDKSessionContext().apply {
            userName = username
            sessionName = roomName
            token = accessToken
        }

        /*   val connectOptionsBuilder =
               ConnectOptions.Builder(accessToken)
                   .bandwidthProfile(
                       BandwidthProfileOptions(
                           VideoBandwidthProfileOptions.Builder().build()
                       ))
                   .roomName(roomName)
           Log.d(TAG, "acctoken: $accessToken ")
           localAudioTrack?.let {
               connectOptionsBuilder.audioTracks(listOf(it))
           }
           localVideoTrack?.let {
               connectOptionsBuilder.videoTracks(listOf(it))
           }*/

        /* room = initVideoCallRoom.connect(connectOptionsBuilder)
         serviceRoom = room*/
        initVideoCallRoom.connect(connectOptionsBuilder)
        // zoomSdk.joinSession(connectOptionsBuilder)
    }

    fun btnDisable() {
        binding?.btnDisconnect?.isEnabled = false
    }

    fun btnEnable() {
        binding?.btnDisconnect?.isEnabled = true
    }

    private fun startRecording() {
        val recordingHelper = ZoomVideoSDK.getInstance().recordingHelper
        recordingHelper?.startCloudRecording()
    }

    override fun onDestroy() {
        VideoCallService.stopService(this)
        zoomSdk.leaveSession(true)
        Log.i("destroy", "destroy: Destroy All")
        timer.onDestroy()
        initSocket?.disconnect()
        /* localAudioTrack?.release()
         localVideoTrack?.release()*/
        handler.removeCallbacks(runnable)
        binding?.contentLayout?.viewModel?.callDisconnect()
        unregisterReceiver(deniedCallBroadcastReceiver)

        super.onDestroy()
    }

    //Room callback
    override fun onConnected(isRemoteParticipantPresent: Boolean) {
        busy = true

     //   startRecording()

        if (isRemoteParticipantPresent) {
            binding?.contentLayout?.viewModel?.type = "end_call"
            binding?.contentLayout?.viewModel?.callStatus?.set("")
            isCallDisconnect = false
            showTime = true
            binding?.contentLayout?.primaryVideoView2?.visibility = View.INVISIBLE
            binding?.contentLayout?.primaryVideoView?.visibility = View.VISIBLE
            binding?.contentLayout?.thumbnailVideoView?.visibility = View.VISIBLE
        } else {
            // binding?.contentLayout?.viewModel?.type = "miss_call"
            binding?.contentLayout?.viewModel?.callStatus?.set("Calling...")
            timer.setDisconnectClock()
            showTime = false
        }
        timer.callClock()
    }

    override fun onReconnected() {
        binding?.contentLayout?.viewModel?.callStatus?.set("")
    }

    override fun onReconnecting() {
        binding?.contentLayout?.viewModel?.callStatus?.set("Reconnecting...")
        showTime = false
    }

    override fun onConnectFailure() {
        busy = false
        finish()
    }

    override fun onDisconnected() {
        if (binding?.contentLayout?.viewModel?.isReceiver == false)
            binding?.contentLayout?.viewModel?.callDisconnect()

        showToast("Call End")
        busy = false
        sendBroadcast(Intent(BROADCAST_SHOW_LOG_SESSION_POPUP))
    }

    override fun onParticipantConnected() {
   //     startRecording()
        binding?.contentLayout?.viewModel?.callStatus?.set("")
        binding?.contentLayout?.viewModel?.type = "end_call"
        isCallDisconnect = false
        showTime = true
        binding?.contentLayout?.primaryVideoView2?.visibility = View.INVISIBLE
        binding?.contentLayout?.primaryVideoView?.visibility = View.VISIBLE
        binding?.contentLayout?.thumbnailVideoView?.visibility = View.VISIBLE
    }

    override fun onParticipantDisconnected() {
        Log.d(TAG, "onParticipantDisconnected: ")
        finish()
    }

    override fun onVideoTrackSubscribed(userId: String) {
        zoomSdk.session.remoteUsers.firstOrNull { userId == it.userID }?.videoCanvas?.subscribe(
            primaryVideoView,
            ZoomVideoSDKVideoAspect.ZoomVideoSDKVideoAspect_PanAndScan,
            ZoomVideoSDKVideoResolution.ZoomVideoSDKResolution_Auto
        )
    }

    /* override fun onVideoTrackSubscribed(remoteVideoTrack: RemoteVideoTrack) {
         primaryVideoView.mirror = false
         remoteVideoTrack.addSink(primaryVideoView)
     }*/

}