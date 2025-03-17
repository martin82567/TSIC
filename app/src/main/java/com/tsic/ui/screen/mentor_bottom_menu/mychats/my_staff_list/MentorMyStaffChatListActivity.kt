package com.tsic.ui.screen.mentor_bottom_menu.mychats.my_staff_list

import android.app.NotificationManager
import android.content.Context
import android.content.res.Configuration
import android.os.CountDownTimer
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityMentorMyStaffChatListBinding
import com.tsic.ui.base.MentorBaseMainActivity
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast

class MentorMyStaffChatListActivity : MentorBaseMainActivity() {


    private var binding: ActivityMentorMyStaffChatListBinding? = null
    private var disposable: CountDownTimer? = null


    override fun getContentView() {
        val stub = bindingBase.appBarMain.viewstub.viewStub
        stub?.layoutResource = R.layout.activity_mentor_my_staff_chat_list
        stub?.setOnInflateListener { _, inflatedView ->
            binding = DataBindingUtil.bind(inflatedView)
            initUiAndListner()
        }
        stub?.inflate()
    }

    override fun getNavigationMenuItemId(): Int {
        return R.id.nav_bottom_mentor_my_chat
    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.swipeRefreshLayout?.isRefreshing = yes
    }


    private fun initUiAndListner() {
        when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
            Configuration.UI_MODE_NIGHT_NO -> {
                binding?.rootLayout?.setBackgroundResource(R.drawable.bg_all_white)
            } // Night mode is not active, we're using the light theme
            Configuration.UI_MODE_NIGHT_YES -> {
                binding?.rootLayout?.setBackgroundResource(R.drawable.bg3)
            } // Night mode is active, we're using dark theme
        }
        binding?.apply {
            activity = this@MentorMyStaffChatListActivity
            vm = MentorMyStaffListViewModel(this@MentorMyStaffChatListActivity)
            //vm?.fetchStaffList()
            supportActionBar?.title = "STAFF LIST"

        }
        bindingBase?.appBarMain?.toolbar?.title = "STAFF LIST"

        binding?.swipeRefreshLayout?.setOnRefreshListener {
            binding?.vm?.fetchStaffList(true)
        }
        showMentorSessionBadge()
        val notificationManager =
            getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.cancel(107)
    }


    /*override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        binding?.apply {
            vm?.fetchStaffList()
            swipeRefreshLayout.setOnRefreshListener {
                vm?.fetchStaffList()
            }
        }
    }*/

    fun showToast(msg: String?) {
        msg?.let { toast(it).show() }
    }

    override fun onResume() {
        super.onResume()
        binding?.vm?.fetchStaffList(true)
        disposable = object : CountDownTimer(360000, 5000) {
            override fun onTick(millisUntilFinished: Long) {
                binding?.vm?.fetchStaffList()
            }

            override fun onFinish() {
                start()
            }
        }.start()
    }

    override fun onPause() {
        super.onPause()
        disposable?.cancel()
        binding?.vm?.onPause()
    }

    /* binding = DataBindingUtil.bind(
            inflater,
            R.layout.activity_mentor_my_staff_chat_list,
            container,
            false
        )
        binding?.fragment = this
        binding?.vm = MentorMyStaffListViewModel(this)
        // Inflate the layout for this fragment
        return binding!!.root*/

}