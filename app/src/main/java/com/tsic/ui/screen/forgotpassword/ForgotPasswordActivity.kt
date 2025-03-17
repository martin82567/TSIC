package com.tsic.ui.screen.forgotpassword

/**
 * @author Kaiser Perwez
 */

import android.content.res.Configuration
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityForgotPasswordBinding
import com.tsic.util.INTENT_KEY_EMAIL
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast


class ForgotPasswordActivity : AppCompatActivity() {


    //declarations
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityForgotPasswordBinding>(
            this,
            R.layout.activity_forgot_password
        )
    }


    //methods
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()
        Toast.makeText(this@ForgotPasswordActivity, "A One-Time-Password (OTP) password has been sent to your email address on file. You will need the OTP to establish your new password on the next screen. The OTP will expire in 30 minutes.\"", Toast.LENGTH_LONG).show()
        //activity.showToast("A One-Time-Password (OTP) password has been sent to your email address on file. You will need the OTP to establish your new password on the next screen. The OTP will expire in 30 minutes.")
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
        binding.vm = ForgotPasswordViewModel(this)
        setStatusBarColor(R.color.colorStatusTranslucentGreen)

        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            title = "Forgot Password"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }


        binding?.contentLayout?.apply {
            binding?.vm?.email?.set(intent.getStringExtra(INTENT_KEY_EMAIL) ?: "")
            btnSubmit?.setOnClickListener {
                binding?.vm?.resetPassword()
            }
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
