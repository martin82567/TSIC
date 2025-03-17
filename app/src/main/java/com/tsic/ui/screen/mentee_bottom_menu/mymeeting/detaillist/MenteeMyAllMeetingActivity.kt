package com.tsic.ui.screen.mentee_bottom_menu.mymeeting.detaillist

import android.content.res.Configuration
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityMenteeMyAllMeetingBinding
import com.tsic.ui.base.BaseTabViewPagerAdapter
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.past.MenteePastMeetingListFrag
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.requested.MenteeRequestedMeetingListFrag
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.upcoming.MenteeUpcomingMeetingListFrag
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast

class MenteeMyAllMeetingActivity : AppCompatActivity() {
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityMenteeMyAllMeetingBinding>(
            this,
            R.layout.activity_mentee_my_all_meeting
        )
    }
    val upComingMeetingFrag = MenteeUpcomingMeetingListFrag()
    val requestedMeetingFrag by lazy { MenteeRequestedMeetingListFrag(this) }
    val pastMeetingFrag = MenteePastMeetingListFrag()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
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
//            setDisplayShowHomeEnabled(true)
        }
        val adapter = BaseTabViewPagerAdapter(supportFragmentManager).apply {
            when (intent.getIntExtra("page", 1)) {
                1 -> addFragment(requestedMeetingFrag, "Scheduling Mentor Sessions")
                2 -> addFragment(upComingMeetingFrag, "Confirmed Mentor Sessions")
                else -> addFragment(pastMeetingFrag, "Completed Mentor Session")
            }


        }
        binding?.apply {
            viewPagerMeeting?.adapter = adapter
        }
    }


    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.progressBar?.visibility = if (yes) View.VISIBLE else View.INVISIBLE
    }

    fun showToast(msg: String) {
        toast(msg)
    }

}