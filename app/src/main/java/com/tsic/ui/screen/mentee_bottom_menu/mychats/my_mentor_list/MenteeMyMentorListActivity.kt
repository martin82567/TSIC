package com.tsic.ui.screen.mentee_bottom_menu.mychats.my_mentor_list


import android.app.NotificationManager
import android.content.Context
import android.content.res.Configuration
import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import androidx.fragment.app.Fragment
import com.tsic.R
import com.tsic.databinding.ActivityMenteeMyMentorListBinding
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast

/**
 * A simple [Fragment] subclass.
 */
class MenteeMyMentorListActivity : AppCompatActivity() {
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityMenteeMyMentorListBinding>(
            this,
            R.layout.activity_mentee_my_mentor_list
        )
    }

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
        binding?.vm = MenteeMyMentorListViewModel(this)
        binding?.activity = this
        setStatusBarColor(R.color.colorStatusTranslucentGreen)
        setSupportActionBar(binding?.toolbar)



        supportActionBar?.apply {
            title = "MY MENTORS"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }

        binding?.vm?.fetchMyMentorList()
        binding?.swipeRefreshLayout?.setOnRefreshListener {
            binding?.vm?.fetchMyMentorList()
        }

        /*binding?.rootLayout?.setOnClickListener {
            startActivity<ChatDetailsActivity>(
                INTENT_KEY_CHATTER_ID to binding?.vm?.id?.get(),
                INTENT_KEY_CHATTER_NAME to binding?.vm?.name?.get(),
                INTENT_KEY_CHATTER_PIC to binding?.vm?.profilePic?.get(),
                INTENT_KEY_CHATTER_TYPE to TYPE_MENTOR
            )
        }*/


    }

    fun showToast(msg: String?) {
        msg?.let { toast(it).show() }
    }

    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
    }

    override fun onPause() {
        super.onPause()
        binding?.vm?.onPause()
    }

    override fun onResume() {
        super.onResume()
        val notificationManager =
            getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.cancel(106)
        binding?.vm?.fetchMyMentorList()
    }
}
