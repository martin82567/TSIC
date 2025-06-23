package com.tsic.ui.screen.mentor_bottom_menu.myprofile

/**
 * @author Kaiser Perwez
 */

import android.Manifest
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
import android.text.InputType
import android.view.View
import android.view.WindowManager
import android.widget.EditText
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AlertDialog
import androidx.core.app.ActivityCompat
import androidx.core.app.NotificationCompat
import androidx.core.content.ContextCompat
import androidx.core.view.GravityCompat
import androidx.databinding.DataBindingUtil
import androidx.databinding.ObservableField
import com.esafirm.imagepicker.features.ImagePickerConfig
import com.esafirm.imagepicker.features.ImagePickerMode
import com.esafirm.imagepicker.features.ReturnMode
import com.esafirm.imagepicker.features.registerImagePicker
import com.tsic.R
import com.tsic.databinding.ActivityMentorMyProfileBinding
import com.tsic.ui.base.MentorBaseMainActivity
import com.tsic.ui.screen.mentor_bottom_menu.mychats.my_mentee_list.MentorMyMenteeChatListActivity
import com.tsic.ui.screen.mentor_bottom_menu.mysessions.MentorMySessionsActivity
import com.tsic.ui.screen.mentor_drawer_menu.meetings.MentorMyMeetingActivity
import com.tsic.ui.screen.message_center.MessageCenterActivity
import com.tsic.ui.screen.util_screens.FullscreenImageActivity
import com.tsic.util.INTENT_KEY_TITLE
import com.tsic.util.INTENT_KEY_URL
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.getFilePathFromUri
import gun0912.tedimagepicker.builder.TedImagePicker
import org.jetbrains.anko.alert
import org.jetbrains.anko.cancelButton
import org.jetbrains.anko.configuration
import org.jetbrains.anko.customView
import org.jetbrains.anko.design.textInputEditText
import org.jetbrains.anko.design.textInputLayout
import org.jetbrains.anko.dip
import org.jetbrains.anko.padding
import org.jetbrains.anko.startActivity
import org.jetbrains.anko.toast
import org.jetbrains.anko.verticalLayout
import java.util.Locale


class MentorMyProfileActivity : MentorBaseMainActivity() {

    private var disposable: CountDownTimer? = null

    var name = ObservableField<String>("")
    var profilePic = ObservableField<String>("")


    //declarations
    var binding: ActivityMentorMyProfileBinding? = null
    var adapter: MentorBannerListAdapter? = null

