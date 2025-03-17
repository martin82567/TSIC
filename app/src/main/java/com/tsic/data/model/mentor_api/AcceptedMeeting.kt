package com.tsic.data.model.mentor_api


import com.google.gson.annotations.SerializedName


data class AcceptedMeeting(
    @SerializedName("address")
    val address: String? = "", // 240 S Pineapple Ave Sarasota FL 34236 United States
    @SerializedName("agency_id")
    val agencyId: Int? = 0, // 61
    @SerializedName("created_by")
    val createdBy: Int? = 0, // 8
    @SerializedName("created_by_type")
    val createdByType: String? = "", // mentor
    @SerializedName("created_date")
    val createdDate: String? = "", // 2019-09-12 07:14:54
    @SerializedName("date")
    val date: String? = "", // 11-18-2019
    @SerializedName("description")
    val description: String? = "", // Test description new added
    @SerializedName("school_location")
    val schoolLocation: String? = "",
    @SerializedName("school_name")
    val schoolName: String? = "",
    @SerializedName("id")
    val id: Int? = 0, // 8
    @SerializedName("method_value")
    val methodValue: String? = "",
    @SerializedName("is_mentor_created")
    val isMentorCreated: Boolean? = false, // true
    @SerializedName("latitude")
    val latitude: String? = "", // 27.334338953804973
    @SerializedName("longitude")
    val longitude: String? = "", // -82.54082028514705
    @SerializedName("mentees")
    val mentees: List<Mentee?>? = listOf(),
    @SerializedName("schedule_time")
    val scheduleTime: String? = "", // 2019-11-18 13:47:33
    @SerializedName("status")
    val status: Int? = 0, // 1
    @SerializedName("time")
    val time: String? = "", // 13:47:33
    @SerializedName("title")
    val title: String? = "" // meeting test 1
) {
    data class Mentee(
        @SerializedName("firstname")
        val firstname: String? = "", // Mentee
        @SerializedName("id")
        val id: Int? = 0, // 1
        @SerializedName("image")
        val image: String? = "", // 156818315120420423835d78936fcb836.jpg
        @SerializedName("lastname")
        val lastname: String? = "", // L
        @SerializedName("middlename")
        val middlename: String? = ""
    )
}