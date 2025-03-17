package com.tsic.ui.screen.mentee_drawer_menu.goal.completed_details

/**
 * @author Kaiser Perwez
 */

//import com.github.tcking.giraffecompressor.GiraffeCompressor
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.downloader.PRDownloader
import com.downloader.PRDownloaderConfig
import com.tsic.R
import com.tsic.databinding.ActivityMenteeCompletedGoalDetailsBinding
import com.tsic.util.INTENT_KEY_GOAL_ID
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.toast

class MenteeCompletedGoalDetailsActivity : AppCompatActivity() {

    //declarations
    private val FILE_REQUEST_CODE: Int = 100
    private val REQUEST_SAVE_NOTES = 101
    internal val binding by lazy {
        DataBindingUtil.setContentView<ActivityMenteeCompletedGoalDetailsBinding>(
            this,
            R.layout.activity_mentee_completed_goal_details
        )
    }
    val goalId by lazy { intent.getStringExtra(INTENT_KEY_GOAL_ID) ?: "" }

    //methods
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()
    }

    private fun initUiAndListeners() {
        // GiraffeCompressor.init(this)
        // Enabling database for resume support even after the application is killed:
        val config = PRDownloaderConfig.newBuilder()
            .setDatabaseEnabled(true)
            .build()
        PRDownloader.initialize(applicationContext, config)


        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            title = "Goal Details"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }
        setStatusBarColor(R.color.colorStatusTranslucentGreen)
        binding?.apply {
            activity = this@MenteeCompletedGoalDetailsActivity
            vm = MenteeCompletedGoalDetailsViewModel(this@MenteeCompletedGoalDetailsActivity)
            vm?.getGoalDetails(goalId)
            contentLayout?.swipeRefreshLayout?.let {
                it.isRefreshing = true
                it.setProgressViewOffset(false, 100, 200)
                it.setOnRefreshListener {
                    vm?.getGoalDetails(goalId)
                }
            }
        }
    }

    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
    }

    fun showToast(msg: String?) {
        msg?.let { toast(it).show() }
    }

    override fun onPause() {
        super.onPause()
        binding.vm?.onPause()
    }

    override fun onStop() {
        super.onStop()
        binding.vm?.onStop()
    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.contentLayout?.apply {
            swipeRefreshLayout?.isRefreshing = yes
            materialCardView?.visibility = if (yes) View.INVISIBLE else View.VISIBLE
        }
    }
}
