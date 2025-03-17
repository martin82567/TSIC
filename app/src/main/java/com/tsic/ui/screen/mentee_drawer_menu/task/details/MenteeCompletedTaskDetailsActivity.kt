package com.tsic.ui.screen.mentee_drawer_menu.task.details

/**
 * @author Kaiser Perwez
 */

//import com.github.tcking.giraffecompressor.GiraffeCompressor
import android.content.res.Configuration
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.downloader.PRDownloader
import com.downloader.PRDownloaderConfig
import com.tsic.R
import com.tsic.databinding.ActivityMenteeCompletedTaskDetailsBinding
import com.tsic.util.INTENT_KEY_TASK_ID
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast

class MenteeCompletedTaskDetailsActivity : AppCompatActivity() {

    //declarations
    private val FILE_REQUEST_CODE: Int = 100
    private val REQUEST_SAVE_NOTES = 101
    internal val binding by lazy {
        DataBindingUtil.setContentView<ActivityMenteeCompletedTaskDetailsBinding>(
            this,
            R.layout.activity_mentee_completed_task_details
        )
    }
    val taskId by lazy { intent.getStringExtra(INTENT_KEY_TASK_ID) ?: "" }

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
        // GiraffeCompressor.init(this)
        // Enabling database for resume support even after the application is killed:
        val config = PRDownloaderConfig.newBuilder()
            .setDatabaseEnabled(true)
            .build()
        PRDownloader.initialize(applicationContext, config)


        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            title = "Task Details"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }
        setStatusBarColor(R.color.colorStatusTranslucentGreen)
        binding?.apply {
            activity = this@MenteeCompletedTaskDetailsActivity
            vm = MenteeCompletedTasksDetailsViewModel(this@MenteeCompletedTaskDetailsActivity)
            vm?.getTaskDetails(taskId)
            contentLayout.swipeRefreshLayout.let {
                it.isRefreshing = true
                it.setProgressViewOffset(false, 100, 200)
                it.setOnRefreshListener {
                    vm?.getTaskDetails(taskId)
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
            swipeRefreshLayout.isRefreshing = yes
            rootContentLayout.visibility = if (yes) View.INVISIBLE else View.VISIBLE
        }
    }
}
