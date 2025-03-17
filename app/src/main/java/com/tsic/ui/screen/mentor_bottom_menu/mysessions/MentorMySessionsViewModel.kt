package com.tsic.ui.screen.mentor_bottom_menu.mysessions


import android.util.Log
import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.model.mentor_api.SessionResponse
import com.tsic.data.remote.api.MentorApiService
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.logoutForTnC
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers
import org.jetbrains.anko.indeterminateProgressDialog

class MentorMySessionsViewModel(private val activity: MentorMySessionsActivity) {


    var listSession = ObservableField<List<SessionResponse?>>(listOf())
    var isNoShow = ObservableField<Boolean>()


    private var disposable: Disposable? = null
    private val apiService by lazy { MentorApiService.create() }

    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }


    fun setupViewModel() {

    }

    fun fetchData() {
        activity.dismissKeyboard()
        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

       listSession.set(listOf())
        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        val dialog = activity.indeterminateProgressDialog("Loading data...").apply {
            setCancelable(false)

        }
            val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
            disposable = apiService.getSessionList(token)
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe {
                    activity?.runOnUiThread { dialog?.show() }
                }
                .doAfterTerminate {
                    activity?.runOnUiThread { dialog?.dismiss() }
                }
                .subscribe(
                    { result ->
                        if (result.status == Status.SUCCESS) {
                            listSession.set(result.data)
                            Log.e(">>","....session data = ${result.data}")


//                            listSession.set(result)
                        } else {
                            if (result.message == "Logged Out") {
                                activity.logoutForTnC()
                            } else {
                                activity.showToast(result.message.toString())
                            }
//                            activity.showToast(result.message)
                            // activity?.showToast(result.message)
                        }
                    },
                    { error ->
                        activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                    }
                )
        }

        fun dispose() {
            disposable?.dispose()
        }

        fun onResume() = fetchData()
        fun onPause() = dispose()
        fun onStop() = dispose()

}