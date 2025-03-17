package com.tsic.ui.screen.mentee_drawer_menu.learning

/**
 * @author Kaiser Perwez
 */

import android.content.res.Configuration
import android.os.Bundle
import android.view.inputmethod.EditorInfo
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityMenteeLearningBinding
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast

class MenteeLearningActivity : AppCompatActivity() {

    //declarations
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityMenteeLearningBinding>(
            this,
            R.layout.activity_mentee_learning
        )
    }

    //methods
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()
    }

    private fun initUiAndListeners() {
        binding.vm = MenteeLearningViewModel(this)
        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            title = "Resource"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }
        setStatusBarColor(R.color.colorStatusTranslucentGreen)

        binding?.apply {
            when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
                Configuration.UI_MODE_NIGHT_NO -> {
                    contentLayout?.imageView?.setBackgroundResource(R.drawable.bg_all_curved_toolbar)
                } // Night mode is not active, we're using the light theme
                Configuration.UI_MODE_NIGHT_YES -> {
                    contentLayout?.imageView?.setBackgroundResource(R.drawable.bg2)
                } // Night mode is active, we're using dark theme
            }
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
                it.setProgressViewOffset(false, 100, 200)
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
