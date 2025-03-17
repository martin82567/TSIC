package com.tsic.ui.screen.videocallscreen

import android.app.Activity
import android.util.Log
import androidx.appcompat.app.AppCompatActivity
import com.google.gson.Gson
import com.tsic.data.local.prefs.KEY_LOGIN_MODE
import com.tsic.data.local.prefs.KEY_USER_ID
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.remote.api.VIDEO_URL
import com.tsic.util.extension.InitSocket
import io.socket.client.Socket
import org.json.JSONException
import org.json.JSONObject

class InitVideoCallSocket(activity: Activity) {
    private val videoCallSocket by lazy { InitSocket().initSocket(VIDEO_URL) }
    val userPrefs by lazy {
        PreferenceHelper.getSharedPrefs(activity)
    }
    private val loginType = userPrefs?.getString(KEY_LOGIN_MODE, "")
    private val userId = userPrefs?.getInt(KEY_USER_ID, 0)
    val gson: Gson = Gson()

    init {
        videoCallSocket.on(Socket.EVENT_CONNECT) { args ->
            val jsonObject = JSONObject()
            try {
                jsonObject.put("type", loginType)
                jsonObject.put("id", userId)
                videoCallSocket.emit("connected", jsonObject)
            } catch (e: JSONException) {
                e.printStackTrace()
            }
        }
        videoCallSocket.on("reqReceived") { args ->

        }
        videoCallSocket.on("endVideo") { args ->
            val jsonObject = JSONObject()
            jsonObject.put("denied_by", "app")
            activity.apply {
                finish()
            }
        }
        videoCallSocket.on("getTimerVal") { args ->
            Log.d("TAG", "getTimerVal:$args ")
            (activity as? VideoCallActivity)?.apply {
                binding?.contentLayout?.viewModel?.apply {
                    setTimer(
                        roomUserData[2],
                        roomUserData[3],
                        roomUserData[0],
                        roomUserData[1],
                        totalsec - 1
                    )
                }
            }
        }
        videoCallSocket.on("setTimerVal") { args ->
            val obj: JSONObject = args[0] as JSONObject
            Log.d("TAG", "setTimerVal:${obj.get("remainimgCallTime")}")
            try {
                val time: Long = obj.get("remainimgCallTime").toString().toLong().minus(1)
                (activity as? VideoCallActivity)?.totalsec = time
            } catch (e: Exception) {
                Log.d("TAG", "setTimerVal:${e.message} ")
            }

        }

        videoCallSocket.connect()
    }

    fun disconnect() {
        videoCallSocket.disconnect()
    }

    fun startCalling(
        receiver_type: String,
        receiver_id: String,
        sender_type: String,
        sender_id: String,
        receiver_device_type: String? = null,
        receiver_device_token: String? = null,
    ) {
        val jsonObject = JSONObject()
        try {
            jsonObject.put("receiver_type", receiver_type)
            jsonObject.put("receiver_id", receiver_id)
            jsonObject.put("sender_type", sender_type)
            jsonObject.put("sender_id", sender_id)
            receiver_device_type?.let {
                jsonObject.put("receiver_device_type", it)
            }
            receiver_device_token?.let {
                jsonObject.put("receiver_firebase_id", it)
            }
            videoCallSocket.emit("reqSend", jsonObject)
            Log.d("TAG", "startCalling: ${jsonObject.toString()}")
        } catch (e: JSONException) {
            e.printStackTrace()
        }
    }

    fun callDenied(
        receiver_type: String,
        receiver_id: String,
        sender_type: String,
        sender_id: String,
        receiver_device_type: String? = null,
        receiver_device_token: String? = null
    ) {
        val jsonObject = JSONObject()
        try {
            jsonObject.put("receiver_type", receiver_type)
            jsonObject.put("receiver_id", receiver_id)
            jsonObject.put("sender_type", sender_type)
            jsonObject.put("sender_id", sender_id)
            receiver_device_type?.let {
                jsonObject.put("receiver_device_type", it)
            }
            receiver_device_token?.let {
                jsonObject.put("receiver_firebase_id", it)
            }
            Log.d("TAG", "callDenied: $jsonObject")
            videoCallSocket.emit("callDenied", jsonObject)
        } catch (e: JSONException) {
            e.printStackTrace()
        }
    }

    fun endBeforeReceived(
        receiver_type: String,
        receiver_id: String,
        sender_type: String,
        sender_id: String,
        denied: Boolean = false,
        receiver_device_type: String? = null,
        receiver_device_token: String? = null,
    ) {
        val jsonObject = JSONObject()
        try {
            jsonObject.put("receiver_type", receiver_type)
            jsonObject.put("receiver_id", receiver_id)
            jsonObject.put("sender_type", sender_type)
            jsonObject.put("sender_id", sender_id)
            if (denied) {
                jsonObject.put("denied_by", "app")
            }

            receiver_device_type?.let {
                jsonObject.put("receiver_device_type", it)
            }
            receiver_device_token?.let {
                jsonObject.put("receiver_firebase_id", it)
            }
            Log.d("TAG", "callDenied: $jsonObject")
            videoCallSocket.emit("endBeforeReceived", jsonObject)
        } catch (e: JSONException) {
            e.printStackTrace()
        }
    }

    fun getTimer(
        receiver_type: String,
        receiver_id: String,
        sender_type: String,
        sender_id: String,
        receiver_device_type: String? = null,
        receiver_device_token: String? = null
    ) {
        val jsonObject = JSONObject()
        try {
            jsonObject.put("receiver_type", receiver_type)
            jsonObject.put("receiver_id", receiver_id)
            jsonObject.put("sender_type", sender_type)
            jsonObject.put("sender_id", sender_id)
            receiver_device_type?.let {
                jsonObject.put("receiver_device_type", it)
            }
            receiver_device_token?.let {
                jsonObject.put("receiver_firebase_id", it)
            }
            videoCallSocket.emit("getTimer", jsonObject)
        } catch (e: JSONException) {
            e.printStackTrace()
        }
    }

    fun setTimer(
        receiver_type: String,
        receiver_id: String,
        sender_type: String,
        sender_id: String,
        remainimgCallTime: Long
    ) {
        val jsonObject = JSONObject()
        try {
            jsonObject.put("receiver_type", receiver_type)
            jsonObject.put("receiver_id", receiver_id)
            jsonObject.put("sender_type", sender_type)
            jsonObject.put("sender_id", sender_id)
            jsonObject.put("remainimgCallTime", remainimgCallTime)
            videoCallSocket.emit("setTimer", jsonObject)
        } catch (e: JSONException) {
            e.printStackTrace()
        }
    }
}