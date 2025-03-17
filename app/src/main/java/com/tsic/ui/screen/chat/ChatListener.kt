package com.tsic.ui.screen.chat

import com.twilio.chat.Message

interface ChatListener {
    fun onMessageAdded(massage: Message?)
    fun onMessageSend()
    fun onLoadMessage(list: List<ChatMessage>)
    fun onError(error: String)
    fun getChannelSid(channelSid: String)
    fun onTokenExpired()
    fun onClientSynchronization()
    fun showLoader()
    fun hideLoader()
    fun createChannel()
    fun allMessageSeen()
//    fun userOnline()
//    fun userOffline(message: String?)
    fun sendNotification(message: String?)
}