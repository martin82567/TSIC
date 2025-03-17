package com.tsic.ui.screen.mentor_drawer_menu.report_list

import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityReportListBinding
import com.tsic.util.INTENT_MENTEEID
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.selector
import org.jetbrains.anko.toast

class ReportListActivity : AppCompatActivity() {


    //declarations
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityReportListBinding>(
            this,
            R.layout.activity_report_list
        )
    }

    private val viewModel by lazy {
        MentorReportViewModel(this)
    }


    val menteeId by lazy { intent.getStringExtra(INTENT_MENTEEID) }


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()


    }

    /*override fun getContentView() {
        val stub = bindingBase.appBarMain.viewstub.viewStub
        stub?.layoutResource = R.layout.activity_report_list
        stub?.setOnInflateListener { _, inflatedView ->
            binding = DataBindingUtil.bind(inflatedView)
            initUiAndListeners()
        }
        stub?.inflate()
    }*/

    /* override fun getNavigationMenuItemId(): Int {
         return R.id.nav_mentor_report
     }*/

    private fun initUiAndListeners() {
        binding.vm = viewModel
        setSupportActionBar(binding.toolbar)
        binding?.apply {
            vm = MentorReportViewModel(this@ReportListActivity)
            setStatusBarColor(R.color.colorStatusTranslucentGreen)

            // vm?.fetchUploadedReports(menteeId)
            supportActionBar?.apply {
                title = "Report List"
                setDisplayHomeAsUpEnabled(true)
                setDisplayShowHomeEnabled(true)
            }
        }


        binding?.contentLayout?.swipeRefreshLayout?.setOnRefreshListener {
            binding?.vm?.fetchUploadedReports(binding?.vm?.menteeId.toString())
        }

        binding?.contentLayout?.eTSearch?.setOnClickListener {
            menteeFetchList()
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

    fun menteeFetchList() {
        val list = arrayListOf<String>()
        binding?.vm?.listMenteeNames?.forEach {
            list.add(it)
        }

        if (list.isEmpty()) {
            showToast("Please wait..Fetching your mentee list")
            binding?.vm?.fetchMenteeList()
            return
        }

        selector("Select Mentee Name", list) { dialogInterface, i ->
            binding?.vm?.apply {
                menteeName.set("${list[i]}")
                binding?.vm?.menteeId = "${listMenteeId[i]}"

                fetchUploadedReports(binding?.vm?.menteeId.toString())
            }
        }
    }

}

