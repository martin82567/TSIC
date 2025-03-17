package com.tsic.ui.screen.mentor_drawer_menu.meetings.view_session_log

/**
 * @author Kaiser Perwez
 */

import android.content.res.Configuration
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityViewSessionLogBinding
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast


class ViewSessionLogActivity : AppCompatActivity() {


    //declarations
    val binding by lazy {
        DataBindingUtil.setContentView<ActivityViewSessionLogBinding>(
            this,
            R.layout.activity_view_session_log
        )
    }
    var adapter: ViewSessionLogListAdapter? = null

    //methods
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()
        initAdapter()
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
        binding.vm = ViewSessionLogViewModel(this)
        binding?.vm?.fetchViewSessionLogList()
        setStatusBarColor(R.color.colorStatusTranslucentGreen)

        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            title = "View Session Log"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }

    }

    private fun initAdapter() {
        binding?.apply {
            adapter = vm?.tempList?.let {
                ViewSessionLogListAdapter(
                    it,
                    this@ViewSessionLogActivity,
                )
            }
        }
        binding?.rvSession?.apply {
            adapter = this@ViewSessionLogActivity.adapter
//            setHasFixedSize(true)
            setItemViewCacheSize(100)
        }

    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.progressBar?.visibility = if (yes) View.VISIBLE else View.INVISIBLE

    }

    fun showToast(msg: String) {
        toast(msg)
    }

    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
    }

    override fun onPause() {
        super.onPause()
        binding?.vm?.onPause()
    }

    override fun onStop() {
        super.onStop()
        binding?.vm?.onStop()
    }


}
