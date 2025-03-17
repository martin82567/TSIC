package com.tsic.ui.screen.mentor_drawer_menu.resource


import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.model.mentor_api.ELearning
import com.tsic.data.remote.api.MentorApiService
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.tsic.util.extension.logoutForTnC
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers

class MentorResourceViewModel(private val activity: MentorResourceActivity) {

    var searchString = ObservableField("")
    var varInt = ObservableField(0)
    var varList = ObservableField<List<ELearning>>(listOf())
    var profilePic = ObservableField<String>("")
    var description = ObservableField<String>("")
    var title = ObservableField<String>("")


    var varExtraString = ""

    private var disposable: Disposable? = null
    private val apiService by lazy { MentorApiService.create() }

    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }


    fun setupViewModel() {

    }

    fun fetchData() {
        activity.dismissKeyboard()
        varList.set(listOf())
        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = apiService.getLearningList(token)
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe {
                activity.isBusyLoadingData(true)
            }
            .doAfterTerminate {
                activity.isBusyLoadingData(false)
            }
            .subscribe(
                { result ->
                    if (result.status == Status.SUCCESS) {
                        varList.set(result?.data?.elearninglist)
                        /* result.data?.learningList?.let {
                             description.set(it.description)
                             title.set(it.titleName)
                             profilePic.set("${MentorApiService.MENTOR_IMAGE_URL}${it.fileUrl}")

                         }*/

                    } else {
                        if (result.message == "Logged Out") {
                            activity.logoutForTnC()
                        } else {
                            activity.showToast(result.message)
                        }
                        activity.isBusyLoadingData(false)
                    }
                },
                { error ->
                    activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                    activity.isBusyLoadingData(false)

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

/*interface LearningViewModelCallbacks{
	fun onCallback1(param1:Any)*/

