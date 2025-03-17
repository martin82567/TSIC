package com.tsic.data.model.mentee_api


import com.google.gson.annotations.SerializedName

data class MeetingListResponse(
    @SerializedName("meeting")
    var meetingList: List<RequestedMenteeMeeting> = listOf()
)

data class RequestedMenteeMeeting(
    @SerializedName("address")
    val address: String? = "", // Sarasota, FL, USA
    @SerializedName("agency_id")
    val agencyId: Int? = 0, // 61
    @SerializedName("created_by")
    val createdBy: Int? = 0, // 0
    @SerializedName("created_by_type")
    val createdByType: String? = "",
    @SerializedName("created_date")
    val createdDate: String? = "", // 2019-09-03 06:45:17
    @SerializedName("creator_name")
    val creatorName: String? = "", // Take Stock in Children Sarasota
    @SerializedName("date")
    val date: String? = "", // 10-30-2019
    @SerializedName("description")
    val description: String? = "", // Test Web Meeting
    @SerializedName("id")
    val id: Int? = 0, // 9
    @SerializedName("method_value")
    val methodValue: String? = "",
    @SerializedName("is_mentor_created")
    val isMentorCreated: Boolean? = false, // false
    @SerializedName("is_request_sent")
    val isRequestSent: Boolean? = false, // true
    @SerializedName("is_web_status_done")
    val isWebStatusDone: Int? = 0, // 1
    @SerializedName("latitude")
    val latitude: String? = "", // 27.33643
    @SerializedName("longitude")
    val longitude: String? = "", // -82.53065
    @SerializedName("schedule_time")
    val scheduleTime: String? = "", // 2019-10-30 12:14:59
    @SerializedName("status")
    val status: Int? = 0, // 0
    @SerializedName("time")
    val time: String? = "", // 12:14:59
    @SerializedName("title")
    val title: String? = "", // Test Web Meeting
    @SerializedName("type")
    val type: String? = "", // mentee
    @SerializedName("user_id")
    val userId: Int? = 0, // 1
    @SerializedName("school_name")
    val school_name: String? = "", // New York School
    @SerializedName("web_status")
    val webStatus: Int? = 0, // 1
    @SerializedName("web_status_date")
    val webStatusDate: String? = "" // 2019-09-03 10:02:25
)