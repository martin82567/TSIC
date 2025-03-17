package com.tsic.ui.screen.mentee_bottom_menu.myuploads

/**
 * @author Kaiser Perwez
 */

import android.app.Activity
import android.content.Intent
import android.content.res.Configuration
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityMenteeMyUploadsBinding
import com.tsic.ui.base.MenteeBaseMainActivity
import com.tsic.ui.screen.mentee_bottom_menu.myuploads.upload_report.MenteeUploadReportActivity
import org.jetbrains.anko.configuration
import org.jetbrains.anko.startActivityForResult
import org.jetbrains.anko.toast

class MenteeMyUploadsActivity : MenteeBaseMainActivity() {

    private val REQUEST_SAVE_REPORT = 1


    //declarations
    private var binding: ActivityMenteeMyUploadsBinding? = null

    override fun getContentView() {
        val stub = bindingBase.appBarMain.viewstub.viewStub
        stub?.layoutResource = R.layout.activity_mentee_my_uploads
        stub?.setOnInflateListener { _, inflatedView ->
            binding = DataBindingUtil.bind(inflatedView)
            initUiAndListeners()
        }
        stub?.inflate()
    }

    override fun getNavigationMenuItemId(): Int {
        return R.id.nav_bottom_mentee_my_upload
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
        binding?.apply {
            vm = MenteeMyUploadsViewModel(this@MenteeMyUploadsActivity)
            vm?.fetchUploadedReports()
            supportActionBar?.title = "MY UPLOADS"

        }

        bindingBase?.appBarMain?.toolbar?.setBackgroundColor(resources.getColor(R.color.colorToolbarGreen))

        bindingBase?.appBarMain?.toolbar?.title = "MY UPLOADS"


        binding?.floatingUpload?.setOnClickListener {
            startActivityForResult<MenteeUploadReportActivity>(REQUEST_SAVE_REPORT)
        }
        binding?.contentLayout?.swipeRefreshLayout?.setOnRefreshListener {
            binding?.vm?.fetchUploadedReports()
        }
        showStuffChatBadge()
        showMentorSessionBadge()
    }


    fun showToast(msg: String?) {
        msg?.let { toast(it).show() }
    }

    override fun onPause() {
        super.onPause()
        binding?.vm?.onPause()
    }

    override fun onStop() {
        super.onStop()
        binding?.vm?.onStop()
    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.contentLayout?.swipeRefreshLayout?.apply {
            setProgressViewOffset(true, 100, 200)
            isRefreshing = yes
        }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {

        if (requestCode == REQUEST_SAVE_REPORT && resultCode == Activity.RESULT_OK) {
            binding?.vm?.fetchUploadedReports()
        }
        super.onActivityResult(requestCode, resultCode, data)

    }
}
