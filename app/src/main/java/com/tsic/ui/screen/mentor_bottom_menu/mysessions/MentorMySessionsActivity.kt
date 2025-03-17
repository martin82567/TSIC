package com.tsic.ui.screen.mentor_bottom_menu.mysessions

/**
 * @author Kaiser Perwez
 */

import android.app.Activity
import android.content.Intent
import android.content.res.Configuration
import android.os.Bundle
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityMentorMySessionsBinding
import com.tsic.ui.base.MentorBaseMainActivity
import com.tsic.ui.screen.mentor_bottom_menu.mysessions.add_session.MentorAddSessionActivity
import org.jetbrains.anko.configuration
import org.jetbrains.anko.startActivityForResult
import org.jetbrains.anko.toast

class MentorMySessionsActivity : MentorBaseMainActivity() {


    private var binding: ActivityMentorMySessionsBinding? = null
    private val REQUEST_SAVE_SESSION = 1

    //declarations
    override fun getContentView() {
        val stub = bindingBase.appBarMain.viewstub.viewStub
        stub?.layoutResource = R.layout.activity_mentor_my_sessions
        stub?.setOnInflateListener { _, inflatedView ->
            binding = DataBindingUtil.bind(inflatedView)
            initUiAndListeners()
        }
        stub?.inflate()
    }

    override fun getNavigationMenuItemId(): Int {
        return R.id.nav_bottom_mentor_my_session_logs
    }


    //methods
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()
    }

    //methods

    private fun initUiAndListeners() {
        binding?.apply {
            activity = this@MentorMySessionsActivity
            vm = MentorMySessionsViewModel(this@MentorMySessionsActivity)
            vm?.fetchData()


        }
        bindingBase?.appBarMain?.toolbar?.setBackgroundColor(resources.getColor(R.color.colorToolbarGreen))
        bindingBase?.appBarMain?.toolbar?.title = "SESSION LOGS"

        when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
            Configuration.UI_MODE_NIGHT_NO -> {
                //binding?.contentLayout?.imageView?.setBackgroundResource(R.drawable.bg_all_curved_toolbar)
                binding?.contentLayout?.rootContentLayout?.setBackgroundResource(R.drawable.bg_all_white)
            } // Night mode is not active, we're using the light theme
            Configuration.UI_MODE_NIGHT_YES -> {
                //binding?.contentLayout?.imageView?.setBackgroundResource(R.drawable.bg2)
                binding?.contentLayout?.rootContentLayout?.setBackgroundResource(R.drawable.bg3)
            } // Night mode is active, we're using dark theme
        }

        binding?.floatingAdd?.setOnClickListener {
            startActivityForResult<MentorAddSessionActivity>(REQUEST_SAVE_SESSION)
        }
        showBadge()
        showMentorSessionBadge()
    }

    fun showToast(msg: String) {
        toast(msg)
    }

    override fun onResume() {
        super.onResume()
        binding?.vm?.onResume()
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
        binding?.contentLayout?.swipeRefreshLayout?.isRefreshing = yes
    }

//    fun gotoMentorAddSessionScreen(view: View) {
//        startActivity<MentorAddSessionActivity>()
//    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {

        if (requestCode == REQUEST_SAVE_SESSION) {
            if (resultCode == Activity.RESULT_OK) {
                binding?.vm?.onResume()
            }
        }
        super.onActivityResult(requestCode, resultCode, data)

    }

}
