package com.tsic.ui.base

import android.app.Activity
import android.app.Dialog
import android.content.ActivityNotFoundException
import android.content.Intent
import android.content.res.Configuration
import android.net.Uri
import android.os.Bundle
import android.provider.Settings
import android.view.*
import androidx.appcompat.app.ActionBarDrawerToggle
import androidx.appcompat.app.AppCompatActivity
import androidx.appcompat.app.AppCompatDelegate
import androidx.databinding.DataBindingUtil
import androidx.drawerlayout.widget.DrawerLayout
import com.google.android.material.bottomnavigation.BottomNavigationView
import com.google.android.material.navigation.NavigationView
import com.tsic.BuildConfig
import com.tsic.R
import com.tsic.SplashActivity
import com.tsic.data.local.prefs.*
import com.tsic.data.model.Status
import com.tsic.data.remote.api.MENTOR_MENTEE_TOOLKIT_URL
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.databinding.ActivityMenteeBaseMainBinding
import com.tsic.databinding.DarkModeBinding
import com.tsic.databinding.NavHeaderMainBinding
import com.tsic.ui.screen.changepassword.ChangePasswordActivity
import com.tsic.ui.screen.mentee_bottom_menu.mychats.my_staff_list.MenteeMyStaffListActivity
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.MenteeMyMeetingActivity
import com.tsic.ui.screen.mentee_bottom_menu.myprofile.MenteeMyProfileActivity
import com.tsic.ui.screen.mentee_bottom_menu.myuploads.MenteeMyUploadsActivity
import com.tsic.ui.screen.mentee_drawer_menu.goal.MenteeGoalActivity
import com.tsic.ui.screen.mentee_drawer_menu.learning.MenteeLearningActivity
import com.tsic.ui.screen.mentee_drawer_menu.task.MenteeTaskActivity
import com.tsic.ui.screen.message_center.MessageCenterActivity
import com.tsic.ui.screen.pdf_viewer.PdfViewerActivity
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.setStatusBarColor
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers
import org.jetbrains.anko.*

