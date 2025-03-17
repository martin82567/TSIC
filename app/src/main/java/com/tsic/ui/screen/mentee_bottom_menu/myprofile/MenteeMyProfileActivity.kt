package com.tsic.ui.screen.mentee_bottom_menu.myprofile

/**
 * @author Kaiser Perwez
 */

import android.Manifest
import android.app.Activity
import android.app.NotificationChannel
import android.app.NotificationManager
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.content.res.Configuration
import android.net.Uri
import android.os.Build
import android.os.CountDownTimer
import android.provider.Settings
import android.view.View
import android.view.WindowManager
import android.widget.EditText
import androidx.core.app.ActivityCompat
import androidx.core.app.NotificationCompat
import androidx.core.view.GravityCompat
import androidx.databinding.DataBindingUtil
import com.jaiselrahman.filepicker.activity.FilePickerActivity
import com.jaiselrahman.filepicker.config.Configurations
import com.jaiselrahman.filepicker.model.MediaFile
import com.tsic.R
import com.tsic.databinding.ActivityMenteeMyProfileBinding
import com.tsic.ui.base.MenteeBaseMainActivity
import com.tsic.ui.screen.mentee_bottom_menu.mychats.my_mentor_list.MenteeMyMentorListActivity
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.MenteeMyMeetingActivity
import com.tsic.ui.screen.mentee_drawer_menu.goal.MenteeGoalActivity
import com.tsic.ui.screen.mentee_drawer_menu.learning.MenteeLearningActivity
import com.tsic.ui.screen.mentee_drawer_menu.task.MenteeTaskActivity
import com.tsic.ui.screen.message_center.MessageCenterActivity
import com.tsic.ui.screen.util_screens.FullscreenImageActivity
import com.tsic.util.INTENT_KEY_TITLE
import com.tsic.util.INTENT_KEY_URL
import com.tsic.util.extension.dismissKeyboard
import org.jetbrains.anko.*
import org.jetbrains.anko.design.textInputEditText
import org.jetbrains.anko.design.textInputLayout
import java.util.*


class MenteeMyProfileActivity : MenteeBaseMainActivity() {

    private val ACTION_MANAGE_OVERLAY_PERMISSION_REQUEST_CODE = 107

    private val FILE_REQUEST_CODE: Int = 101
    private var disposable: CountDownTimer? = null
    var adapter: MenteeBannerListAdapter? = null

    //declarations
    var binding: ActivityMenteeMyProfileBinding? = null

    override fun getContentView() {
        val stub = bindingBase.appBarMain.viewstub.viewStub
        stub?.layoutResource = R.layout.activity_mentee_my_profile
        stub?.setOnInflateListener { _, inflatedView ->
            binding = DataBindingUtil.bind(inflatedView)
            initUiAndListeners()
        }
        stub?.inflate()
    }

    override fun getNavigationMenuItemId(): Int {
        return R.id.nav_bottom_mentee_my_profile
    }


    private fun initUiAndListeners() {
        bindingBase?.appBarMain?.toolbar?.title == "My Profile"
        checkPermission()
        binding?.apply {
            vm = MenteeMyProfileViewModel(this@MenteeMyProfileActivity)
            activity = this@MenteeMyProfileActivity
            initBannerAdapter()
            contentLayout?.swipeRefreshLayout?.setOnRefreshListener {
                vm?.getUserData(true)
                //vm?.getSystemMessage()
            }
            contentLayout?.chatMentor?.setOnClickListener {
                gotoMentorChatScreen(it)
            }
            contentLayout?.goalMenteeScreen?.setOnClickListener {
                gotoGoalScreen(it)
            }
            contentLayout?.meetingMentor?.setOnClickListener {
                gotoMeetingScreen(it)
            }
            val currentNightMode = configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK
            when (currentNightMode) {
                Configuration.UI_MODE_NIGHT_NO -> {
                    contentLayout?.rootContentLayout?.setBackgroundResource(R.drawable.bg_profile_top)
                } // Night mode is not active, we're using the light theme
                Configuration.UI_MODE_NIGHT_YES -> {
                    contentLayout?.rootContentLayout?.setBackgroundResource(R.drawable.bg1)
                } // Night mode is active, we're using dark theme
            }
            /*contentLayout?.gotToResource?.setOnClickListener {
                gotoResouceScreen(it)
            }*/
            clearBadge()
        }
    }

