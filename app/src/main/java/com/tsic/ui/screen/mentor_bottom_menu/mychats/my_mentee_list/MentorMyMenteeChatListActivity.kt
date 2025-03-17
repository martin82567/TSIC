package com.tsic.ui.screen.mentor_bottom_menu.mychats.my_mentee_list

import android.app.NotificationManager
import android.content.Context
import android.content.res.Configuration
import android.os.Bundle
import android.os.CountDownTimer
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityMentorMyMenteeChatListBinding
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast

class MentorMyMenteeChatListActivity : AppCompatActivity() {

    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityMentorMyMenteeChatListBinding>(
            this,
            R.layout.activity_mentor_my_mentee_chat_list
        )
    }

    private var disposable: CountDownTimer? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListner()
    }


    fun isBusyLoadingData(yes: Boolean) {
        binding?.swipeRefreshLayout?.isRefreshing = yes
    }


    private fun initUiAndListner() {
        when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
            Configuration.UI_MODE_NIGHT_NO -> {
                binding?.swipeRefreshLayout?.setBackgroundResource(R.drawable.bg_all_white)
            } // Night mode is not active, we're using the light theme
            Configuration.UI_MODE_NIGHT_YES -> {
                binding?.swipeRefreshLayout?.setBackgroundResource(R.drawable.bg3)
            } // Night mode is active, we're using dark theme
        }
        binding?.vm = MentorMyMenteeListViewModel(this)
        binding?.activity = this
        setStatusBarColor(R.color.colorStatusTranslucentGreen)
        setSupportActionBar(binding?.toolbar)
        supportActionBar?.apply {
            title = "MENTEE LIST"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }
        binding?.toolbar?.setNavigationOnClickListener {
            finish()
        }

        //binding?.vm?.fetchMyMenteeList()
        binding?.swipeRefreshLayout?.setOnRefreshListener {
            binding?.vm?.fetchMyMenteeList(true)
        }
        val notificationManager =
            getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.cancel(105)

    }

    override fun onResume() {
        super.onResume()
        binding?.vm?.fetchMyMenteeList(true)
        disposable = object : CountDownTimer(360000, 5000) {
            override fun onTick(millisUntilFinished: Long) {
                binding?.vm?.fetchMyMenteeList()
            }

            override fun onFinish() {
                start()
            }
        }.start()
    }

    override fun onPause() {
        super.onPause()
        disposable?.cancel()
        binding?.vm?.onPause()
    }

    fun showToast(msg: String?) {
        msg?.let { toast(it).show() }
    }

}


