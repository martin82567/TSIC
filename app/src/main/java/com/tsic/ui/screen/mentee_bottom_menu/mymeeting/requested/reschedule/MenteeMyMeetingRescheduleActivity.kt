package com.tsic.ui.screen.mentee_bottom_menu.mymeeting.requested.reschedule

/**
 * @author Kaiser Perwez
 */

import android.content.res.Configuration
import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityMenteeMyMeetingRescheduleBinding
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast

class MenteeMyMeetingRescheduleActivity: AppCompatActivity(){

    //declarations
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityMenteeMyMeetingRescheduleBinding>(
            this,
            R.layout.activity_mentee_my_meeting_reschedule
        )
    }


    //methods
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
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
        binding.vm = MenteeMyMeetingRescheduleViewModel(this)

        setStatusBarColor(R.color.colorStatusTranslucentGreen)

        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            title = "RESCHEDULE A SESSION"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }
        val id=intent?.getStringExtra("id")?:""
        binding.contentLayout.btnSubmit.setOnClickListener {
            binding.contentLayout.viewModel?.rescheduleSession(id)
        }

    }

    fun isBusyLoadingData(yes: Boolean) {
//        binding?.progressBar?.visibility = if (yes) View.VISIBLE else View.INVISIBLE

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
