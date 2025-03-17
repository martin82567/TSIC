package com.tsic.ui.screen.mentee_drawer_menu.task.pending_task

/**
 * @author Kaiser Perwez
 */

//import com.github.tcking.giraffecompressor.GiraffeCompressor
import android.content.Intent
import android.content.pm.PackageManager
import android.content.res.Configuration
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.downloader.PRDownloader
import com.downloader.PRDownloaderConfig
import com.jaiselrahman.filepicker.activity.FilePickerActivity
import com.jaiselrahman.filepicker.config.Configurations
import com.jaiselrahman.filepicker.model.MediaFile
import com.tsic.R
import com.tsic.databinding.ActivityMenteePendingTaskDetailsBinding
import com.tsic.ui.screen.mentee_drawer_menu.task.pending_task.add_note.MenteePendingtaskAddNoteActivity
import com.tsic.util.INTENT_KEY_TASK_ID
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.startActivityForResult
import org.jetbrains.anko.toast

class MenteePendingTaskDetailsActivity : AppCompatActivity() {

    //declarations
    private val FILE_REQUEST_CODE: Int = 100
    private val REQUEST_SAVE_NOTES = 101
    internal val binding by lazy {
        DataBindingUtil.setContentView<ActivityMenteePendingTaskDetailsBinding>(
            this,
            R.layout.activity_mentee_pending_task_details
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
            activity = this@MenteePendingTaskDetailsActivity
            vm = MenteePendingTaskDetailsViewModel(this@MenteePendingTaskDetailsActivity)
            vm?.getTaskDetails(taskId)
            contentLayout?.swipeRefreshLayout?.let {
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
            swipeRefreshLayout?.isRefreshing = yes
            rootContentLayout?.visibility = if (yes) View.INVISIBLE else View.VISIBLE
        }
    }

    fun initGoalSubmitButton() {
        val statusGoal = binding?.vm?.details?.get()?.datastatus ?: 0
        binding?.contentLayout?.btnComplete?.apply {
            text =
                when (statusGoal) {
                    0 -> "Start Task"
                    1 -> "Mark As Complete"
                    else -> {
                        isEnabled = false
                        isClickable = false
                        "Completed"
                    }
                }
        }
    }

    fun selectImages() {
        val intent = Intent(this, FilePickerActivity::class.java).apply {
            putExtra(
                FilePickerActivity.CONFIGS, Configurations.Builder()
                    .setCheckPermission(true)
                    .setShowImages(true)
                    .setShowAudios(false)
                    .setShowVideos(false)
                    .enableImageCapture(true)
                    .enableVideoCapture(false)
                    .setMaxSelection(5)
                    //.setSingleChoiceMode(true)
                    .setSkipZeroSizeFiles(true)
                    .build()
            )
        }
        startActivityForResult(intent, FILE_REQUEST_CODE)
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)

        if (requestCode == FILE_REQUEST_CODE) {
            if (resultCode == RESULT_OK) {
                binding.vm?.apply {
                    val mPaths =
                        data?.getParcelableArrayListExtra<MediaFile>(FilePickerActivity.MEDIA_FILES)
                    if (mPaths != null && mPaths.isNotEmpty())
                        binding?.vm?.apply {
                            uploadFile(mPaths)
                        }

                }

            }
        }
        if (requestCode == REQUEST_SAVE_NOTES) {
            if (resultCode == RESULT_OK) {
                binding?.vm?.getTaskDetails(taskId)
            }
        }
    }

    override fun onRequestPermissionsResult(
        requestCode: Int,
        permissions: Array<out String>,
        grantResults: IntArray
    ) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        if (grantResults.isNotEmpty() && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
            selectImages()
        } else {
            showToast("Please approve permissions to open ImagePicker")
        }
    }

    fun gotoAddNoteScreen(view: View) {
        startActivityForResult<MenteePendingtaskAddNoteActivity>(
            REQUEST_SAVE_NOTES,
            INTENT_KEY_TASK_ID to binding?.vm?.details?.get()?.id?.toString()
        )
    }
}