    fun getNotificationPermission() {
        try {
            if (Build.VERSION.SDK_INT > Build.VERSION_CODES.S_V2) {
                ActivityCompat.requestPermissions(
                    this, arrayOf<String>(Manifest.permission.POST_NOTIFICATIONS),
                    110
                )
            }
        } catch (e: java.lang.Exception) {
        }
    }

    private fun initBannerAdapter() {
        binding?.contentLayout?.apply {
            adapter = MenteeBannerListAdapter(binding?.vm?.bannerMsgList!!)
            rvBannerList.adapter = adapter
        }
    }

    fun showImage() {
        val url = binding?.vm?.profilePic?.get() ?: ""
        startActivity<FullscreenImageActivity>(
            INTENT_KEY_TITLE to "Profile Pic",
            INTENT_KEY_URL to url
        )


    }

    fun updateImage() {
        val intent = Intent(this, FilePickerActivity::class.java).apply {
            putExtra(
                FilePickerActivity.CONFIGS, Configurations.Builder()
                    .setCheckPermission(true)
                    .setShowImages(true)
                    .setShowAudios(false)
                    .setShowVideos(false)
                    .enableImageCapture(true)
                    .enableVideoCapture(false)
                    .setMaxSelection(1)
                    //.setSingleChoiceMode(true)
                    .setSkipZeroSizeFiles(true)
                    .build()
            )
        }
        startActivityForResult(intent, FILE_REQUEST_CODE)
    }

