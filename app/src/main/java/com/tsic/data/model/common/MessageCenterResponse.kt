package com.tsic.data.model.common


import com.google.gson.annotations.SerializedName

data class MessageCenterResponse(
    @SerializedName("count_messages")
    val countMessages: Int,
    @SerializedName("messages")
    val messages: List<Message>
) {
    data class Message(
        @SerializedName("created_at")
        val createdAt: String? = "",
        @SerializedName("id")
        val id: Int? = 0,
        @SerializedName("message")
        val message: String? = "",
        @SerializedName("created_by")
        val createdBy: String? = "1"
    )
}