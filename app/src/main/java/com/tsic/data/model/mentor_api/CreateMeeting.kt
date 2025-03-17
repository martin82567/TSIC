package com.tsic.data.model.mentor_api


import com.google.gson.annotations.SerializedName

data class CreateMeeting(
    @SerializedName("meeting")
    val meeting: Meeting? = Meeting()
) {
    data class Meeting(
        @SerializedName("address")
        val address: String? = "", // Sarasota Springs, FL, USA
        @SerializedName("agency_id")
        val agencyId: Int? = 0, // 61
        @SerializedName("created_by")
        val createdBy: Int? = 0, // 10
        @SerializedName("created_by_type")
        val createdByType: String? = "", // mentor
        @SerializedName("created_date")
        val createdDate: String? = "", // 2019-08-28 12:39:32
        @SerializedName("date")
        val date: String? = "", // 10-31-2025
        @SerializedName("description")
        val description: String? = "", // gfgfdgfdgfdgg
        @SerializedName("id")
        val id: Int? = 0, // 4
        @SerializedName("latitude")
        val latitude: String? = "", // 27.30894
        @SerializedName("longitude")
        val longitude: String? = "", // -82.47954
        @SerializedName("mentee_id")
        val menteeId: Int? = 0, // 5
        @SerializedName("schedule_time")
        val scheduleTime: String? = "", // 2025-10-31 05:22:52
        @SerializedName("school_id")
        val schoolId: Int? = 0, // 2
        @SerializedName("school_location")
        val schoolLocation: String? = "", // Sarasota
        @SerializedName("status")
        val status: Int? = 0, // 1
        @SerializedName("time")
        val time: String? = "", // 05:22:52
        @SerializedName("title")
        val title: String? = "" // fdgfdgfdg
    )
}