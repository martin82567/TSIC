package com.tsic.ui.screen.chat

import android.app.NotificationManager
import android.content.Context
import android.content.res.Configuration
import android.os.Bundle
import android.util.Log
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.BuildConfig
import com.tsic.R
import com.tsic.databinding.ActivityTwilioChatBinding
import com.tsic.ui.screen.videocallscreen.VideoCallActivity
import com.tsic.util.*
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.indeterminateProgressDialog
import org.jetbrains.anko.startActivity
import org.jetbrains.anko.toast


class TwilioChatActivity : AppCompatActivity() {

    val binding by lazy {
        DataBindingUtil.setContentView<ActivityTwilioChatBinding>(
            this,
            R.layout.activity_twilio_chat
        )
    }
    private val viewModel by lazy {
        TwilioChatViewModel(this)
    }
    val progressDialog by lazy {
        if (BuildConfig.DEBUG) null else indeterminateProgressDialog("Loading Chat History...").apply {
            setCancelable(false)
        }
    }
    var adapter: TwilioChatDetailsAdapter? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setStatusBarColor(R.color.colorStatusTranslucentGreen)
        when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
            Configuration.UI_MODE_NIGHT_NO -> {
                binding?.rootLayout?.setBackgroundResource(R.drawable.bg_all_white)
            } // Night mode is not active, we're using the light theme
            Configuration.UI_MODE_NIGHT_YES -> {
                binding?.rootLayout?.setBackgroundResource(R.drawable.bg3)
            } // Night mode is active, we're using dark theme
        }

        binding?.viewModel = viewModel
        viewModel.fetch()
        viewModel?.apply {
            chatterId.set(intent?.getStringExtra(INTENT_KEY_CHATTER_ID) ?: "")
            chatterName.set(intent?.getStringExtra(INTENT_KEY_CHATTER_NAME) ?: "")
            chatterPic.set(intent?.getStringExtra(INTENT_KEY_CHATTER_PIC) ?: "")
            chatterType.set(intent?.getStringExtra(INTENT_KEY_CHATTER_TYPE) ?: "")
            chatCode = intent?.getStringExtra(INTENT_KEY_CHAT_CODE) ?: ""
            chatSid = intent?.getStringExtra(INTENT_KEY_CHAT_SID) ?: ""
            // firebaseToken.set(intent?.getStringExtra(INTENT_KEY_FIREBASE_TOKEN) ?: "")
            if (chatterType.get() == TYPE_MENTOR_STAFF || chatterType.get() == TYPE_MENTEE_STAFF || chatterType.get() == TYPE_MENTOR)
                videoButtonEnable.set(false)
        }
        Log.d("TAG", "onCreate: ${viewModel.chatSid} ${viewModel.chatCode}")
        adapter = TwilioChatDetailsAdapter(viewModel.chatMsgList, viewModel.identity)

        binding?.contentChatMessage?.apply {
            back.setOnClickListener {
                finish()
                dismissKeyboard()
            }
            rvChatMessageList?.adapter = adapter
            rvChatMessageList?.setHasFixedSize(true)
            sendMsg.setOnClickListener {
//                if (isSend) return@setOnClickListener
                val msg = binding?.viewModel?.mymsg?.get() ?: ""
                if (msg.isNotBlank()) {
                    viewModel?.sendChatMessage(msg.trim())
                } else {
                    showToast("Can't send empty message")
                }
            }
            btnVideoCall.setOnClickListener {
                startActivity<VideoCallActivity>(
                    "call_from" to "Web Call",
                    "receiver_id" to binding?.viewModel?.chatterId?.get(),
                )
            }
        }

    }

    override fun onResume() {
        super.onResume()
        viewModel?.onResume()
        clearNotification()
    }

    override fun onPause() {
        super.onPause()
        viewModel.onPause()
    }

    override fun onDestroy() {
        super.onDestroy()
        viewModel.onDestroy()
    }
    fun showToast(msg: String) {
        toast(msg)
    }

    fun clearNotification() {
        val notificationManager =
            getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.cancel(105)
        notificationManager.cancel(106)
        notificationManager.cancel(107)
        notificationManager.cancel(108)
    }
}