abstract class MenteeBaseMainActivity : AppCompatActivity(),
    NavigationView.OnNavigationItemSelectedListener,
    BottomNavigationView.OnNavigationItemSelectedListener {

    private val apiService by lazy {
        MenteeApiService.create()
    }

    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(this, USER_PREF)
    }

    private var disposable: Disposable? = null

    val bindingBase by lazy {
        DataBindingUtil.setContentView<ActivityMenteeBaseMainBinding>(
            this,
            R.layout.activity_mentee_base_main
        )
    }
    var MENTEE_HELP_URL = ""
    val headerBinding by lazy {
        val headerView = bindingBase.navView.getHeaderView(0)
        NavHeaderMainBinding.bind(headerView)
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        getContentView()
        /*bindingBase.appBarMain.toolbarCustom.toolbarIcon.apply {
            setImageResource(R.drawable.ic_menu)
            setOnClickListener {
                bindingBase.drawerLayout.openDrawer(GravityCompat.START)
            }
        }*/
        bindingBase?.appBarMain?.toolbar?.let {
            supportActionBar?.title = "My Profile"
            setSupportActionBar(it)
            ActionBarDrawerToggle(
                this, bindingBase.drawerLayout, it,
                R.string.navigation_drawer_open, R.string.navigation_drawer_close
            ).apply {
                bindingBase.drawerLayout.addDrawerListener(this)
                syncState()
            }
        }
        bindingBase?.navView?.setNavigationItemSelectedListener(this)
        val versionText = bindingBase?.navView?.menu?.findItem(R.id.version)
        versionText?.title = getString(R.string.version_code, BuildConfig.VERSION_NAME)
        bindingBase.appBarMain.bottomNavMain.setOnNavigationItemSelectedListener(this)
        setStatusBarColor(R.color.colorStatusTranslucentGreen)
        updateTimeZone()
        getFAQ()
    }

    override fun onResume() {
        super.onResume()
        initUserDataOnNavHeader()
    }

    fun setStuffChatBadge(value: Int) {
        if (value == 0) {
            resetStuffChatBadge()
            return
        }
        var badge =
            bindingBase.appBarMain.bottomNavMain.getOrCreateBadge(R.id.nav_bottom_mentee_my_chat)
        badge.isVisible = true
        badge.number = value
        userPrefs?.apply {
            PreferenceHelper.setData("badge_value", value)
        }
    }

    fun showStuffChatBadge() {
        val v = userPrefs?.getInt("badge_value", 0)!!
        if (v == 0) {
            resetStuffChatBadge()
            return
        }
        var badge =
            bindingBase.appBarMain.bottomNavMain.getOrCreateBadge(R.id.nav_bottom_mentee_my_chat)
        badge.isVisible = true
        badge.number = userPrefs?.getInt("badge_value", 0)!!
    }

    private fun resetStuffChatBadge() {
        var badge =
            bindingBase.appBarMain.bottomNavMain.getOrCreateBadge(R.id.nav_bottom_mentee_my_chat)
        badge.isVisible = false
        userPrefs?.apply {
            PreferenceHelper.setData("badge_value", 0)
        }
    }

    fun setMentorSessionBadge(value: Int) {
        if (value == 0) {
            resetMentorSessionBadge()
            return
        }
        var badge =
            bindingBase.appBarMain.bottomNavMain.getOrCreateBadge(R.id.nav_bottom_mentee_my_meeting)
        badge.isVisible = true
        badge.number = value
        userPrefs?.apply {
            PreferenceHelper.setData("mentor_session_badge_value", value)
        }
    }

    fun showMentorSessionBadge() {
        val v = userPrefs?.getInt("mentor_session_badge_value", 0)!!
        if (v == 0) {
            resetMentorSessionBadge()
            return
        }
        var badge =
            bindingBase.appBarMain.bottomNavMain.getOrCreateBadge(R.id.nav_bottom_mentee_my_meeting)
        badge.isVisible = true
        badge.number = userPrefs?.getInt("mentor_session_badge_value", 0)!!
    }


    private fun resetMentorSessionBadge() {
        var badge =
            bindingBase.appBarMain.bottomNavMain.getOrCreateBadge(R.id.nav_bottom_mentee_my_meeting)
        badge.isVisible = false
        userPrefs?.apply {
            PreferenceHelper.setData("mentor_session_badge_value", 0)
        }
    }

    fun initUserDataOnNavHeader() {

        val username = "${userPrefs?.getString(KEY_FIRST_NAME, "")} ${
            userPrefs?.getString(
                KEY_MIDDLE_NAME,
                ""
            )
        } ${
            userPrefs?.getString(
                KEY_LAST_NAME, ""
            )
        } "
        val picUrl = "${userPrefs?.getString(KEY_PROFILE_PIC, "")}"
        val email = "${userPrefs?.getString(KEY_EMAIL, "")}"


        headerBinding.let {
            it.pic = picUrl
            it.name = username
            it.email = email
        }
    }

    override fun onStart() {
        super.onStart()
        updateNavigationBarState()
    }

    override fun onPause() {
        super.onPause()
        overridePendingTransition(0, 0)
        disposable?.dispose()
        disposableTimezone?.dispose()
    }

    fun setIntentDataAndStartActivity(intent: Intent, idNavBottomScreen: Int) {
        startActivity(intent)
        selectBottomNavigationBarItem(idNavBottomScreen)
    }

    //----------------bottom nav---------------//
    private fun updateNavigationBarState() =
        selectBottomNavigationBarItem(getNavigationMenuItemId())

    private fun selectBottomNavigationBarItem(itemId: Int) {
        val item = bindingBase.appBarMain.bottomNavMain.menu.findItem(itemId)
        item?.isChecked = true
    }

    fun selectStatusColor(colorId: Int) {
        setStatusBarColor(colorId)
    }

    private fun changeScreen(item: MenuItem) {
        when (item.itemId) {
            R.id.nav_bottom_mentee_my_profile -> startActivity<MenteeMyProfileActivity>()
            R.id.nav_bottom_mentee_my_chat -> {
                startActivity<MenteeMyStaffListActivity>()
            }
            R.id.nav_bottom_mentee_my_meeting -> {
                startActivity<MenteeMyMeetingActivity>()
            }
            R.id.nav_bottom_mentee_my_upload -> {
                startActivity<MenteeMyUploadsActivity>()
            }
            R.id.nav_mentee_msg_center -> startActivity<MessageCenterActivity>()
            R.id.nav_mentee_goal -> startActivity<MenteeGoalActivity>()
            R.id.nav_mentee_task -> startActivity<MenteeTaskActivity>()
            R.id.nav_mentee_resource -> startActivity<MenteeLearningActivity>()
            R.id.nav_mentee_change_password -> startActivity<ChangePasswordActivity>()
            R.id.nav_mentee_toolkit ->
                startActivity<PdfViewerActivity>(
                    "url" to MENTOR_MENTEE_TOOLKIT_URL,
                    "title" to "Mentor Toolkit"
                )
            R.id.nav_mentor_notification->{
                val settingsIntent: Intent = Intent(Settings.ACTION_APP_NOTIFICATION_SETTINGS)
                    .addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                    .putExtra(Settings.EXTRA_APP_PACKAGE, packageName)
                startActivity(settingsIntent)
            }
            R.id.nav_mentee_help -> startActivity<PdfViewerActivity>(
                "url" to MENTEE_HELP_URL,
                "title" to "FAQ"
            )//openBrowser(MENTEE_HELP_URL)
            else -> bindingBase.drawerLayout.consume { logout() }
        }
        //finish()
    }

    private fun changeMode() {
        val dialogBinding: DarkModeBinding =
            DataBindingUtil.inflate(
                LayoutInflater.from(this),
                R.layout.dark_mode,
                null,
                false
            )
        when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
            Configuration.UI_MODE_NIGHT_NO -> {
                dialogBinding?.lightMode?.isChecked = true
            }
            Configuration.UI_MODE_NIGHT_YES -> {
                dialogBinding?.darkMode?.isChecked = true
            }
        }
        val dialog = Dialog(this@MenteeBaseMainActivity)
        dialog.setContentView(dialogBinding.root)
        val window = dialog.window
        window?.setLayout(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT)
        window?.setBackgroundDrawableResource(android.R.color.transparent)
        window?.setGravity(Gravity.CENTER)
        dialog.setCancelable(false)
        dialogBinding.btnCancel.setOnClickListener {
            dismissKeyboard()
            dialog.dismiss()
        }
        dialogBinding.btnOk.setOnClickListener {
            when (dialogBinding?.radioGroup2?.checkedRadioButtonId) {
                R.id.light_mode -> {
                    userPrefs?.apply {
                        PreferenceHelper.setData(KEY_DARK_MODE, "0")
                    }
                    //darkMode.set("0")
                    AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_NO)
                }
                R.id.dark_mode -> {
                    userPrefs?.apply {
                        PreferenceHelper.setData(KEY_DARK_MODE, "1")
                    }
                    // darkMode.set("1")
                    AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_YES)
                    //setStatusBarColor(if(darkMode.get()=="1") R.color.colorDarkStatusTranslucentGreen else R.color.colorStatusTranslucentGreen)
                }
            }
            dismissKeyboard()
            dialog.dismiss()
        }
        dialog.show()
    }


    override fun onNavigationItemSelected(item: MenuItem) = when (item.itemId) {
        R.id.nav_bottom_mentee_my_profile,
        R.id.nav_bottom_mentee_my_chat,
        R.id.nav_bottom_mentee_my_upload,
        R.id.nav_bottom_mentee_my_meeting -> bindingBase.appBarMain.bottomNavMain.consume {
            changeScreen(
                item
            )
        }
        // R.id.nav_bottom_mentee_my_mentor

        R.id.nav_mentee_goal,
        R.id.nav_mentee_task,
        R.id.nav_mentee_resource,
        R.id.nav_mentee_msg_center,
        R.id.nav_mentee_change_password,
        R.id.nav_mentee_toolkit,
        R.id.nav_mentor_notification,
        R.id.nav_mentee_help -> bindingBase.drawerLayout.consume {
            changeScreen(item)
        }
        R.id.nav_mentee_dark_mode -> bindingBase.drawerLayout.consume { changeMode() }
        R.id.nav_mentee_logout -> bindingBase.drawerLayout.consume { logout() }
        R.id.version -> false
        else -> bindingBase.drawerLayout.consume { logout() }
    }

    private inline fun BottomNavigationView.consume(f: () -> Unit): Boolean {//for bottom nav
        f()
        return true
    }

    private inline fun DrawerLayout.consume(f: () -> Unit): Boolean {//a func is passed which returns nothing
        f()
        closeDrawers()
        return false
    }

    internal abstract fun getContentView()

    internal abstract fun getNavigationMenuItemId(): Int

    fun Activity?.logout() {

        if (!isDeviceOnline()) {
            toast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        this@logout?.alert("Sure to Logout?") {
            yesButton {
                BaseApplication.upComingMeetingId = null
                logoutCall() }
            negativeButton("Stay") {}
        }?.show()
    }


    fun watchYoutubeVideo(id: String) {
        val appIntent = Intent(Intent.ACTION_VIEW, Uri.parse("vnd.youtube:$id"))
        val webIntent = Intent(
            Intent.ACTION_VIEW,
            Uri.parse("http://www.youtube.com/watch?v=$id")
        )
        try {
            startActivity(appIntent)
        } catch (ex: ActivityNotFoundException) {
            startActivity(webIntent)
        }
    }

    private fun logoutCall() {
        val dialog = indeterminateProgressDialog("Logging out..").apply {
            setCancelable(false)
        }

        disposable = apiService.logoutUser(userPrefs?.getString(KEY_AUTH_TOKEN, ""))
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doAfterTerminate {
                dialog.dismiss()
            }
            .subscribe(
                { result ->
                    if (result?.status == true) {
                        toast(result.message.toString())
                        PreferenceHelper.clearSharedPreferences(
                            PreferenceHelper.customPrefs(
                                this@MenteeBaseMainActivity,
                                USER_PREF
                            )
                        )
                        startActivity<SplashActivity>()
                        finish()
                    } else
                        toast(result.message.toString())
                },
                {
                    toast("Some error occurred.")
                }
            )
    }

    var disposableTimezone: Disposable? = null
    fun updateTimeZone() {
        disposableTimezone = apiService.fetchTimeZone(userPrefs?.getString(KEY_AUTH_TOKEN, ""))
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doAfterTerminate {
            }
            .subscribe(
                { result ->
                    if (result?.status == true) {
                        if (userPrefs != null) {
                            PreferenceHelper.setData(
                                KEY_TIMEZONE,
                                result.data?.timezone
                            )
                            PreferenceHelper.setData(
                                KEY_TIMEZONE_OFFSET,
                                result.data?.timezoneOffset
                            )
                        }
                    }
                },
                {
                    toast("Some error occurred.")
                }
            )
    }

    private fun getFAQ() {

        if (!isDeviceOnline()) {
            toast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        disposable =
            apiService.getFAQ(userPrefs?.getString(KEY_AUTH_TOKEN, ""))
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe { }
                .doAfterTerminate { }
                .subscribe { result ->
                    if (result.status == Status.SUCCESS) {
                        MENTEE_HELP_URL = result.data.menteeFaq
                    }
                }
    }
}