package com.tsic.data.model.mentor_api


import com.google.gson.annotations.SerializedName

data class MentorReport(
    @SerializedName("created_date")
    val createdDate: String? = "", // 2019-09-10 10:18:46
    @SerializedName("id")
    val id: Int? = 0, // 5
    @SerializedName("image")
    val image: String? = "", // 156811072619850162235d7778867e62e.jpeg
    @SerializedName("mentee_id")
    val menteeId: Int? = 0, // 1
    @SerializedName("name")
    val name: String? = "", // last test
    @SerializedName("status")
    val status: Int? = 0 // 1
)