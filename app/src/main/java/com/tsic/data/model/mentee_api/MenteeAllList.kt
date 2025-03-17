package com.tsic.data.model.mentee_api


import com.google.gson.annotations.SerializedName

data class MenteeAllList(
    @SerializedName("data")
    val `data`: Data?,
    @SerializedName("message")
    val message: String?,
    @SerializedName("status")
    val status: Boolean?
) {
    data class Data(
        @SerializedName("requested")
        val requested: List<Requested?>?,
        @SerializedName("upcoming")
        val upcoming: List<Upcoming?>?
    ) {
        data class Requested(
            @SerializedName("address")
            val address: String?,
            @SerializedName("agency_id")
            val agencyId: Int?,
            @SerializedName("created_by")
            val createdBy: Int?,
            @SerializedName("created_by_type")
            val createdByType: String?,
            @SerializedName("created_date")
            val createdDate: String?,
            @SerializedName("creator_name")
            val creatorName: String?,
            @SerializedName("date")
            val date: String?,
            @SerializedName("description")
            val description: String?,
            @SerializedName("id")
            val id: Int?,
            @SerializedName("is_mentor_created")
            val isMentorCreated: Boolean?,
            @SerializedName("is_request_sent")
            val isRequestSent: Boolean?,
            @SerializedName("is_web_status_done")
            val isWebStatusDone: Int?,
            @SerializedName("latitude")
            val latitude: String?,
            @SerializedName("longitude")
            val longitude: String?,
            @SerializedName("meeting_users_status")
            val meetingUsersStatus: Int?,
            @SerializedName("method_value")
            val methodValue: String?,
            @SerializedName("schedule_time")
            val scheduleTime: String?,
            @SerializedName("school_id")
            val schoolId: Int?,
            @SerializedName("school_location")
            val schoolLocation: String?,
            @SerializedName("school_name")
            val schoolName: String?,
            @SerializedName("school_type")
            val schoolType: String?,
            @SerializedName("session_method_location_id")
            val sessionMethodLocationId: Int?,
            @SerializedName("status")
            val status: Int?,
            @SerializedName("time")
            val time: String?,
            @SerializedName("title")
            val title: String?,
            @SerializedName("type")
            val type: String?,
            @SerializedName("user_id")
            val userId: Int?,
            @SerializedName("web_status")
            val webStatus: Int?,
            @SerializedName("web_status_date")
            val webStatusDate: String?
        )

        data class Upcoming(
            @SerializedName("address")
            val address: String?,
            @SerializedName("agency_id")
            val agencyId: Int?,
            @SerializedName("created_by")
            val createdBy: Int?,
            @SerializedName("created_by_type")
            val createdByType: String?,
            @SerializedName("created_date")
            val createdDate: String?,
            @SerializedName("creator_name")
            val creatorName: String?,
            @SerializedName("date")
            val date: String?,
            @SerializedName("description")
            val description: String?,
            @SerializedName("id")
            val id: Int?,
            @SerializedName("is_mentor_created")
            val isMentorCreated: Boolean?,
            @SerializedName("is_request_sent")
            val isRequestSent: Boolean?,
            @SerializedName("is_web_status_done")
            val isWebStatusDone: Int?,
            @SerializedName("latitude")
            val latitude: String?,
            @SerializedName("longitude")
            val longitude: String?,
            @SerializedName("method_value")
            val methodValue: String?,
            @SerializedName("schedule_time")
            val scheduleTime: String?,
            @SerializedName("school_id")
            val schoolId: Int?,
            @SerializedName("school_location")
            val schoolLocation: String?,
            @SerializedName("school_name")
            val schoolName: String?,
            @SerializedName("school_type")
            val schoolType: String?,
            @SerializedName("session_method_location_id")
            val sessionMethodLocationId: Int?,
            @SerializedName("status")
            val status: Int?,
            @SerializedName("time")
            val time: String?,
            @SerializedName("title")
            val title: String?,
            @SerializedName("type")
            val type: String?,
            @SerializedName("user_id")
            val userId: Int?,
            @SerializedName("web_status")
            val webStatus: Int?,
            @SerializedName("web_status_date")
            val webStatusDate: String?,
            @SerializedName("mentor_id")
            val mentor_id: Int?
        )
    }
}