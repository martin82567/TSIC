package com.tsic.ui.screen.mentee_bottom_menu.mychats.my_staff_list


import android.app.NotificationManager
import android.content.Context
import android.content.res.Configuration
import android.os.CountDownTimer
import androidx.databinding.DataBindingUtil
import androidx.fragment.app.Fragment
import com.tsic.R
import com.tsic.databinding.ActivityMenteeMyStaffListBinding
import com.tsic.ui.base.MenteeBaseMainActivity
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast

/**
 * A simple [Fragment] subclass.
 */
class MenteeMyStaffListActivity : MenteeBaseMainActivity() {


    private var binding: ActivityMenteeMyStaffListBinding? = null

    var rvAdapter: MenteeMyStaffListAdapter? = null
    private var disposable: CountDownTimer? = null

    override fun getContentView() {
        val stub = bindingBase.appBarMain.viewstub.viewStub
        stub?.layoutResource = R.layout.activity_mentee_my_staff_list
        stub?.setOnInflateListener { _, inflatedView ->
            binding = DataBindingUtil.bind(inflatedView)
            initUiAndListner()
        }
        stub?.inflate()
    }

    override fun getNavigationMenuItemId(): Int {
        return R.id.nav_bottom_mentee_my_chat
    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.swipeRefreshLayout?.isRefreshing = yes
    }


    private fun initUiAndListner() {
        when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
            Configuration.UI_MODE_NIGHT_NO -> {
                binding?.swipeRefreshLayout?.setBackgroundResource(R.drawable.bg_all_white)
            } // Night mode is not active, we're using the light theme
            Configuration.UI_MODE_NIGHT_YES -> {
                binding?.swipeRefreshLayout?.setBackgroundResource(R.drawable.bg3)
            } // Night mode is active, we're using dark theme
        }
        binding?.apply {
            activity = this@MenteeMyStaffListActivity
            vm = MenteeMyStaffListViewModel(this@MenteeMyStaffListActivity)
            vm?.fetchMyStaffList()
            supportActionBar?.title = "MyUploads"

        }
        bindingBase?.appBarMain?.toolbar?.setBackgroundColor(resources.getColor(R.color.colorToolbarGreen))
        bindingBase?.appBarMain?.toolbar?.title = "STAFF LIST"
        //binding?.vm?.fetchMyStaffList()
        binding?.swipeRefreshLayout?.setOnRefreshListener {
            binding?.vm?.fetchMyStaffList(true)
        }
        initRecyclerView()
        showMentorSessionBadge()

    }

    fun initRecyclerView() {
        rvAdapter =
            MenteeMyStaffListAdapter(binding?.vm?.listStaff!!, this@MenteeMyStaffListActivity)
        binding?.rvListStuff?.adapter = rvAdapter
    }

    fun notifyDataSetChanged() {
        rvAdapter?.notifyDataSetChanged()
    }

    fun showToast(msg: String?) {
        msg?.let { toast(it).show() }
    }

    override fun onPause() {
        super.onPause()
        disposable?.cancel()
        binding?.vm?.onPause()
    }

    override fun onResume() {
        super.onResume()
        val notificationManager =
            getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.cancel(107)
        binding?.vm?.fetchMyStaffList(true)
        disposable = object : CountDownTimer(360000, 5000) {
            override fun onTick(millisUntilFinished: Long) {
                binding?.vm?.fetchMyStaffList()
            }

            override fun onFinish() {
                start()
            }
        }.start()
    }
}
