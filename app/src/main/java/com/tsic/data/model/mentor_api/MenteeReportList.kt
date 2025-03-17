package com.tsic.data.model.mentor_api


import com.google.gson.annotations.SerializedName

data class MenteeReportList(
    @SerializedName("created_date")
    val created_date: String? = "", // 2019-08-23 08:55:38
    @SerializedName("id")
    val id: Int? = 0, // 1
    @SerializedName("image")
    val image: String? = "", // 156655053813897956695d5faa0a6c547.jpeg
    @SerializedName("mentee_id")
    val menteeId: Int? = 0, // 8
    @SerializedName("name")
    val name: String? = "", // Report One
    @SerializedName("status")
    val status: Int? = 0 // 1
)