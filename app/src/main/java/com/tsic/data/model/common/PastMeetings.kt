package com.tsic.data.model.common

import com.google.gson.annotations.SerializedName

data class PastMeetings(
    @SerializedName("id")
    val id: Int?,
    @SerializedName("title")
    val title: String?,
    @SerializedName("description")
    val description: String?,
    @SerializedName("school_id")
    val school_id: Int?,
    @SerializedName("school_type")
    val school_type: String?,
    @SerializedName("school_location")
    val school_location: String?,
    @SerializedName("session_method_location_id")
    val session_method_location_id: Int?,
    @SerializedName("agency_id")
    val agency_id: Int?,
    @SerializedName("created_by")
    val created_by: Int,
    @SerializedName("created_by_type")
    val created_by_type: String?,
    @SerializedName("schedule_time")
    val schedule_time: String?,
    @SerializedName("address")
    val address: String?,
    @SerializedName("latitude")
    val latitude: String?,
    @SerializedName("longitude")
    val longitude: String?,
    @SerializedName("is_logged")
    val is_logged: Int?,
    @SerializedName("created_date")
    val created_date: String?,
    @SerializedName("mentor_id")
    val mentor_id: Int?,
    @SerializedName("mentee_id")
    val mentee_id: Int?,
    @SerializedName("created_from")
    val created_from: String?,
    @SerializedName("status")
    val status: Int?,
    @SerializedName("method_value")
    val method_value: String?,
    @SerializedName("firstname")
    val firstname: String?,
    @SerializedName("lastname")
    val lastname: String?,
)