    fun updateUsername() {
        binding?.vm?.apply {
            alert {
                isCancelable = false
                var nameText: EditText? = null
                customView {
                    verticalLayout {
                        padding = dip(20)
                        /*    input = editText() {
                                hint = "New name"
                                setText(name?.get() ?: "")
                            }*/
                        textInputLayout {
                            hint = "New name"
                            nameText = textInputEditText {
                                textSize = 16f
                                isSingleLine = true
                                setText(name.get() ?: "")
                            }
                        }
                    }
                }
                positiveButton("Update") {
                    name.set("${nameText?.text}")
                    dismissKeyboard()
                    if ((name.get() ?: "").isNotBlank())
                        updateProfile()
                }
                cancelButton { dismissKeyboard() }
                window.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_ADJUST_RESIZE)
            }.show()
        }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 110) {
            return
        }
        if (requestCode == FILE_REQUEST_CODE && resultCode == Activity.RESULT_OK) {
            val mPaths =
                data?.getParcelableArrayListExtra<MediaFile>(FilePickerActivity.MEDIA_FILES)
            if (mPaths != null && mPaths.isNotEmpty())
                binding?.vm?.apply {
                    profilePic.set(mPaths.get(0)?.path)
                    updateProfile() //for image
                }


        }
        if (requestCode == ACTION_MANAGE_OVERLAY_PERMISSION_REQUEST_CODE) {
            if (!Settings.canDrawOverlays(this)) {
                // You don't have permission
                checkPermission();
            } else {
                getNotificationPermission()
            }
        }
    }

    private fun checkPermission() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            if (!Settings.canDrawOverlays(this)) {
                if ("xiaomi" == Build.MANUFACTURER.toLowerCase(Locale.ROOT)) {
                    try {
                        val intent = Intent("miui.intent.action.APP_PERM_EDITOR")
                        intent.setClassName(
                            "com.miui.securitycenter",
                            "com.miui.permcenter.permissions.PermissionsEditorActivity"
                        )
                        intent.putExtra("extra_pkgname", getPackageName())
                        startActivity(intent)
                    } catch (e: Exception) {
                        val intent = Intent(
                            Settings.ACTION_MANAGE_OVERLAY_PERMISSION,
                            Uri.parse("package:$packageName")
                        )
                        startActivityForResult(
                            intent,
                            ACTION_MANAGE_OVERLAY_PERMISSION_REQUEST_CODE
                        )
                    }
                } else {
                    val intent = Intent(
                        Settings.ACTION_MANAGE_OVERLAY_PERMISSION,
                        Uri.parse("package:$packageName")
                    )
                    startActivityForResult(intent, ACTION_MANAGE_OVERLAY_PERMISSION_REQUEST_CODE)
                }
            }

        }
    }

    override fun onRequestPermissionsResult(
        requestCode: Int,
        permissions: Array<out String>,
        grantResults: IntArray
    ) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        if (grantResults.isNotEmpty() && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
            updateImage()
        } else {
            showToast("Please approve permissions to open ImagePicker")
        }
        /*ActivityCompat.requestPermissions(
            this,
            arrayOf(
                Settings.ACTION_MANAGE_OVERLAY_PERMISSION
            ),
            ACTION_MANAGE_OVERLAY_PERMISSION_REQUEST_CODE
        )*/
    }

    fun clearBadge() {
        val n = NotificationCompat.Builder(applicationContext, getString(R.string.app_name))
            .setSmallIcon(R.drawable.tsic)
            .setContentTitle("")
            .setContentText("")
            .setContentInfo("")
            .setPriority(NotificationCompat.PRIORITY_HIGH)
            .setAutoCancel(true)
            .setTicker(getString(R.string.app_name))
            //.setSound(null)
            .setNumber(0)
        val notificationManager =
            getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                getString(R.string.app_name),
                "CHANNEL_NAME",
                NotificationManager.IMPORTANCE_DEFAULT
            )
            channel.setShowBadge(true)
            notificationManager.createNotificationChannel(channel)
        }
        notificationManager.notify(
            101,
            n.build()
        )
        notificationManager.cancel(101)
    }

    fun showToast(msg: String?) {
        msg?.let { toast(it).show() }
    }

    override fun onResume() {
        super.onResume()
        binding?.vm?.getUserData(true)
        binding?.vm?.getSystemMessage()
        disposable = object : CountDownTimer(360000, 5500) {
            override fun onTick(millisUntilFinished: Long) {
                binding?.vm?.getUserData()
                binding?.vm?.getSystemMessage()
            }

            override fun onFinish() {
                start()
            }
        }.start()
        showMentorSessionBadge()

    }

    override fun onPause() {
        super.onPause()
        binding?.vm?.onPause()
        disposable?.cancel()

    }


    override fun onStop() {
        super.onStop()
        binding?.vm?.onStop()
    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.contentLayout?.swipeRefreshLayout?.apply {
            setProgressViewOffset(true, 100, 200)
            isRefreshing = yes
            /*binding?.contentLayout?.scrollView?.visibility =
                if (yes) View.INVISIBLE else View.VISIBLE*/
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

    fun gotoMentorChatScreen(view: View?) {

        try {
            startActivity<MenteeMyMentorListActivity>()
        } catch (ex: Exception) {

        }

    }

    fun gotoGoalScreen(view: View?) {

        try {
            startActivity<MenteeGoalActivity>()
        } catch (ex: Exception) {
        }

    }


    fun gotoMeetingScreen(view: View?) {
        try {
            startActivity<MenteeMyMeetingActivity>()

        } catch (ex: Exception) {
        }
    }

    fun gotoResourceScreen(view: View?) {
        try {
            startActivity<MenteeLearningActivity>()

        } catch (ex: Exception) {
        }
    }

    fun gotoMessageCenter(view: View) {
        view.context?.startActivity<MessageCenterActivity>()
    }

    fun gotoTasksScreen(view: View?) {
        try {
            startActivity<MenteeTaskActivity>()
        } catch (ex: Exception) {

        }
    }

    val time_limit = 2000
    var back_pressed = 0L
    override fun onBackPressed() {
        if (bindingBase.drawerLayout.isDrawerOpen(GravityCompat.START)) {
            bindingBase.drawerLayout.closeDrawer(GravityCompat.START)
        } else {
            if (time_limit + back_pressed > System.currentTimeMillis()) {
                finish()
            } else
                toast("Press twice to exit")
        }
        back_pressed = System.currentTimeMillis()
    }

    fun logOut(view: View) {
        logout()
    }


}
