package com.tsic.data.model.mentor_api


import com.google.gson.annotations.SerializedName

data class AssignedMenteeList(
    @SerializedName("mentee_list")
    val menteeList: List<Mentee?> = emptyList()
)

data class Mentee(
    @SerializedName("assign_id")
    val assignId: Int? = 0, // 23
    @SerializedName("assign_status")
    val assignStatus: Int? = 0, // 0
    @SerializedName("email")
    val email: String? = "", // chris.mentee.two@test.com
    @SerializedName("firstname")
    val firstname: String? = "", // Chris Mentee Two
    @SerializedName("id")
    val id: Int? = 0, // 5
    @SerializedName("image")
    val image: String? = "",
    @SerializedName("lastname")
    val lastname: String? = "", // Test
    @SerializedName("middlename")
    val middlename: String? = ""
)
