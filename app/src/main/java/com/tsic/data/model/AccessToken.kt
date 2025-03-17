package com.tsic.data.model

data class AccessToken(
    val room_sid: String = "",
    val unique_name: String = "",
    val sender_accesstoken: String? = null,
    val receiver_accesstoken: String? = null,
    val created_at: String = "",
    val time: Long? = 0
)

data class InitVideoChat(
    val chat_code: String = "",
    val unique_name: String = "",
    val remaining_time: String = "0"
)
