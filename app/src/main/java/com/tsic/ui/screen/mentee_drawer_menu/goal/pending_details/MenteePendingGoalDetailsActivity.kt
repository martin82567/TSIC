package com.tsic.ui.screen.mentee_drawer_menu.goal.pending_details

/**
 * @author Kaiser Perwez
 */

//import com.github.tcking.giraffecompressor.GiraffeCompressor
import android.content.Intent
import android.content.pm.PackageManager
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
import com.tsic.databinding.ActivityMenteePendingGoalDetailsBinding
import com.tsic.ui.screen.mentee_drawer_menu.goal.pending_details.add_note.MenteePendingGoalsAddNoteActivity
import com.tsic.util.INTENT_KEY_GOAL_ID
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.startActivityForResult
import org.jetbrains.anko.toast

class MenteePendingGoalDetailsActivity : AppCompatActivity() {

    //declarations
    private val FILE_REQUEST_CODE: Int = 100
    private val REQUEST_SAVE_NOTES = 101
    internal val binding by lazy {
        DataBindingUtil.setContentView<ActivityMenteePendingGoalDetailsBinding>(
            this,
            R.layout.activity_mentee_pending_goal_details
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
            activity = this@MenteePendingGoalDetailsActivity
            vm = MenteePendingGoalDetailsViewModel(this@MenteePendingGoalDetailsActivity)
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
            btnComplete?.visibility = if (yes) View.INVISIBLE else View.VISIBLE
        }
    }

    fun initGoalSubmitButton() {
        val statusGoal = binding?.vm?.details?.get()?.datastatus ?: 0
        binding?.contentLayout?.btnComplete?.apply {
            text =
                when (statusGoal) {
                    0 -> "Start Goal"
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
                binding?.vm?.getGoalDetails(goalId)
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
        startActivityForResult<MenteePendingGoalsAddNoteActivity>(
            REQUEST_SAVE_NOTES,
            INTENT_KEY_GOAL_ID to binding?.vm?.details?.get()?.id?.toString()
        )
    }
}
