package com.tsic.data.model.mentor_api

import com.google.gson.annotations.SerializedName


/*
data class SessionListResponse(
    @SerializedName("status")
    @SerializedName("msg")
    @SerializedName("data")
    val listSession: List<SessionResponse?>? = listOf()
)
*/


data class SessionResponse(

    @SerializedName("created_date")
    val createdDate: String? = "", // 2019-08-26 07:28:34
    @SerializedName("date")
    val date: String? = "", // 08-29-2019
    @SerializedName("id")
    val id: Int? = 0, // 1
    @SerializedName("mentee_id")
    val menteeId: Int? = 0, // 1
    @SerializedName("mentor_id")
    val mentorId: Int? = 0, // 8
    @SerializedName("name")
    val name: String? = "", // test session
    @SerializedName("address")
    val address: String? = "", // USA
    @SerializedName("firstname")
    val firstname: String? = "", // 8
    @SerializedName("middlename")
    val middlename: String? = "", // test session
    @SerializedName("lastname")
    val lastname: String? = "",
    @SerializedName("session_method_location_id")
    var session_method_location_id: String? = "",
    @SerializedName("method_value")
    val method_value: String? = "",
    @SerializedName("type")
    var type: String? = "",
    @SerializedName("time_duration")
    val timeDuration: String? = "",// 30
    @SerializedName("status")
    val status: Int? = 0, // 1
    @SerializedName("time_from")
    val timeFrom: String? = "", // 9AM
    @SerializedName("time_to")
    val timeTo: String? = "", // 3PM
    @SerializedName("no_show")
    val noShow: Int?,
    @SerializedName("schedule_date")
    val scheduleDate: String? = "" // DD/MM/YYYY,
)

data class DeleteSessionModel(
    @SerializedName("meeting_id")
    val meeting_id : String
)