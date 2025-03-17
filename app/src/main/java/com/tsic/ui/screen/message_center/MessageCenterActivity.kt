package com.tsic.ui.screen.message_center

/**
 * @author Kaiser Perwez
 */

import android.app.NotificationManager
import android.content.Context
import android.content.res.Configuration
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityMessageCenterBinding
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast


class MessageCenterActivity : AppCompatActivity() {


    //declarations
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityMessageCenterBinding>(
            this,
            R.layout.activity_message_center
        )
    }


    //methods
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        val notificationManager =
            getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.cancel(31)

        initUiAndListeners()
    }

    private fun initUiAndListeners() {
        binding?.rootLayout?.setBackgroundResource(
            when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
                Configuration.UI_MODE_NIGHT_NO ->
                    R.drawable.bg_all_white
                Configuration.UI_MODE_NIGHT_YES ->
                    R.drawable.bg3
                else -> R.drawable.bg_all_white
            }
        )
        binding.vm = MessageCenterViewModel(this)
        binding?.vm?.messageCenter()
        setStatusBarColor(R.color.colorStatusTranslucentGreen)

        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            title = "Announcement"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }

    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.progressBar?.visibility = if (yes) View.VISIBLE else View.INVISIBLE

    }

    fun showToast(msg: String) {
        toast(msg)
    }

    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
    }

    override fun onPause() {
        super.onPause()
        binding?.vm?.onPause()
    }

    override fun onStop() {
        super.onStop()
        binding?.vm?.onStop()
    }


}
