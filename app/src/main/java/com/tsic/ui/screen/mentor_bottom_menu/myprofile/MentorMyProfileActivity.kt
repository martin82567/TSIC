package com.tsic.ui.screen.mentor_bottom_menu.myprofile

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
import android.text.InputType
import android.view.View
import android.view.WindowManager
import android.widget.EditText
import androidx.appcompat.app.AlertDialog
import androidx.core.app.ActivityCompat
import androidx.core.app.NotificationCompat
import androidx.core.content.ContextCompat
import androidx.core.view.GravityCompat
import androidx.databinding.DataBindingUtil
import androidx.databinding.ObservableField
import com.jaiselrahman.filepicker.activity.FilePickerActivity
import com.jaiselrahman.filepicker.config.Configurations
import com.jaiselrahman.filepicker.model.MediaFile
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
import org.jetbrains.anko.*
import org.jetbrains.anko.design.textInputEditText
import org.jetbrains.anko.design.textInputLayout
import java.util.*


class MentorMyProfileActivity : MentorBaseMainActivity() {
    val ACTION_MANAGE_OVERLAY_PERMISSION_REQUEST_CODE = 107

    private val FILE_REQUEST_CODE: Int = 101
    private var disposable: CountDownTimer? = null

    var name = ObservableField<String>("")
    var profilePic = ObservableField<String>("")


    //declarations
    var binding: ActivityMentorMyProfileBinding? = null
    var adapter: MentorBannerListAdapter? = null
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
        checkPermission()
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


    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 111) {
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

    override fun onRequestPermissionsResult(
        requestCode: Int,
        permissions: Array<out String>,
        grantResults: IntArray
    ) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        if (requestCode == 110) {
            return
        }
        /* if (requestCode == 109) {
             if (grantResults.isNotEmpty()
                 && grantResults[0] == PackageManager.PERMISSION_GRANTED
             ) {
                 startLocationService(this@MentorMyProfileActivity)
             } else {
                 stopLocationService(this@MentorMyProfileActivity)
             }
         } else */
        if (grantResults.isNotEmpty() && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
            updateImage()
        } else {
            showToast("Please approve permissions to open ImagePicker")
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

