package com.tsic.data.model.mentee_api

import com.google.gson.annotations.SerializedName

data class ChatMessageDetailsModel(
    @SerializedName("agency_id") val agency_id: String
)

data class ChatMessageListResponse(
    @SerializedName("agency_id")
    var agencyId: String = "",
    @SerializedName("chat_code")
    var chatCode: String = "",
    @SerializedName("data")
    var chatMsgList: List<ChatMsg> = listOf(),
    @SerializedName("status")
    var status: Boolean = false,
    @SerializedName("token")
    var token: String = ""
)


data class ChatCodeResponse(
    @SerializedName("chat_code")
    var chatCode: String = "",
    @SerializedName("status")
    var status: Boolean = false,
    @SerializedName("token")
    var token: String = ""
)


