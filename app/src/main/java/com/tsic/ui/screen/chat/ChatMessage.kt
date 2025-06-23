package com.tsic.ui.screen.chat

data class ChatModel(
    val status: Boolean?,
    val message: String?,
    val data: ChatData?,
)

data class ChatData(
    val chats: List<ChatMessage>?,
)

data class ChatMessage(
    val sid: String?="",
    val message: String?="",
    val author: String?="",
    val date: String?="",
    var isSeen:Boolean=true
)
