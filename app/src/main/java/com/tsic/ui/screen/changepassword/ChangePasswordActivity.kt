package com.tsic.ui.screen.changepassword

/**
 * @author Kaiser Perwez
 */

import android.content.res.Configuration
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.KEY_LOGIN_MODE
import com.tsic.databinding.ActivityChangePasswordBinding
import com.tsic.util.TYPE_MENTEE
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast


class ChangePasswordActivity : AppCompatActivity() {

    //declarations
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityChangePasswordBinding>(
            this,
            R.layout.activity_change_password
        )
    }


    //methods
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
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
        binding.vm = ChangePasswordViewModel(this)
        setStatusBarColor(R.color.colorStatusTranslucentGreen)
        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            title = "Change Password"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }

        binding?.contentLayout?.submitBtn?.setOnClickListener {
            val loginMode= binding?.vm?.userPrefs?.getString(KEY_LOGIN_MODE, "")
            if (loginMode == TYPE_MENTEE)
                binding?.vm?.getchangePass()
            else
                binding?.vm?.getchangePassMentor()
        }


    }

    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
    }


    fun showToast(msg: String) {
        toast(msg)
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
        binding?.progressBar?.visibility = if (yes) View.VISIBLE else View.INVISIBLE
    }

}
