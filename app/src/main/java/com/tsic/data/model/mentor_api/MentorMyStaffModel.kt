package com.tsic.data.model.mentor_api


import com.google.gson.annotations.SerializedName

data class MentorMyStaffModel(
    @SerializedName("address")
    val address: String? = "",
    @SerializedName("email")
    val email: String? = "", // cfisher@tsic.org
    @SerializedName("id")
    val id: Int? = 0, // 77
    @SerializedName("name")
    val name: String? = "", // Casey Fisher
    @SerializedName("unread_chat_count")
    val unreadChat: Int? = 0,
    @SerializedName("profile_pic")
    val profilePic: String? = "",
    @SerializedName("channel_sid")
    val channelSid: String? = "",
    @SerializedName("code")
    val code: String? = "",
    @SerializedName("timezone")
    val timezone: String? = "" // America/New_York
)