package com.tsic.ui.common

/**
 * @author Kaiser Perwez
 */

import android.app.Activity
import com.karumi.dexter.Dexter
import com.karumi.dexter.MultiplePermissionsReport
import com.karumi.dexter.PermissionToken
import com.karumi.dexter.listener.PermissionRequest
import com.karumi.dexter.listener.multi.MultiplePermissionsListener
import org.jetbrains.anko.alert
import org.jetbrains.anko.toast

object PermissionUtil {
    fun checkCameraStoragePermission(activity: Activity) {
        Dexter.withActivity(activity)
            .withPermissions(
                android.Manifest.permission.CAMERA,
                android.Manifest.permission.WRITE_EXTERNAL_STORAGE
            )
            .withListener(object : MultiplePermissionsListener {
                override fun onPermissionsChecked(report: MultiplePermissionsReport?) {
                    if (report == null)
                        return

                    if (report.areAllPermissionsGranted())
                        (activity as CameraStoragePermissionListener).onCameraStoragePermissionGranted()
                    else
                        showCameraStoragePermissionDetailsDialog(activity)
                }

                override fun onPermissionRationaleShouldBeShown(
                    permissions: MutableList<PermissionRequest>?,
                    token: PermissionToken?
                ) {
                    token?.continuePermissionRequest()
                }
            })
            .withErrorListener { activity.toast("Error occurred while asking for camera and storage permission!") }
            .onSameThread()
            .check()
    }

    private fun showCameraStoragePermissionDetailsDialog(activity: Activity) {
        activity.alert(
            "Camera and storage permission is needed to capture/upload files",
            "Need Permissions"
        ).apply {
            positiveButton("Allow") {
                it.cancel()
                checkCameraStoragePermission(activity)
            }
            negativeButton("Ignore") {
                it.cancel()
            }
        }.show()
    }
}

interface CameraStoragePermissionListener {
    fun onCameraStoragePermissionGranted()
}
