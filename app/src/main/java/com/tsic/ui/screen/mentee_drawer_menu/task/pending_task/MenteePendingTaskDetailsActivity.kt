package com.tsic.ui.screen.mentee_drawer_menu.task.pending_task

/**
 * @author Kaiser Perwez
 */

//import com.github.tcking.giraffecompressor.GiraffeCompressor
import android.Manifest
import android.content.Intent
import android.content.pm.PackageManager
import android.content.res.Configuration
import android.os.Build
import android.os.Bundle
import android.view.View
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.databinding.DataBindingUtil
import com.downloader.PRDownloader
import com.downloader.PRDownloaderConfig
import com.esafirm.imagepicker.features.ImagePickerConfig
import com.esafirm.imagepicker.features.ImagePickerMode
import com.esafirm.imagepicker.features.registerImagePicker
import com.tsic.R
import com.tsic.databinding.ActivityMenteePendingTaskDetailsBinding
import com.tsic.ui.screen.mentee_drawer_menu.task.pending_task.add_note.MenteePendingtaskAddNoteActivity
import com.tsic.util.INTENT_KEY_TASK_ID
import com.tsic.util.extension.setStatusBarColor
import com.tsic.util.getFilePathFromUri
import gun0912.tedimagepicker.builder.TedImagePicker
import org.jetbrains.anko.configuration
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

    private val cameraPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestMultiplePermissions()
    ) { _ ->
        selectImages()
    }

    private val startActivityForResult = registerForActivityResult(
        ActivityResultContracts.StartActivityForResult()
    ) { result -> if (result.resultCode == RESULT_OK) {
        binding?.vm?.getTaskDetails(taskId)
    }
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
        val permissionsToRequest = mutableListOf<String>()

        if (ContextCompat.checkSelfPermission(this, Manifest.permission.CAMERA) != PackageManager.PERMISSION_GRANTED) {
            permissionsToRequest.add(Manifest.permission.CAMERA)
//            permissionsToRequest.add(Manifest.permission.READ_EXTERNAL_STORAGE)
//            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
//                permissionsToRequest.add(Manifest.permission.READ_MEDIA_IMAGES)
//            }
        }

        // Request permissions if not granted
        if (permissionsToRequest.isNotEmpty()) {
            cameraPermissionLauncher.launch(permissionsToRequest.toTypedArray())
            return
        }
        TedImagePicker.with(this)
            .image()
            .max(5, "You can only select five image")
            .dropDownAlbum()
            .startMultiImage { uriList ->
                val filePathList = uriList.map { getFilePathFromUri(it, this) }

                if (filePathList.isNotEmpty() && filePathList.any { it.isNotEmpty() }) {
                    binding?.vm?.apply {
                        uploadFile(filePathList)
                    }

                } else {
                    toast("Something went wrong")
                }
            }
    }


    fun gotoAddNoteScreen(view: View) {
        val intent = Intent(this, MenteePendingtaskAddNoteActivity::class.java).apply {
            putExtra(INTENT_KEY_TASK_ID, binding?.vm?.details?.get()?.id?.toString())
        }

        startActivityForResult.launch(intent)
    }
}
