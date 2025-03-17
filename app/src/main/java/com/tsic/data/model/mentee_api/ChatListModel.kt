package com.tsic.data.model.mentee_api

import com.google.gson.annotations.SerializedName


data class ChatResponse(
    @SerializedName("status") var status: Boolean = false,
    @SerializedName("message") var message: String = "",
    @SerializedName("data") var data: ChatData? = null

)

data class ChatData(
    @SerializedName("chat_code")
    var chatCode: String = "",
    @SerializedName("timezone")
    var timezone: String = "",
    @SerializedName("timezone_offset")
    var timezone_offset: String = "",
    @SerializedName("firebase_id")
    var firebaseId: String = "",
    @SerializedName("device_type")
    var deviceType: String = "",
    @SerializedName("count_message")
    var count_message: Int? = 0,
    @SerializedName("threads")
    var chatList: List<ChatMsg> = listOf()
)

data class ChatMsg(
    @SerializedName("id") var id: Int = 0,
    @SerializedName("chat_code") var chatCode: String = "",
    @SerializedName("receiver_id") var receiverId: Int = 0,
    @SerializedName("sender_id") var senderId: Int = 0,
    @SerializedName("message") var message: String = "No msg",
    @SerializedName("from_where") var fromWhere: String = "",
    @SerializedName("receiver_is_read") var isRead: String = "0",
    @SerializedName("pic") var chatterServerPic: String = "",
    @SerializedName("created_date") var createdDate: String = ""
)

data class ZowodResponse(
    @SerializedName("status") var status: Boolean = false,
    @SerializedName("message") var message: String = "",
    @SerializedName("messages") var data: List<ZowodMsg>? = listOf()
)

data class ZowodMsg(
    @SerializedName("message") val message: String,
    @SerializedName("from_where") val from_where: String,
    @SerializedName("seller_id") val seller_id: Int,
    @SerializedName("buyer_id") val buyer_id: Int,
    @SerializedName("product_id") val product_id: Int,
    @SerializedName("is_read_to_id") val is_read_to_id: Int,
    @SerializedName("latitude") val latitude: String,
    @SerializedName("longitude") val longitude: String,
    @SerializedName("share_location") val share_location: Int,
    @SerializedName("created_at") val created_at: String,
    @SerializedName("sender_userid") val sender_userid: Int,
    @SerializedName("created_time") val created_time: String

)