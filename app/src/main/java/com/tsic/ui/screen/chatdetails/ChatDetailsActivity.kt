package com.tsic.ui.screen.chatdetails

/**
 * @author Kaiser Perwez
 */

//import com.tsic.ui.screen.videocallscreen.VideoCallActivity
import android.content.res.Configuration
import android.os.Bundle
import android.os.Handler
import android.util.Log
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.BuildConfig
import com.tsic.R
import com.tsic.data.remote.api.CHAT_URL
import com.tsic.databinding.ActivityChatDetailsBinding
import com.tsic.ui.screen.videocallscreen.VideoCallActivity
import com.tsic.util.*
import com.tsic.util.extension.InitSocket
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.setStatusBarColor
import io.socket.client.Socket
import org.jetbrains.anko.*
import org.json.JSONException
import org.json.JSONObject


class ChatDetailsActivity : AppCompatActivity(), SocketListener {

    //declarations
    val binding by lazy {
        DataBindingUtil.setContentView<ActivityChatDetailsBinding>(
            this,
            R.layout.activity_chat_details
        )
    }

    private val SEND_MESSAGE_TIMEOUT = 200L
    var isSend = false
    private val handler = Handler()
    private val runnable by lazy {
        Runnable {
            isSend = false
        }
    }
    val progressDialog by lazy {
        indeterminateProgressDialog("Loading Chat History...").apply {
            setCancelable(false)
        }
    }

    //methods
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()
        when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
            Configuration.UI_MODE_NIGHT_NO -> {
                binding?.rootLayout?.setBackgroundResource(R.drawable.bg_all_white)
            } // Night mode is not active, we're using the light theme
            Configuration.UI_MODE_NIGHT_YES -> {
                binding?.rootLayout?.setBackgroundResource(R.drawable.bg3)
            } // Night mode is active, we're using dark theme
        }
    }

    private fun initUiAndListeners() {
        binding.vm = ChatDetailsViewModel(this)
        binding?.activity = this
        binding?.contentChatMessage?.apply {
            back.setOnClickListener {
                finish()
                dismissKeyboard()
            }
            sendMsg.setOnClickListener {
                if (isSend) return@setOnClickListener
                val msg = binding?.vm?.mymsg?.get()?.toString() ?: ""
                if (msg.isNotBlank()) {
                    sendMsgToSocket(msg.trim())
                } else {
                    showToast("Can't send empty message")
                }
            }
            btnVideoCall.setOnClickListener {
                startActivity<VideoCallActivity>(
                    "receiver_id" to binding?.vm?.chatterId?.get(),
                    "call_from" to "Web Call"
                )
            }
        }
        setStatusBarColor(R.color.colorStatusTranslucentGreen)

        binding?.vm?.apply {
            chatterId.set(intent?.getStringExtra(INTENT_KEY_CHATTER_ID) ?: "")
            chatterName.set(intent?.getStringExtra(INTENT_KEY_CHATTER_NAME) ?: "")
            chatterPic.set(intent?.getStringExtra(INTENT_KEY_CHATTER_PIC) ?: "")
            chatterType.set(intent?.getStringExtra(INTENT_KEY_CHATTER_TYPE) ?: "")
            // firebaseToken.set(intent?.getStringExtra(INTENT_KEY_FIREBASE_TOKEN) ?: "")
            if (chatterType.get() == TYPE_MENTOR_STAFF || chatterType.get() == TYPE_MENTEE_STAFF || chatterType.get() == TYPE_MENTOR)
                videoButtonEnable.set(false)
        }
    }

    fun showToast(msg: String) {
        toast(msg)
    }

    override fun onResume() {
        super.onResume()

        binding?.vm?.apply {
            if (chatCode.isBlank())
                fetchMsgList()
            else
                connectSocket(chatCode)
        }
    }

    override fun onPause() {
        super.onPause()
        binding?.vm?.apply {
            if (!chatCode.isBlank()) {
                val jsonObject = JSONObject()
                try {
                    var v = binding?.vm?.myId
                    jsonObject.put("id", v)
                    socket.emit("roomExit", jsonObject)
                } catch (e: JSONException) {
                    e.printStackTrace()
                }
                socket?.disconnect()

            }
        }

        binding?.vm?.onPause()
    }


    private val socket by lazy {
        InitSocket().initSocket(CHAT_URL)
    }

    override fun connectSocket(chatCode: String) {
        socket?.on(Socket.EVENT_CONNECT) { args ->
            if (chatCode.isBlank()) {
                binding?.vm?.fetchMsgList()
                return@on
            }

            val jsonObject = JSONObject()
            try {
                jsonObject.put("id", binding?.vm?.myId)
                jsonObject.put("chat_code", chatCode)
                socket.emit("connected", jsonObject)
            } catch (e: JSONException) {
                e.printStackTrace()
            }
        }

        socket?.on("receiveMessage") { args ->
            binding?.vm?.page = 0
            binding?.vm?.fetchMsgList(false)
        }
        socket?.on("new_participant_joined") { args ->
            val reader = JSONObject(args[0].toString())
            Log.d("TAG", "connectSocket: ${reader["id"]}")
            if (reader["id"] != binding?.vm?.myId) {
                binding?.vm?.page = 0
                binding?.vm?.fetchMsgList(false)
            }
        }
        socket?.connect()

    }

    override fun sendMsgToSocket(msg: String) {

        val jsonObject = JSONObject()
        try {
            binding?.vm?.apply {

                jsonObject.put("chat_code", chatCode)
                jsonObject.put("sender_id", myId)
                jsonObject.put("sender_name", myName)
                jsonObject.put("receiver_id", chatterId.get())
                jsonObject.put("receiver_name", chatterName.get())
                jsonObject.put("device_token", firebaseToken.get())
                jsonObject.put("device_type", deviceType.get())
                jsonObject.put("message", msg)
                jsonObject.put("from_where", myLoginMode)
                jsonObject.put("time_zone", timeZone.get())
                jsonObject.put(
                    "type", chatterType.get() ?: TYPE_MENTEE
                )
                jsonObject.put("receiver_is_read", "0")
                socket.emit("sendMessage", jsonObject)
                isSend = true
                handler.postDelayed(runnable, SEND_MESSAGE_TIMEOUT)
                binding?.contentChatMessage?.typeMsg?.setText("")
            }

        } catch (e: JSONException) {
            e.printStackTrace()
        }
    }

    fun isBusyLoadingData(yes: Boolean) {
        if (!BuildConfig.DEBUG) {
            binding?.contentChatMessage?.pageLoader?.visibility = View.GONE
            if (yes) progressDialog.show() else progressDialog.dismiss()
        }
    }

}

interface  SocketListener {
    fun connectSocket(chatCode: String)
    fun sendMsgToSocket(msg: String)
}