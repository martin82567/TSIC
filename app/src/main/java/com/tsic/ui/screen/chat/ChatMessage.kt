package com.tsic.ui.screen.chat

data class ChatMessage(
    val sid: String?="",
    val message: String?="",
    val author: String?="",
    val date: String?="",
    var isSeen:Boolean=true
)
