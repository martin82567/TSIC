package com.tsic.ui.screen.mentor_drawer_menu.meetings.alllist

import android.content.res.Configuration
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityMentorAllDetailsListBinding
import com.tsic.ui.base.BaseTabViewPagerAdapter
import com.tsic.ui.screen.mentor_drawer_menu.meetings.past.MentorPastMeetingListFrag
import com.tsic.ui.screen.mentor_drawer_menu.meetings.requested.MentorRequestedMeetingListFrag
import com.tsic.ui.screen.mentor_drawer_menu.meetings.upcoming.MentorUpcomingMeetingListFrag
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast

class MentorAllDetailsListActivity : AppCompatActivity() {
    //declarations
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityMentorAllDetailsListBinding>(
            this,
            R.layout.activity_mentor_all_details_list
        )
    }
    val upComingMeetingFrag = MentorUpcomingMeetingListFrag()
    val requestedMeetingFrag = MentorRequestedMeetingListFrag()
    val pastMeetingFrag = MentorPastMeetingListFrag()

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
        setStatusBarColor(R.color.colorStatusTranslucentGreen)

        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            title = "SESSIONS"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }


        val adapter = BaseTabViewPagerAdapter(supportFragmentManager).apply {
            when (intent.getIntExtra("page", 1)) {
                1 -> addFragment(requestedMeetingFrag, "Scheduling Mentor Sessions")
                2 -> addFragment(upComingMeetingFrag, "Confirmed Mentor Sessions")
                else -> addFragment(pastMeetingFrag, "Completed Mentor Sessions")
            }

        }
        binding?.apply {
            viewPagerMeeting?.adapter = adapter
        }

    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.progressBar?.visibility = if (yes) View.VISIBLE else View.INVISIBLE

    }

    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
    }

    fun showToast(msg: String) {
        toast(msg)
    }

}