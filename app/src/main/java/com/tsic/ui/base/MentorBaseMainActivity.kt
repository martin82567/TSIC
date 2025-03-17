package com.tsic.ui.base

//import com.tsic.data.service.FetchLocationService.Companion.stopLocationService
import android.app.Activity
import android.app.AlertDialog
import android.app.Dialog
import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.content.IntentFilter
import android.os.Build
import android.os.Bundle
import android.provider.Settings
import android.util.Log
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
import com.tsic.data.local.prefs.PreferenceHelper.setData
import com.tsic.data.model.Status
import com.tsic.data.remote.api.MENTOR_MENTEE_TOOLKIT_URL
import com.tsic.data.remote.api.MentorApiService
import com.tsic.databinding.ActivityMentorBaseMainBinding
import com.tsic.databinding.DarkModeBinding
import com.tsic.databinding.NavHeaderMainBinding
import com.tsic.ui.screen.changepassword.ChangePasswordActivity
import com.tsic.ui.screen.mentor_bottom_menu.mychats.my_staff_list.MentorMyStaffChatListActivity
import com.tsic.ui.screen.mentor_bottom_menu.myprofile.MentorMyProfileActivity
import com.tsic.ui.screen.mentor_bottom_menu.mysessions.MentorMySessionsActivity
import com.tsic.ui.screen.mentor_bottom_menu.mysessions.add_session.MentorAddSessionActivity
import com.tsic.ui.screen.mentor_drawer_menu.meetings.MentorMyMeetingActivity
import com.tsic.ui.screen.mentor_drawer_menu.resource.MentorResourceActivity
import com.tsic.ui.screen.message_center.MessageCenterActivity
import com.tsic.ui.screen.pdf_viewer.PdfViewerActivity
import com.tsic.ui.screen.videocallscreen.AddSessionDialog
import com.tsic.util.BROADCAST_SHOW_LOG_SESSION_POPUP
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.setStatusBarColor
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers
import org.jetbrains.anko.*

