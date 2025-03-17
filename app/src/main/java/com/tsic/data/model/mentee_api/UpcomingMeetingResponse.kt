package com.tsic.data.model.mentee_api


import com.google.gson.annotations.SerializedName

data class UpcomingMeeting(
    @SerializedName("meeting")
    var listMenteeMeeting: List<UpcomingMeetingResponse>?
)

data class UpcomingMeetingResponse(
    @SerializedName("address")
    val address: String? = "", // Sarasota Springs, FL, USA
    @SerializedName("school_name")
    val school_name: String? = "",
    @SerializedName("agency_id")
    val agencyId: Int? = 0, // 61
    @SerializedName("created_by")
    val createdBy: Int? = 0, // 8
    @SerializedName("created_by_type")
    val createdByType: String? = "", // mentor
    @SerializedName("created_date")
    val createdDate: String? = "", // 2019-09-02 07:34:53
    @SerializedName("creator_name")
    val creatorName: String? = "", // Mentor  One
    @SerializedName("date")
    val date: String? = "", // 09-10-2019
    @SerializedName("description")
    val description: String? = "", // Aqua Two Meeting
    @SerializedName("method_value")
    val methodValue: String? = "",
    @SerializedName("id")
    val id: Int? = 0, // 7
    @SerializedName("is_mentor_created")
    val isMentorCreated: Boolean? = false, // true
    @SerializedName("is_request_sent")
    val isRequestSent: Boolean? = false, // true
    @SerializedName("is_web_status_done")
    val isWebStatusDone: Int? = 0, // 0
    @SerializedName("latitude")
    val latitude: String? = "", // 27.30894
    @SerializedName("longitude")
    val longitude: String? = "", // -82.47954
    @SerializedName("schedule_time")
    val scheduleTime: String? = "", // 2019-09-10 12:55:25
    @SerializedName("status")
    val status: Int? = 0, // 1
    @SerializedName("time")
    val time: String? = "", // 12:55:25
    @SerializedName("title")
    val title: String? = "", // Aqua Two Meeting
    @SerializedName("type")
    val type: String? = "", // mentee
    @SerializedName("user_id")
    val userId: Int? = 0, // 1
    @SerializedName("web_status")
    val webStatus: Int? = 0, // 1
    @SerializedName("web_status_date")
    val webStatusDate: String? = "" // 0000-00-00 00:00:00
)