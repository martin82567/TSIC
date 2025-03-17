package com.tsic.ui.screen.login

/**
 * @author Kaiser Perwez
 */

import android.content.Intent
import android.content.res.Configuration
import android.graphics.Color
import android.graphics.Typeface
import android.os.Bundle
import android.text.InputType
import android.text.Spannable
import android.text.SpannableString
import android.text.style.ForegroundColorSpan
import android.text.style.UnderlineSpan
import android.view.MenuItem
import android.view.View
import android.view.inputmethod.EditorInfo
import android.widget.TextView
import androidx.annotation.NonNull
import androidx.appcompat.app.ActionBar
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityLoginBinding
import com.tsic.ui.screen.chooseloginmode.ChooseLoginModeActivity
import com.tsic.util.INTENT_KEY_EMAIL
import com.tsic.util.INTENT_KEY_LOGIN_MODE
import com.tsic.util.TYPE_MENTEE
import com.tsic.util.extension.ForgetPasswordDialog
import com.tsic.util.extension.openBrowser
import kotlinx.android.synthetic.main.content_login.*
import org.jetbrains.anko.AnkoContext
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast


class LoginActivity : AppCompatActivity() {

    private var passwordShown = false
    var url = ""
    var text2: String=""
    var msgDisplay:Boolean=false
    lateinit var spannable: Spannable


    //declarations
    val binding by lazy {
        DataBindingUtil.setContentView<ActivityLoginBinding>(this, R.layout.activity_login)
    }

    //methods
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()
    }

    // this event will enable the back
    // function to the button on press
    override fun onOptionsItemSelected(@NonNull item: MenuItem): Boolean {
        when (item.getItemId()) {
            android.R.id.home -> {
                val intent = Intent(this,ChooseLoginModeActivity::class.java)
                intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                startActivity(intent)
                finish()
                return true
            }
        }
        return super.onOptionsItemSelected(item)
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
        binding.apply {
            vm = LoginViewModel(this@LoginActivity)
            activity = this@LoginActivity
            val extras = getIntent().extras
            vm?.email = extras?.getString(INTENT_KEY_EMAIL) ?: ""
            vm?.loginMode = extras?.getString(INTENT_KEY_LOGIN_MODE) ?: TYPE_MENTEE
            vm?.disclaimer()
            contentLayout.textLayoutForgotPwd.setOnClickListener {

                ForgetPasswordDialog(
                    AnkoContext.Companion.create(
                        this@LoginActivity,
                        it
                    )
                ).apply {
                    cancelButton.setOnClickListener {
                        dialog.dismiss()
                    }
                    okButton.setOnClickListener { it ->
                        it?.isEnabled = false
                        binding?.vm?.getOtpAtEmail(emailText.text.toString())
                        dialog.dismiss()
                    }
                }
            }


            contentLayout.txtEmail.setOnEditorActionListener { v, actionId, event ->
                if(actionId == EditorInfo.IME_ACTION_NEXT){
                    vm?.loginWaiver()
                    true
                } else {
                    false
                }
            }


            contentLayout.togglePwd.setOnClickListener {
                if (passwordShown) {
                    passwordShown = false
                    contentLayout.txtPassword.inputType = 129
                    contentLayout.txtPassword.typeface = Typeface.SANS_SERIF
                    contentLayout.togglePwd.setImageResource(R.drawable.ic_hide_pwd)

                } else {
                    passwordShown = true
                    contentLayout.txtPassword.inputType = InputType.TYPE_TEXT_VARIATION_PASSWORD
                    contentLayout.togglePwd.setImageResource(R.drawable.ic_show_pwd)
                }
            }
            contentLayout.btnLogin.setOnClickListener {
                //requestPermission()
                vm?.login(msgDisplay)
            }
            contentLayout.tvTNC.setOnClickListener {
                openBrowser(url)
            }
        }

    }

    /*fun showMessage(show: Boolean) {
        if (show) {
            binding?.contentLayout?.apply {
                cardView6.visibility = View.VISIBLE
                shimmerFrameLayout.visibility = View.VISIBLE
            }
        } else {
            binding?.contentLayout?.apply {
                cardView6.visibility = View.GONE
                shimmerFrameLayout.visibility = View.GONE
            }
        }
    }*/

    fun showMessageWaiver(show: Boolean) {
        if (show) {
            binding?.contentLayout?.apply {
                cb_t_n_c.visibility = View.VISIBLE
                msgDisplay=true
                txtWaiverTxt()


            }
        } else {
            binding?.contentLayout?.apply {
                cb_t_n_c.visibility = View.GONE
                msgDisplay=false
                txtWaiverTxt()

            }
        }
    }

    fun showToast(msg: String?) {
        msg?.let {
            toast(it).show()
        }
    }

    override fun onBackPressed() {
        super.onBackPressed()
        val intent = Intent(this,ChooseLoginModeActivity::class.java)
        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        startActivity(intent)
        finish()
    }

    fun isBusyLoadingData(yes: Boolean) {
        buttonEnableOrDisable(!yes)
        binding?.progressBar?.visibility = if (yes) View.VISIBLE else View.INVISIBLE
    }

    fun buttonEnableOrDisable(isEnable: Boolean) {
        binding?.contentLayout?.btnLogin?.isEnabled = isEnable
    }

    override fun onResume() {
        super.onResume()
        binding?.contentLayout?.systemMessage?.shimmerFrameLayout?.startShimmer();
        binding?.vm?.getSystemMessage()
    }

    private fun txtWaiverTxt() {
        if(msgDisplay) {

            binding.contentLayout.tvTNC.setText(spannable, TextView.BufferType.SPANNABLE)
            binding.contentLayout.tvTNC.visibility=View.VISIBLE
        }else{
            binding.contentLayout.tvTNC.setText("")
            binding.contentLayout.tvTNC.visibility=View.GONE
        }
    }

    fun setTextTNC(s: String) {
        text2= ("$s ${getString(R.string.click_here)}")

        spannable = SpannableString(text2)

        spannable.setSpan(
            ForegroundColorSpan(Color.rgb(0, 50, 255)),
            s.length,
            s.length + 11,
            Spannable.SPAN_EXCLUSIVE_EXCLUSIVE
        )
        spannable.setSpan(
            UnderlineSpan(),
            s.length + 1,
            s.length + 11,
            0
        )

        binding.contentLayout.tvTNC.setText(spannable, TextView.BufferType.SPANNABLE)
        binding.contentLayout.tvTNC.visibility=View.VISIBLE
        cb_t_n_c.visibility = View.VISIBLE


    }

}

