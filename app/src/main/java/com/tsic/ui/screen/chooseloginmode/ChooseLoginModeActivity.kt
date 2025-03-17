package com.tsic.ui.screen.chooseloginmode

/**
 * @author Kaiser Perwez
 */

import android.content.SharedPreferences
import android.content.res.Configuration
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.data.local.prefs.KEY_DARK_MODE
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.databinding.ActivityChooseLoginModeBinding
import com.tsic.ui.screen.mentor_bottom_menu.mysessions.calendarView.makeInVisible
import com.tsic.ui.screen.mentor_bottom_menu.mysessions.calendarView.makeVisible
import kotlinx.android.synthetic.main.activity_choose_login_mode.*
import kotlinx.android.synthetic.main.content_choose_login_mode.view.*
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast

class ChooseLoginModeActivity : AppCompatActivity() {

    //declarations
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityChooseLoginModeBinding>(
            this,
            R.layout.activity_choose_login_mode
        )
    }
    private val userPrefs: SharedPreferences? by lazy {
        PreferenceHelper.customPrefs(this, USER_PREF)
    }

    //methods
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()
    }

    private fun initUiAndListeners() {
        when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
            Configuration.UI_MODE_NIGHT_NO -> {
                binding?.rootLayout?.setBackgroundResource(R.drawable.bg_all_white)
            } // Night mode is not active, we're using the light theme
            Configuration.UI_MODE_NIGHT_YES -> {
                binding?.rootLayout?.setBackgroundResource(R.drawable.bg3)
            } // Night mode is active, we're using dark theme
        }
        if (userPrefs?.getString(KEY_DARK_MODE, "") == "")
            userPrefs?.apply {
                PreferenceHelper.setData(KEY_DARK_MODE, "0")
            }

        binding?.apply {

            vm = CheckUserViewModel(this@ChooseLoginModeActivity)
            activity = this@ChooseLoginModeActivity
            contentLayout.btnMentor.setOnClickListener{
                contentLayout.txtCheckEmail.makeVisible()
                contentLayout.btnNext.makeVisible()
                contentLayout.btnMentor.makeInVisible()
                contentLayout.btnMentee.makeInVisible()
                contentLayout.tvOr.makeInVisible()
                contentLayout.imageViewAvatar.makeInVisible()
            }
            contentLayout.btnMentee.setOnClickListener{
                contentLayout.txtCheckEmail.makeVisible()
                contentLayout.btnNext.makeVisible()
                contentLayout.btnMentor.makeInVisible()
                contentLayout.btnMentee.makeInVisible()
                contentLayout.tvOr.makeInVisible()
                contentLayout.imageViewAvatar.makeInVisible()
            }
            contentLayout.btnNext.setOnClickListener {
                vm?.getUserDetails()
            }
        }
    }
    fun showToast(msg: String?) {
        msg?.let {
            toast(it).show()
        }
    }

    fun isBusyLoadingData(yes: Boolean) {
        buttonEnableOrDisable(!yes)
        binding?.chooseLoginModeProgressBar?.visibility = if (yes) View.VISIBLE else View.INVISIBLE
    }

    fun buttonEnableOrDisable(isEnable: Boolean) {
        binding?.contentLayout?.btnNext?.isEnabled = isEnable
    }

}
