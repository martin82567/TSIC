package com.tsic.ui.screen.chatdetails

import androidx.databinding.ObservableBoolean
import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.*
import com.tsic.data.model.Status
import com.tsic.data.model.mentee_api.ChatMsg
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.data.remote.api.MentorApiService
import com.tsic.util.TYPE_MENTEE
import com.tsic.util.TYPE_MENTOR
import com.tsic.util.extension.isDeviceOnline
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers

class ChatDetailsViewModel(private val activity: ChatDetailsActivity) {
    private var disposable: Disposable? = null
    private val menteeApiService by lazy { MenteeApiService.create() }
    private val mentorApiService by lazy { MentorApiService.create() }

    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }


    var chatterPic = ObservableField("")
    var chatterName = ObservableField("")
    var chatterId = ObservableField("")
    var chatterType = ObservableField("")
    var chatterFirebaseToken = ObservableField("")
    var chatterDeviceType = ObservableField("")
    var videoButtonEnable = ObservableBoolean(true)
    var firebaseToken = ObservableField("")
    var deviceType = ObservableField("")
    var page = 0
    var chatCount = 0
    var moreData = true

    var mymsg = ObservableField("")
    val myId by lazy {
        userPrefs?.getInt(KEY_USER_ID, 0) ?: 0
    }
    val myLoginMode by lazy {
        userPrefs?.getString(KEY_LOGIN_MODE, TYPE_MENTEE) ?: TYPE_MENTEE
    }
    val myName = "${userPrefs?.getString(KEY_FIRST_NAME, "")} ${
        userPrefs?.getString(
            KEY_MIDDLE_NAME,
            ""
        )
    } ${
        userPrefs?.getString(
            KEY_LAST_NAME, ""
        )
    } "


    //var chatMsgList = ObservableField<List<ChatMsg?>>(listOf())
    var chatTempMsgList = mutableListOf<ChatMsg>()
    var chatMsgList = listOf<ChatMsg>()
    var chatCode = ""
    var timeZone = ObservableField("")
    var timeZoneOffset = ObservableField("")
    var prevMsgCount = 0
    var rvAdapter = ChatDetailsAdapter(chatMsgList, activity)

    fun fetchMsgList(showProgress: Boolean = true) {
        when (myLoginMode) {
            TYPE_MENTEE -> if (chatterType.get()
                    ?.toString() ?: "" == TYPE_MENTOR
            ) getMenteeMyMentorChatMsgList(
                showProgress
            )
            else getMenteeMyStaffChatMsgList(showProgress)

            else -> if (chatterType.get()
                    ?.toString() ?: "" == TYPE_MENTEE
            ) getMentorMyMenteeChatMsgList(
                showProgress
            ) else getMentorMyStaffChatMsgList(showProgress)
        }
    }

    private fun getMenteeMyMentorChatMsgList(showProgress: Boolean = true) {
        //activity.dismissKeyboard()
        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable = menteeApiService.getMentorChatMessages(
            token,
            chatterId.get().toString(),
            page.toString()
        )
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe {
                if (showProgress)
                    activity.isBusyLoadingData(true)
            }
            .doAfterTerminate {
                activity.isBusyLoadingData(false)
            }
            .subscribe(
                { result ->
                    activity.isBusyLoadingData(false)
                    if (result.status == Status.SUCCESS) {
                        if (chatCode.isBlank()) {
                            result.data?.apply {
                                this@ChatDetailsViewModel.chatCode = chatCode
                                timeZone.set(timezone)
                                timeZoneOffset.set("GMT$timezone_offset")
                                firebaseToken.set(firebaseId)
                                this@ChatDetailsViewModel.deviceType.set(deviceType)
                            }
                            activity.connectSocket(chatCode)
                        }
                        result.data.let {
                            val t = it?.count_message
                            chatCount =
                                if (t?.rem(15) == 0) t?.div(15) else t?.div(15)?.plus(1) ?: 0
                            chatCount
                            if (page == 0)
                                chatTempMsgList?.clear()
                            if (result.data?.chatList?.size == 0)
                                page--
                            else {
                                val v = it?.chatList?.apply {
                                    forEach {
                                        it.chatterServerPic = chatterPic.get()?.toString() ?: ""
                                    }
                                }
                                v?.forEach { it ->
                                    chatTempMsgList?.add(it)
                                }
                                /* chatMsgList=chatTempMsgList
                                 rvAdapter?.notifyDataSetChanged()*/
                                activity?.binding?.contentChatMessage?.rvChatMessageList?.apply {
                                    scrollToPosition(if (page * 15 == 0) 0 else page * 15 - 1)
                                    adapter = ChatDetailsAdapter(chatTempMsgList, activity)
                                }

                            }
                        }
                    } else {
                        activity.showToast(result.message.toString())
                    }
                },
                { error ->
                    activity.isBusyLoadingData(false)
                    activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                }
            )
    }

    private fun getMenteeMyStaffChatMsgList(showProgress: Boolean = true) {
        // activity.dismissKeyboard()
        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }


        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable =
            menteeApiService.getStaffChatMessages(
                token,
                chatterId.get()?.toString() ?: "0", page.toString()
            )
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe {
                    if (showProgress)
                        activity.isBusyLoadingData(true)
                }
                .doAfterTerminate {
                    activity.isBusyLoadingData(false)
                }
                .subscribe(
                    { result ->
                        activity.isBusyLoadingData(false)
                        if (result.status == Status.SUCCESS) {
                            if (chatCode.isBlank()) {
                                chatCode = result.data?.chatCode ?: ""
                                timeZone.set(result.data?.timezone)
                                timeZoneOffset.set("GMT" + result.data?.timezone_offset)
                                deviceType.set("")
                                activity.connectSocket(chatCode)
                            }
                            result.data.let {
                                /*chatMsgList.set(it?.chatList?.reversed()?.apply {
                                    forEach {
                                        it.chatterServerPic =
                                            chatterPic.get()?.toString() ?: ""
                                    }
                                })*/
                                val t = it?.count_message
                                chatCount =
                                    if (t?.rem(15) == 0) t?.div(15) else t?.div(15)?.plus(1) ?: 0
                                chatCount
                                if (page == 0)
                                    chatTempMsgList?.clear()
                                if (result.data?.chatList?.size == 0)
                                    moreData = false
                                else {
                                    moreData = true
                                    val v = it?.chatList?.apply {
                                        forEach {
                                            it.chatterServerPic = chatterPic.get()?.toString() ?: ""
                                        }
                                    }
                                    v?.forEach { it ->
                                        chatTempMsgList?.add(it)
                                    }
                                    /*chatMsgList=chatTempMsgList
//                                    activity?.binding?.contentChatMessage?.rvChatMessageList?.invalidate()
                                    rvAdapter?.notifyDataSetChanged()
*/
                                    activity?.binding?.contentChatMessage?.rvChatMessageList?.apply {
                                        scrollToPosition(if (page * 15 == 0) 0 else page * 15 - 1)
                                        adapter = ChatDetailsAdapter(chatTempMsgList, activity)
                                    }

                                }
                            }
                        } else {
                            activity.showToast(result.message.toString())
                        }
                    },
                    { error ->
                        activity.isBusyLoadingData(false)
                        activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                    }
                )
    }

    private fun getMentorMyMenteeChatMsgList(showProgress: Boolean = true) {
        //activity.dismissKeyboard()
        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }


        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable =
            mentorApiService.getMenteeChatMessages(
                token,
                chatterId.get()?.toString() ?: "0", page.toString()
            )
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe {
                    if (showProgress)
                        activity.isBusyLoadingData(true)
                }
                .doAfterTerminate {
                    activity.isBusyLoadingData(false)
                }
                .subscribe(
                    { result ->
                        activity.isBusyLoadingData(false)
                        if (result.status == Status.SUCCESS) {
                            if (chatCode.isBlank()) {
                                result.data?.apply {
                                    this@ChatDetailsViewModel.chatCode = chatCode
                                    timeZone.set(timezone)
                                    timeZoneOffset.set("GMT$timezone_offset")
                                    firebaseToken.set(firebaseId)
                                    this@ChatDetailsViewModel.deviceType.set(deviceType)
                                }
                                activity.connectSocket(chatCode)
                            }
                            result.data.let {
                                val t = it?.count_message
                                chatCount =
                                    if (t?.rem(15) == 0) t?.div(15) else t?.div(15)?.plus(1) ?: 0
                                chatCount
                                if (page == 0)
                                    chatTempMsgList?.clear()
                                if (result.data?.chatList?.size == 0)
                                    page--
                                else {
                                    val v = it?.chatList?.apply {
                                        forEach {
                                            it.chatterServerPic = chatterPic.get()?.toString() ?: ""
                                        }
                                    }
                                    v?.forEach { it ->
                                        chatTempMsgList?.add(it)
                                    }
                                    /*chatMsgList=chatTempMsgList
//                                    activity?.binding?.contentChatMessage?.rvChatMessageList?.invalidate()
                                    rvAdapter?.notifyDataSetChanged()*/
                                    activity?.binding?.contentChatMessage?.rvChatMessageList?.apply {
                                        scrollToPosition(if (page * 15 == 0) 0 else page * 15 - 1)
                                        adapter = ChatDetailsAdapter(chatTempMsgList, activity)
                                    }
                                    chatterDeviceType.set(it?.deviceType)
                                    chatterFirebaseToken.set(it?.firebaseId)


                                }
                            }
                        } else {
                            activity.showToast(result.message.toString())
                        }
                    },
                    { error ->
                        activity.isBusyLoadingData(false)
                        activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                    }
                )
    }

    private fun getMentorMyStaffChatMsgList(showProgress: Boolean = true) {
        //activity.dismissKeyboard()
        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }


        val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
        disposable =
            mentorApiService.getStaffChatMessages(
                token,
                chatterId.get()?.toString() ?: "0", page.toString()
            )
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread())
                .doOnSubscribe {
                    if (showProgress)
                        activity.isBusyLoadingData(true)
                }
                .doAfterTerminate {
                    activity.isBusyLoadingData(false)
                }
                .subscribe(
                    { result ->
                        activity.isBusyLoadingData(false)
                        if (result.status == Status.SUCCESS) {
                            if (chatCode.isBlank()) {
                                chatCode = result.data?.chatCode ?: ""
                                timeZone.set(result.data?.timezone)
                                timeZoneOffset.set("GMT" + result.data?.timezone_offset)
                                deviceType.set("")
                                activity.connectSocket(chatCode)
                            }
                            result.data.let {
                                /*it?.chatList?.reversed()
                                chatMsgList.set(it?.chatList?.reversed()?.apply {
                                    forEach {
                                        it.chatterServerPic =
                                            chatterPic.get()?.toString() ?: ""
                                    }
                                })*/

                                if (page == 0)
                                    chatTempMsgList?.clear()
                                if (result.data?.chatList?.size == 0)
                                    moreData = false
                                else {
                                    moreData = true
                                    val v = it?.chatList?.apply {
                                        forEach {
                                            it.chatterServerPic = chatterPic.get()?.toString() ?: ""
                                        }
                                    }
                                    v?.forEach { it ->
                                        chatTempMsgList?.add(it)
                                    }
                                    /*chatMsgList=chatTempMsgList
                                    //activity?.binding?.contentChatMessage?.rvChatMessageList?.invalidate()
                                    rvAdapter?.notifyDataSetChanged()*/
                                    activity?.binding?.contentChatMessage?.rvChatMessageList?.apply {
                                        scrollToPosition(if (page * 15 == 0) 0 else page * 15 - 1)
                                        adapter = ChatDetailsAdapter(chatTempMsgList, activity)
                                    }


                                }
                            }
                        } else {
                            activity.showToast(result.message.toString())
                        }
                    },
                    { error ->
                        activity.isBusyLoadingData(false)
                        activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                    }
                )
    }

    fun dispose() {
        disposable?.dispose()
    }

    fun onPause() = dispose()
    fun onStop() = dispose()

}


