package com.tsic.data.model.mentor_api


import com.google.gson.annotations.SerializedName

data class MenteeAssignedList(
    @SerializedName("assign_id")
    val assignId: Int? = 0, // 22
    @SerializedName("assign_status")
    val assignStatus: Int? = 0, // 0
    @SerializedName("email")
    val email: String? = "", // chris.mentee.one@test.com
    @SerializedName("firstname")
    val firstname: String? = "", // Chris Mentee One
    @SerializedName("id")
    val id: Int? = 0, // 4
    @SerializedName("image")
    val image: String? = "", // 15669104251940183425d6527d97602d.jpg
    @SerializedName("lastname")
    val lastname: String? = "", // Test
    @SerializedName("middlename")
    val middlename: String? = ""
)