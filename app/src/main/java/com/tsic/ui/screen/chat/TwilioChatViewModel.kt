package com.tsic.ui.screen.chat

import android.util.Log
import androidx.databinding.ObservableBoolean
import androidx.databinding.ObservableField
import com.tsic.data.local.prefs.*
import com.tsic.data.remote.api.TwilioApiService
import com.tsic.util.TYPE_MENTEE
import com.tsic.util.TYPE_MENTEE_STAFF
import com.tsic.util.TYPE_MENTOR_STAFF
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.isDeviceOnline
import com.twilio.chat.Message
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers

class TwilioChatViewModel(val activity: TwilioChatActivity) : ChatListener {

    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }

    var chatterPic = ObservableField("")
    var chatterName = ObservableField("")
    var chatterId = ObservableField("")
    var chatterType = ObservableField("")
    var videoButtonEnable = ObservableBoolean(true)
    var chatSid = ""
    var chatCode = ""
    var identity = mutableListOf<String>()

    var mymsg = ObservableField("")
    val myId by lazy {
        userPrefs?.getInt(KEY_USER_ID, 0) ?: 0
    }
    val myLoginMode by lazy {
        userPrefs?.getString(KEY_LOGIN_MODE, TYPE_MENTEE) ?: TYPE_MENTEE
    }
    val myName = "${userPrefs?.getString(KEY_FIRST_NAME, "")} ${
        userPrefs?.getString(KEY_MIDDLE_NAME, "")
    } ${
        userPrefs?.getString(KEY_LAST_NAME, "")
    } "

    var chatMsgList = mutableListOf<ChatMessage>()
    private val apiService by lazy { TwilioApiService.create() }
    private var disposable: Disposable? = null
    private val twilioChatManager: TwilioChatManager by lazy {
        TwilioChatManager(this)
    }

    fun fetch() {

        activity.dismissKeyboard()

        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }
        var type = userPrefs?.getString(KEY_LOGIN_MODE, "")
        var id = userPrefs?.getInt(KEY_USER_ID, 0)
        var name = userPrefs?.getString(KEY_FIRST_NAME, "")
        Log.d("TAG", "fetch: ${type}_${id}_${name}")
        disposable = apiService.fetchAccessToken(type, id.toString(), name)
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe {
                if (chatMsgList?.size == 0)
                    activity?.progressDialog?.show()
            }
            .doAfterTerminate {
            }
            .subscribe(
                { result ->
                    if (result.token != "") {
                        twilioChatManager.build(activity.applicationContext, result.token)
                        identity.add(result.identity.toString())
                    } else {
                        activity.showToast("Some Error Occurred")
                        activity?.progressDialog?.dismiss()
                    }
                },
                { error ->
                    activity.showToast(
                        error.message
                            ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later."
                    )
                    activity?.progressDialog?.dismiss()
                }
            )
    }

    fun saveChannelSid(channelSid: String) {

        activity.dismissKeyboard()

        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }

        disposable = apiService.saveChannelSid(
            channelSid,
            chatCode,
            if (chatterType.get() == TYPE_MENTEE_STAFF || chatterType.get() == TYPE_MENTOR_STAFF) chatterType.get() else ""
        )
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe {
                activity.progressDialog?.show()
            }
            .doAfterTerminate {
            }
            .subscribe(
                { result ->
                    if (!result.status)
                        result.message?.let { activity.showToast(it) }

                    activity.progressDialog?.dismiss()
                },
                { error ->
                    activity?.progressDialog?.dismiss()
                    activity.showToast(
                        error.message
                            ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later."
                    )
                }
            )
    }

    private fun sendUserNotification(message: String?) {

        if (!activity.isDeviceOnline()) {
            activity.showToast("Error: \n You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            return
        }
        var fName = userPrefs?.getString(KEY_FIRST_NAME, "")
        var mName = userPrefs?.getString(KEY_MIDDLE_NAME, "")
        var lName = userPrefs?.getString(KEY_LAST_NAME, "")
        var loginType = userPrefs?.getString(KEY_LOGIN_MODE, "")
        var name = "$fName $mName $lName"

        disposable = apiService.sendNotification(
            name, loginType, chatterType.get(), chatterId.get(), message
        )
            .subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe {
//                activity.progressDialog?.show()
            }
            .doAfterTerminate {
            }
            .subscribe(
                { result ->
                    Log.d("TAG", "sendNotification:${result.status}---> ${result.message} ")

                },
                { error ->
                    Log.d("TAG", "sendNotification:${error.message} ")
                }
            )
    }

    fun dispose() {
        disposable?.dispose()
    }

    override fun onMessageAdded(massage: Message?) {
        mymsg.set("")
        chatMsgList.add(
            ChatMessage(
                massage?.sid,
                massage?.messageBody,
                massage?.author,
                massage?.dateCreated,
                false
            )
        )
        activity.binding?.contentChatMessage?.rvChatMessageList?.scrollToPosition(chatMsgList.size - 1)
        activity.adapter?.notifyDataSetChanged()
    }

    override fun onMessageSend() {
        mymsg.set("")
    }

    override fun onLoadMessage(list: List<ChatMessage>) {
        chatMsgList.addAll(list)
        activity.binding?.contentChatMessage?.rvChatMessageList?.scrollToPosition(chatMsgList.size - 1)
        activity.adapter?.notifyDataSetChanged()
        activity.progressDialog?.dismiss()
        activity.clearNotification()
    }

    override fun onError(error: String) {
        activity.showToast(error)
    }

    override fun getChannelSid(channelSid: String) {
        saveChannelSid(channelSid)
    }

    override fun onTokenExpired() {
        fetch()
    }

    override fun onClientSynchronization() {
        if (chatSid == "")
            twilioChatManager.createChannel(chatCode)
        else
            twilioChatManager.loadChannels(chatSid)
    }

    override fun showLoader() {
        if (chatMsgList?.size == 0)
            activity.progressDialog?.show()
    }

    override fun hideLoader() {
        activity.progressDialog?.dismiss()
    }

    override fun createChannel() {
        twilioChatManager.createChannel(chatCode)
    }

    override fun allMessageSeen() {
        chatMsgList?.forEach {
            it.isSeen = true
        }
        activity.adapter?.notifyDataSetChanged()
        activity.binding?.contentChatMessage?.rvChatMessageList?.scrollToPosition(chatMsgList.size - 1)
    }

    override fun sendNotification(message: String?) {
        Log.d("TAG", "sendNotification: $message")
        sendUserNotification(message)
    }

    fun sendChatMessage(msg: String) {
        twilioChatManager.sendChatMessage(msg)
    }

    fun onDestroy() {
        twilioChatManager.destroy()
    }
    fun onResume(){
        twilioChatManager.onResume()
    }
    fun onPause(){
        twilioChatManager.onPause()
    }

}