package com.tsic.ui.screen.mentee_bottom_menu.myprofile

/**
 * @author Kaiser Perwez
 */

import android.Manifest
import android.annotation.SuppressLint
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
import android.widget.EditText
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AlertDialog
import androidx.core.app.ActivityCompat
import androidx.core.app.NotificationCompat
import androidx.core.content.ContextCompat
import androidx.core.view.GravityCompat
import androidx.databinding.DataBindingUtil
import com.esafirm.imagepicker.features.registerImagePicker
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
import com.tsic.util.getFilePathFromUri
import gun0912.tedimagepicker.builder.TedImagePicker
import org.jetbrains.anko.*
import org.jetbrains.anko.design.textInputEditText
import org.jetbrains.anko.design.textInputLayout
import java.util.*


class MenteeMyProfileActivity : MenteeBaseMainActivity() {

    private var disposable: CountDownTimer? = null
    var adapter: MenteeBannerListAdapter? = null

    //declarations
    var binding: ActivityMenteeMyProfileBinding? = null

    private val cameraPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestMultiplePermissions()
    ) { _ ->
        updateImage()
    }

    private val popupPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.StartActivityForResult()
    ) { _ -> showPermissionDialog()
    }

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
        showPermissionDialog()
        binding?.apply {
            vm = MenteeMyProfileViewModel(this@MenteeMyProfileActivity)
            activity = this@MenteeMyProfileActivity
            initBannerAdapter()
            contentLayout.swipeRefreshLayout.setOnRefreshListener {
                vm?.getUserData(true)
                //vm?.getSystemMessage()
            }
            contentLayout.chatMentor.setOnClickListener {
                gotoMentorChatScreen(it)
            }
            contentLayout.goalMenteeScreen.setOnClickListener {
                gotoGoalScreen(it)
            }
            contentLayout.meetingMentor.setOnClickListener {
                gotoMeetingScreen(it)
            }
            val currentNightMode = configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK
            when (currentNightMode) {
                Configuration.UI_MODE_NIGHT_NO -> {
                    contentLayout.rootContentLayout.setBackgroundResource(R.drawable.bg_profile_top)
                }
                Configuration.UI_MODE_NIGHT_YES -> {
                    contentLayout.rootContentLayout.setBackgroundResource(R.drawable.bg1)
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
        } catch (_: java.lang.Exception) {
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

        val permissionsToRequest = mutableListOf<String>()

        if (ContextCompat.checkSelfPermission(this, Manifest.permission.CAMERA) != PackageManager.PERMISSION_GRANTED) {
            permissionsToRequest.add(Manifest.permission.CAMERA)
//            permissionsToRequest.add(Manifest.permission.READ_EXTERNAL_STORAGE)
//            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
//                permissionsToRequest.add(Manifest.permission.READ_MEDIA_IMAGES)
//            }
        }

        // Request permissions if not granted
        if (permissionsToRequest.isNotEmpty()) {
            cameraPermissionLauncher.launch(permissionsToRequest.toTypedArray())
            return
        }

        TedImagePicker.with(this)
            .image()
            .max(1, "You can only select one image")
            .dropDownAlbum()
            .start { uri ->
                val filePath = getFilePathFromUri(uri, this)
            if (filePath.isNotEmpty()) {
                binding?.vm?.apply {
                    profilePic.set(filePath)
                    updateProfile()
                }
            } else {
                toast("Something went wrong")
            }
        }

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
              //  window.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_ADJUST_RESIZE)
            }.show()
        }
    }

    private fun showPermissionDialog() {
        if (!Settings.canDrawOverlays(this)) {
            AlertDialog.Builder(this)
                .setTitle("Overlay Permission Needed")
                .setMessage("This app needs the 'Display over other apps' permission to show important alerts while you're using other apps.")
                .setCancelable(false)
                .setPositiveButton(
                    android.R.string.ok
                ) { dialog, which ->
                    checkPermission()
                }
                .setIconAttribute(android.R.attr.alertDialogIcon)
                .show()
        } else {
            getNotificationPermission()
        }
    }

    @SuppressLint("ObsoleteSdkInt")
    private fun checkPermission() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
        if (!Settings.canDrawOverlays(this)) {
            if ("xiaomi" == Build.MANUFACTURER.lowercase(Locale.ROOT)) {
                try {
                    val intent = Intent("miui.intent.action.APP_PERM_EDITOR")
                    intent.setClassName(
                        "com.miui.securitycenter",
                        "com.miui.permcenter.permissions.PermissionsEditorActivity"
                    )
                    intent.putExtra("extra_pkgname", packageName)
                    startActivity(intent)
                } catch (e: Exception) {
                    val intent = Intent(
                        Settings.ACTION_MANAGE_OVERLAY_PERMISSION,
                        Uri.parse("package:$packageName")
                    )
                    popupPermissionLauncher.launch(intent)
                }
            } else {
                val intent = Intent(
                    Settings.ACTION_MANAGE_OVERLAY_PERMISSION,
                    Uri.parse("package:$packageName")
                )
                popupPermissionLauncher.launch(intent)
            }
        }
        }
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