abstract class MentorBaseMainActivity : AppCompatActivity(),
    NavigationView.OnNavigationItemSelectedListener,
    BottomNavigationView.OnNavigationItemSelectedListener {
    private var isDialogShown = false
    private val apiService by lazy {
        MentorApiService.create()
    }

    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(this, USER_PREF)
    }

    private var disposable: Disposable? = null

    val bindingBase by lazy {
        DataBindingUtil.setContentView<ActivityMentorBaseMainBinding>(
            this,
            R.layout.activity_mentor_base_main
        )
    }

    val headerBinding by lazy {
        val headerView = bindingBase.navView.getHeaderView(0)
        NavHeaderMainBinding.bind(headerView)
    }
    var MENTOR_HELP_URL = ""

    private val sessionLogBroadcastReceiver = object : BroadcastReceiver() {
        override fun onReceive(context: Context?, intent: Intent?) {
            Log.d("MyTag", "BroadcastReceived: BROADCAST_SHOW_LOG_SESSION_POPUP")
            if (!isDialogShown) {
                isDialogShown = true
                context?.let {
                    showAlertDialog(it)
                }
            }
        }
    }

    private fun showAlertDialog(context: Context) {
        AlertDialog.Builder(context)
            .setMessage(getString(R.string.log_session_message))
            .setPositiveButton(getString(R.string.yes)) { dialog, which ->
                context.startActivity(Intent(context, MentorAddSessionActivity::class.java))
                // (context as? Activity)?.finish()
            }
            .setNegativeButton(getString(R.string.no)) { dialog, which ->
                isDialogShown = false
                dialog.dismiss()


            }
            .setCancelable(false)
            .show()
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        getContentView()
        bindingBase?.appBarMain?.activity = this@MentorBaseMainActivity
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
        invalidateOptionsMenu()
    }

    private fun setSeesionLogBroadcastReceiver() {
        val filter = IntentFilter(BROADCAST_SHOW_LOG_SESSION_POPUP)
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            registerReceiver(sessionLogBroadcastReceiver, filter, RECEIVER_NOT_EXPORTED)
        } else {
            registerReceiver(sessionLogBroadcastReceiver, filter)
        }
        //  registerReceiver(sessionLogBroadcastReceiver, IntentFilter(BROADCAST_SHOW_LOG_SESSION_POPUP))
    }


    override fun onResume() {
        super.onResume()
        initUserDataOnNavHeader()
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
        setSeesionLogBroadcastReceiver()
        updateNavigationBarState()
    }

    override fun onPause() {
        super.onPause()
        disposable?.dispose()
        overridePendingTransition(0, 0)
    }

    override fun onDestroy() {
        unregisterReceiver(sessionLogBroadcastReceiver)
        super.onDestroy()
        //stopBackgroundSound()
    }

    /*override fun onStop() {
        super.onStop()
        stopBackgroundSound()

    }
*/
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
            R.id.nav_bottom_mentor_my_profile -> startActivity<MentorMyProfileActivity>()
            R.id.nav_bottom_mentor_my_meeting -> startActivity<MentorMyMeetingActivity>()
            R.id.nav_bottom_mentor_my_session_logs -> startActivity<MentorMySessionsActivity>()
            R.id.nav_bottom_mentor_my_chat -> startActivity<MentorMyStaffChatListActivity>()
            R.id.nav_mentor_dark_mode -> bindingBase.drawerLayout.consume { changeMode() }
            R.id.nav_mentor_resource -> startActivity<MentorResourceActivity>()
            R.id.nav_mentor_msg_center -> startActivity<MessageCenterActivity>()
            R.id.nav_mentor_change_password -> startActivity<ChangePasswordActivity>()
            R.id.nav_mentor_toolkit ->
                startActivity<PdfViewerActivity>(
                    "url" to MENTOR_MENTEE_TOOLKIT_URL,
                    "title" to "Mentor Toolkit"
                )//openBrowser(MENTOR_TOOLKIT_URL)
            R.id.nav_mentor_help -> startActivity<PdfViewerActivity>(
                "url" to MENTOR_HELP_URL, "title" to "FAQ"
            )//openBrowser(MENTOR_HELP_URL)
            R.id.nav_mentor_notification -> {
                val settingsIntent: Intent = Intent(Settings.ACTION_APP_NOTIFICATION_SETTINGS)
                    .addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                    .putExtra(Settings.EXTRA_APP_PACKAGE, packageName)
                startActivity(settingsIntent)
            }

            else -> bindingBase.drawerLayout.consume {
                //stopLocationService(this)
                logout()
            }
        }
        //  finish()
    }

    fun setBadge(value: Int) {
        if (value == 0) {
            resetBadge()
            return
        }
        var badge =
            bindingBase.appBarMain.bottomNavMain.getOrCreateBadge(R.id.nav_bottom_mentor_my_chat)
        badge.isVisible = true
        badge.number = value
        userPrefs?.apply {
            setData("badge_value", value)
        }
    }

    fun showBadge() {
        val v = userPrefs?.getInt("badge_value", 0)!!
        if (v == 0) {
            resetBadge()
            return
        }
        var badge =
            bindingBase.appBarMain.bottomNavMain.getOrCreateBadge(R.id.nav_bottom_mentor_my_chat)
        badge.isVisible = true
        badge.number = userPrefs?.getInt("badge_value", 0)!!
    }

    fun resetBadge() {
        var badge =
            bindingBase.appBarMain.bottomNavMain.getOrCreateBadge(R.id.nav_bottom_mentor_my_chat)
        badge.isVisible = false
        userPrefs?.apply {
            setData("badge_value", 0)
        }
    }

    fun setMentorSessionBadge(value: Int) {
        if (value == 0) {
            resetMentorSessionBadge()
            return
        }
        var badge =
            bindingBase.appBarMain.bottomNavMain.getOrCreateBadge(R.id.nav_bottom_mentor_my_meeting)
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
            bindingBase.appBarMain.bottomNavMain.getOrCreateBadge(R.id.nav_bottom_mentor_my_meeting)
        badge.isVisible = true
        badge.number = userPrefs?.getInt("mentor_session_badge_value", 0)!!
    }


    private fun resetMentorSessionBadge() {
        var badge =
            bindingBase.appBarMain.bottomNavMain.getOrCreateBadge(R.id.nav_bottom_mentor_my_meeting)
        badge.isVisible = false
        userPrefs?.apply {
            PreferenceHelper.setData("mentor_session_badge_value", 0)
        }
    }

    private fun changeMode() {
        val dialogBinding: DarkModeBinding =
            DataBindingUtil.inflate(
                LayoutInflater.from(this),
                R.layout.dark_mode,
                null,
                false
            )
        if (userPrefs?.getString(KEY_DARK_MODE, "0") == "0") {
            dialogBinding?.lightMode?.isChecked = true
        } else {
            dialogBinding?.darkMode?.isChecked = true
        }
        val dialog = Dialog(this@MentorBaseMainActivity)
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
                        setData(KEY_DARK_MODE, "0")
                    }
                    AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_NO)
                }

                R.id.dark_mode -> {
                    userPrefs?.apply {
                        setData(KEY_DARK_MODE, "1")
                    }
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
        R.id.nav_bottom_mentor_my_profile,
        R.id.nav_bottom_mentor_my_meeting,
        R.id.nav_bottom_mentor_my_session_logs,
        R.id.nav_bottom_mentor_my_chat,
        R.id.nav_mentor_notification,
            //  R.id.nav_mentor_goal,
            //  R.id.nav_mentor_meeting,
        R.id.nav_mentor_resource,
        R.id.nav_mentor_change_password,
        R.id.nav_mentor_msg_center,
        R.id.nav_mentor_change_password,
        R.id.nav_mentor_toolkit -> bindingBase.drawerLayout.consume { changeScreen(item) }

        R.id.nav_mentor_help -> bindingBase.drawerLayout.consume { changeScreen(item) }
        R.id.nav_mentor_dark_mode -> bindingBase.drawerLayout.consume { changeMode() }

        R.id.nav_mentee_logout -> bindingBase.drawerLayout.consume {
            logout()
        }

        R.id.version -> false
        else -> bindingBase.drawerLayout.consume {
            logout()
        }
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
            toast("No network connection, please turn on your mobile network")
            return
        }

        this@logout?.alert("Sure to Logout?") {
            yesButton {
                BaseApplication.upComingMeetingId = null
                BaseApplication.passedMeetingId = null
                logoutCall()
            }
            negativeButton("Stay") {}
        }?.show()
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
                                this@MentorBaseMainActivity,
                                USER_PREF
                            )
                        )
                        startActivity<SplashActivity>()
                        finish()
                        dismissKeyboard()
                    } else
                        toast(result.message.toString())
                },
                {
                    toast("Some error occured.")
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
                            setData(
                                KEY_TIMEZONE,
                                result.data?.timezone
                            )
                            setData(
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
            toast("No network connection, please turn on your mobile network")
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
                        MENTOR_HELP_URL = result.data.mentorFaq
                    }
                }
    }

    private fun showSessionReminder() {
        startActivity(Intent(this, AddSessionDialog::class.java))
    }

}