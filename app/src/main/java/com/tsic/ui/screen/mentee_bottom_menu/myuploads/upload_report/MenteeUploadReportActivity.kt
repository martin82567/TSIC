package com.tsic.ui.screen.mentee_bottom_menu.myuploads.upload_report

import android.Manifest
import android.app.Activity
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
import com.esafirm.imagepicker.features.ImagePickerConfig
import com.esafirm.imagepicker.features.ImagePickerMode
import com.esafirm.imagepicker.features.registerImagePicker
import com.tsic.R
import com.tsic.databinding.ActivityMenteeUploadReportBinding
import com.tsic.util.extension.setStatusBarColor
import com.tsic.util.getFilePathFromUri
import gun0912.tedimagepicker.builder.TedImagePicker
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast

class MenteeUploadReportActivity : AppCompatActivity() {

    private val cameraPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestMultiplePermissions()
    ) { _ ->
        selectImage()
    }


    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityMenteeUploadReportBinding>(
            this,
            R.layout.activity_mentee_upload_report
        )
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_mentee_upload_report)
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
        binding.vm = MenteeUploadReportViewModel(this)
        setStatusBarColor(R.color.colorStatusTranslucentGreen)



        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            title = "Upload Report"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }

        binding?.contentLayout?.apply {
            uploadReport?.setOnClickListener {
                selectImage()
            }
            btnUpload?.setOnClickListener {
                binding?.vm?.uploadReport()
            }
        }
    }


    private fun selectImage() {
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
            .max(1, "You can only select one image")
            .dropDownAlbum()
            .start { uri ->
                val filePath = getFilePathFromUri(uri, this)
                if (filePath.isNotEmpty()) {
                    binding?.vm?.apply {
                        imageUpload.set(filePath)
                    }
                } else {
                    toast("Something went wrong")
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
            selectImage()
        } else {
            showToast("Please approve permissions to open ImagePicker")
        }
    }

    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
    }

    fun showToast(msg: String) {
        toast(msg)
    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.progressBar?.visibility = if (yes) View.VISIBLE else View.INVISIBLE
    }


}
