package com.tsic.ui.screen.mentee_bottom_menu.myuploads.upload_report

import android.app.Activity
import android.content.Intent
import android.content.pm.PackageManager
import android.content.res.Configuration
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.jaiselrahman.filepicker.activity.FilePickerActivity
import com.jaiselrahman.filepicker.config.Configurations
import com.jaiselrahman.filepicker.model.MediaFile
import com.tsic.R
import com.tsic.databinding.ActivityMenteeUploadReportBinding
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast

class MenteeUploadReportActivity : AppCompatActivity() {

    private val FILE_REQUEST_CODE: Int = 101


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


    fun selectImage() {
        val intent = Intent(this, FilePickerActivity::class.java).apply {
            putExtra(
                FilePickerActivity.CONFIGS, Configurations.Builder()
                    .setCheckPermission(true)
                    .setShowImages(true)
                    .setShowAudios(false)
                    .setShowVideos(false)
                    .enableImageCapture(true)
                    .enableVideoCapture(false)
                    .setMaxSelection(1)
                    //.setSingleChoiceMode(true)
                    .setSkipZeroSizeFiles(true)
                    .build()
            )
        }
        startActivityForResult(intent, FILE_REQUEST_CODE)
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == FILE_REQUEST_CODE && resultCode == Activity.RESULT_OK) {
            val mPaths =
                data?.getParcelableArrayListExtra<MediaFile>(FilePickerActivity.MEDIA_FILES)
            if (mPaths != null && mPaths.isNotEmpty()) {
                binding?.vm?.apply {
                    imageUpload.set(mPaths.get(0)?.path)
                }

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
