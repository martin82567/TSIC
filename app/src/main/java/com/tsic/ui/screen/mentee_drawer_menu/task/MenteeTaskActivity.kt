package com.tsic.ui.screen.mentee_drawer_menu.task

/**
 * @author Kaiser Perwez
 */
 
import android.app.NotificationManager
import android.content.Context
import android.content.res.Configuration
import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityMenteeTaskBinding
import com.tsic.ui.base.BaseTabViewPagerAdapter
import com.tsic.ui.screen.mentee_drawer_menu.task.completed.MenteeCompletedTasksFrag
import com.tsic.ui.screen.mentee_drawer_menu.task.pending.MenteePendingTasksFrag
import com.tsic.util.extension.setStatusBarColor
import kotlinx.android.synthetic.main.content_mentee_task.*
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast

class MenteeTaskActivity : AppCompatActivity() {

    //declarations
    val pendingTaskFrag = MenteePendingTasksFrag()
    val completedTaskFrag = MenteeCompletedTasksFrag()

    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityMenteeTaskBinding>(
            this,
            R.layout.activity_mentee_task
        )
    }

	//methods
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()
    }

	private fun initUiAndListeners() {
        when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
            Configuration.UI_MODE_NIGHT_NO -> {
                binding?.rootLayout?.setBackgroundResource(R.drawable.bg_all_white)
            } // Night mode is not active, we're using the light theme
            Configuration.UI_MODE_NIGHT_YES -> {
                binding?.rootLayout?.setBackgroundResource(R.drawable.bg3)
            } // Night mode is active, we're using dark theme
        }
        binding.activity = this
        setSupportActionBar(binding?.toolbar)
        supportActionBar?.apply {
            title = "Assignment"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }
        setStatusBarColor(R.color.colorStatusTranslucentGreen)
        setUpViewPager()
        val notificationManager =
            getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.cancel(104)
    }

    private fun setUpViewPager() {
        val adapter = BaseTabViewPagerAdapter(supportFragmentManager).apply {
            addFragment(pendingTaskFrag, "Pending")
            addFragment(completedTaskFrag, "Completed")
        }
        binding?.contentLayout?.apply {
            viewPager_task.adapter = adapter
            tabs_task.setupWithViewPager(viewPager_task)
        }
    }

    fun showToast(msg: String?) {
		msg?.let { toast(it).show() }
	}

    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
	}
}
