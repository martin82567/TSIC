package com.tsic.ui.screen.initvideocallscreen

import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import com.tsic.databinding.ActivityInitVideoCallBinding
import com.tsic.ui.screen.videocallscreen.InitVideoCallSocket

class InitVideoCallActivity : AppCompatActivity() {
    private val binding by lazy {
        ActivityInitVideoCallBinding.inflate(layoutInflater)
    }

    private val receiverId by lazy { intent?.getStringExtra("receiver_id") ?: "" }

    private val receiverType by lazy { intent?.getStringExtra("receiver_type") ?: "" }

    private val receiverDeviceType by lazy {
        intent?.getStringExtra("receiver_device_type") ?: ""
    }

    private val receiverDeviceToken by lazy {
        intent?.getStringExtra("receiver_device_token") ?: ""
    }

    private val senderId by lazy { intent?.getStringExtra("sender_id") ?: "" }

    private val senderType by lazy { intent?.getStringExtra("sender_type") ?: "" }

    lateinit var initSocket: InitVideoCallSocket


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(binding.root)
    }


}