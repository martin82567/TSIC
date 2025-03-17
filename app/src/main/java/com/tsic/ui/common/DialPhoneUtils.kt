/*
package com.tsic.ui.common

/**
 * @author Kaiser Perwez
 */
 
import android.app.Activity
import android.content.Intent
import android.net.Uri
import com.karumi.dexter.Dexter
import com.karumi.dexter.PermissionToken
import com.karumi.dexter.listener.PermissionDeniedResponse
import com.karumi.dexter.listener.PermissionGrantedResponse
import com.karumi.dexter.listener.PermissionRequest
import com.karumi.dexter.listener.single.PermissionListener
import org.jetbrains.anko.alert
import org.jetbrains.anko.toast

class DialPhoneUtils(val searchActivity: Activity,val phone:Long) {

    fun checkPermissionsAndDial() {
        Dexter.withActivity(searchActivity)
            .withPermission(
                android.Manifest.permission.CALL_PHONE
            )
            .withListener(object : PermissionListener {
                override fun onPermissionRationaleShouldBeShown(
                    permission: PermissionRequest?,
                    token: PermissionToken?
                ) {
                    token?.continuePermissionRequest()
                }

                override fun onPermissionGranted(response: PermissionGrantedResponse?) {
                    dialCallIntent()
                }

                override fun onPermissionDenied(response: PermissionDeniedResponse?) {
                    showCameraPermissionDetailsDialog()
                }
            }).withErrorListener { searchActivity.toast("Error occurred while asking for camera permission!") }
            .onSameThread()
            .check()
    }

    fun dialCallIntent() {
        val intent = Intent(Intent.ACTION_DIAL)
        intent.data = Uri.parse("tel:$phone")
        searchActivity.startActivity(intent)

    }


    private fun showCameraPermissionDetailsDialog() {
        searchActivity.alert("This app needs permission for phone calls", "Need Permissions") {
            positiveButton("Allow") {
                it.dismiss()
                checkPermissionsAndDial()
            }
            negativeButton("Ignore") {
                it.dismiss()
                // listener.PermissionDenied()
            }
        }.show()
    }
}*/
