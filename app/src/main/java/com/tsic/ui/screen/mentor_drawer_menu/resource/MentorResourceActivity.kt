package com.tsic.ui.screen.mentor_drawer_menu.resource

/**
 * @author Kaiser Perwez
 */

import android.os.Bundle
import android.view.inputmethod.EditorInfo
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityMentorLearningBinding
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.toast

class MentorResourceActivity : AppCompatActivity() {

    //declarations
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityMentorLearningBinding>(
            this,
            R.layout.activity_mentor_learning
        )
    }

    //methods
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()
    }

    private fun initUiAndListeners() {
        binding?.vm = MentorResourceViewModel(this)
        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            title = "Resource"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }
        setStatusBarColor(R.color.colorStatusTranslucentGreen)

        binding?.apply {
            contentLayout?.eTLearningSearch?.setOnEditorActionListener(TextView.OnEditorActionListener { v, actionId, event ->
                if (actionId == EditorInfo.IME_ACTION_SEARCH) {
                    vm?.fetchData()
                    return@OnEditorActionListener true
                }
                false
            })
            vm?.fetchData()
            contentLayout?.swipeRefreshLayout?.let {
                it.isRefreshing = true
                it.setProgressViewOffset(false, 120, 200)
                it.setOnRefreshListener { vm?.fetchData() }
            }


        }

    }


    fun isBusyLoadingData(yes: Boolean) {
        binding?.contentLayout?.swipeRefreshLayout?.isRefreshing = yes
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
}