    private val popupPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.StartActivityForResult()
    ) { _ ->
        showPermissionDialog()
    }

    private val cameraPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestMultiplePermissions()
    ) { _ ->
        updateImage()
    }

    override fun getContentView() {
        val stub = bindingBase.appBarMain.viewstub.viewStub
        stub?.layoutResource = R.layout.activity_mentor_my_profile
        stub?.setOnInflateListener { _, inflatedView ->
            binding = DataBindingUtil.bind(inflatedView)
            initUiAndListeners()
        }
        stub?.inflate()
    }

    override fun getNavigationMenuItemId(): Int {
        return R.id.nav_bottom_mentor_my_profile
    }


    private fun initUiAndListeners() {

        supportActionBar?.title = "MyProfileMentor"
        showPermissionDialog()
//        checkLocationPermission()
//        val gpsTracker = GpsTracker(this@MentorMyProfileActivity)
//        if (!gpsTracker.canGetLocation())
//            showSettingsAlert()
//        else{
//
//        }
//        startLocationService(this@MentorMyProfileActivity)
        binding?.apply {
            activity = this@MentorMyProfileActivity
            vm = MentorMyProfileViewModel(this@MentorMyProfileActivity)
            initBannerAdapter()

            vm?.fetchData()
            //vm?.getSystemMessage()
            vm?.fetchMeetings()
            contentLayout.swipeRefreshLayout.let {
                it.setProgressViewOffset(false, 100, 200)
                it.setOnRefreshListener {
                    vm?.fetchData(true)
                    //vm?.getSystemMessage()
                }
            }
            contentLayout.iVPic.setOnClickListener {
                showImage()
            }
            when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
                Configuration.UI_MODE_NIGHT_NO -> {
                    contentLayout.rootContentLayout.setBackgroundResource(R.drawable.bg_profile_top)
                } // Night mode is not active, we're using the light theme
                Configuration.UI_MODE_NIGHT_YES -> {
                    contentLayout.rootContentLayout?.setBackgroundResource(R.drawable.bg1)
                } // Night mode is active, we're using dark theme
            }
        }


        clearBadge()
        //setBadge(0)
        //resetBadge()
    }

    private fun initBannerAdapter() {
        binding?.contentLayout?.apply {
            adapter = MentorBannerListAdapter(binding?.vm?.bannerMsgList!!)
            rvBannerList.adapter = adapter
        }
    }

    /* private fun checkLocationPermission() {
         if (ActivityCompat.checkSelfPermission(
                 this,
                 android.Manifest.permission.ACCESS_FINE_LOCATION
             ) != PackageManager.PERMISSION_GRANTED && ActivityCompat.checkSelfPermission(
                 this,
                 android.Manifest.permission.ACCESS_COARSE_LOCATION
             ) != PackageManager.PERMISSION_GRANTED
         ) {
             ActivityCompat
                 .requestPermissions(
                     this@MentorMyProfileActivity,
                     arrayOf(
                         android.Manifest.permission.ACCESS_FINE_LOCATION,
                         android.Manifest.permission.ACCESS_COARSE_LOCATION
                     ),
                     109
                 );
             stopLocationService(this@MentorMyProfileActivity)
         } else {
             startLocationService(this@MentorMyProfileActivity)
         }
     }*/


    fun showImage() {
        val url = binding?.vm?.profilePic?.get() ?: ""
        startActivity<FullscreenImageActivity>(
            INTENT_KEY_TITLE to "Profile Pic",
            INTENT_KEY_URL to url
        )
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
                    if ((name.get() ?: "").isNotBlank())
                        updateProfile()
                }
                cancelButton { dismissKeyboard() }
                window.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_ADJUST_RESIZE)
            }.show()
        }
    }

    fun updateUseremail() {
        binding?.vm?.apply {
            alert {
                isCancelable = false
                var emailText: EditText? = null
                customView {
                    verticalLayout {
                        padding = dip(20)
                        /*    input = editText() {
                                hint = "New name"
                                setText(name?.get() ?: "")
                            }*/
                        textInputLayout {
                            hint = "New name"
                            emailText = textInputEditText {
                                textSize = 16f
                                isSingleLine = true
                                inputType = InputType.TYPE_TEXT_VARIATION_EMAIL_ADDRESS
                                setText(personalEmail.get() ?: "")
                            }
                        }
                    }
                }
                positiveButton("Update") {
                    personalEmail.set("${emailText?.text}")
                    if ((personalEmail.get() ?: "").isNotBlank())
                        updateProfile()
                }
                cancelButton { dismissKeyboard() }
                window.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_ADJUST_RESIZE)
            }.show()
        }
    }

    fun updateUserphone() {
        binding?.vm?.apply {
            alert {
                isCancelable = false
                var phoneText: EditText? = null
                customView {
                    verticalLayout {
                        padding = dip(20)
                        /*    input = editText() {
                                hint = "New name"
                                setText(name?.get() ?: "")
                            }*/
                        textInputLayout {
                            hint = "Update Phone Number"
                            phoneText = textInputEditText {
                                textSize = 16f
                                isSingleLine = true
                                inputType = InputType.TYPE_CLASS_PHONE
                                setText(phoneNumber.get() ?: "")
                            }
                        }
                    }
                }
                positiveButton("Update") {
                    phoneNumber.set("${phoneText?.text}")
                    if ((phoneNumber.get() ?: "").isNotBlank())
                        updateProfile()
                }
                cancelButton { dismissKeyboard() }
                window.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_ADJUST_RESIZE)
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
                        intent.putExtra("extra_pkgname", getPackageName())
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


    fun gotoChatScreen(view: View) {
        view.context?.startActivity<MentorMyMenteeChatListActivity>()
    }

    fun gotoMessageCenter(view: View) {
        view.context?.startActivity<MessageCenterActivity>()
    }

    fun gotoMeetingScreen(view: View) {
        view.context?.startActivity<MentorMyMeetingActivity>()
    }

    fun gotoSessionScreen(view: View) {
        view.context?.startActivity<MentorMySessionsActivity>()
    }


    fun isBusyLoadingData(yes: Boolean) {
        binding?.contentLayout?.swipeRefreshLayout?.isRefreshing = yes
    }

    fun showToast(msg: String?) {
        msg?.let { toast(it) }
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

    override fun onPause() {
        super.onPause()
        disposable?.cancel()
        binding?.vm?.onPause()
    }

    override fun onResume() {
        super.onResume()
        binding?.vm?.fetchData(true)
        binding?.vm?.getSystemMessage()
        disposable = object : CountDownTimer(360000, 5500) {
            override fun onTick(millisUntilFinished: Long) {
                binding?.vm?.fetchData()
                binding?.vm?.getSystemMessage()
            }

            override fun onFinish() {
                start()
            }
        }.start()
        showMentorSessionBadge()
    }


    override fun onStop() {
        super.onStop()
        binding?.vm?.onStop()
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

    fun showSettingsAlert() {
        val alertDialog = AlertDialog.Builder(this)

        // Setting Dialog Title
        alertDialog.setTitle("GPS is settings")

        // Setting Dialog Message
        alertDialog.setMessage("GPS is not enabled. Do you want to go to settings menu?")

        // On pressing Settings button
        alertDialog.setPositiveButton("Settings") { dialog, which ->
            Intent(Settings.ACTION_LOCATION_SOURCE_SETTINGS).also {
                startActivity(it)
            }
        }

        // on pressing cancel button
        alertDialog.setNegativeButton("Cancel") { dialog, which -> dialog.cancel() }
        alertDialog.show()
    }
}

