package com.tsic.ui.screen.mentee_drawer_menu.goal.pending_details

/**
 * @author Kaiser Perwez
 */

//import com.github.tcking.giraffecompressor.GiraffeCompressor
import android.Manifest
import android.content.Intent
import android.content.pm.PackageManager
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
import com.tsic.databinding.ActivityMenteePendingGoalDetailsBinding
import com.tsic.ui.screen.mentee_drawer_menu.goal.pending_details.add_note.MenteePendingGoalsAddNoteActivity
import com.tsic.util.INTENT_KEY_GOAL_ID
import com.tsic.util.extension.setStatusBarColor
import com.tsic.util.getFilePathFromUri
import gun0912.tedimagepicker.builder.TedImagePicker
import org.jetbrains.anko.toast

class MenteePendingGoalDetailsActivity : AppCompatActivity() {

    //declarations
    internal val binding by lazy {
        DataBindingUtil.setContentView<ActivityMenteePendingGoalDetailsBinding>(
            this,
            R.layout.activity_mentee_pending_goal_details
        )
    }
    val goalId by lazy { intent.getStringExtra(INTENT_KEY_GOAL_ID) ?: "" }

    private val cameraPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestMultiplePermissions()
    ) { _ ->
        selectImages()
    }

    private val startActivityForResult = registerForActivityResult(
        ActivityResultContracts.StartActivityForResult()
    ) { result -> if (result.resultCode == RESULT_OK) {
        binding?.vm?.getGoalDetails(goalId)
    }
    }

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
        val intent = Intent(this, MenteePendingGoalsAddNoteActivity::class.java).apply {
            putExtra(INTENT_KEY_GOAL_ID, binding?.vm?.details?.get()?.id?.toString())
        }

        startActivityForResult.launch(intent)

    }
}
