package com.tsic.data.model.mentor_api


import android.os.Parcelable
import com.google.gson.annotations.SerializedName
import kotlinx.android.parcel.Parcelize

@Parcelize
data class MentorPastMeeting(
    @SerializedName("address")
    val address: String? = "", // Sarasota Springs, FL, USA
    @SerializedName("agency_id")
    val agencyId: Int? = 0,
    @SerializedName("status")
    val status: Int? = 0, // 0 pending, 1 accepted, 2 , 3 Cancelled
    @SerializedName("created_by")
    val createdBy: String? = "", // 10
    @SerializedName("created_by_type")
    val createdByType: String? = "", // mentor
    @SerializedName("created_date")
    val createdDate: String? = "", // 2019-09-02 06:13:55
    @SerializedName("date")
    val date: String? = "", // 08-25-2019
    @SerializedName("description")
    val description: String? = "", // Yup
    @SerializedName("id")
    val id: Int? = 0, // 5
    @SerializedName("is_mentor_created")
    val isMentorCreated: Boolean? = false, // true
    @SerializedName("is_logged")
    val isLogged: String? = "",
    @SerializedName("latitude")
    val latitude: String? = "", // 27.30894
    @SerializedName("longitude")
    val longitude: String? = "", // -82.47954
    @SerializedName("note")
    val meetingRequests: String? = "",
    @SerializedName("method_value")
    val methodValue: String? = "",
    @SerializedName("mentees")
    val mentees: List<MenteeeMeeting?>? = listOf(),
    @SerializedName("schedule_time")
    val scheduleTime: String? = "", // 2019-08-25 10:55:25
    @SerializedName("time")
    val time: String? = "", // 10:55:25
    @SerializedName("title")
    val title: String? = "",
    @SerializedName("school_location")
    val school_location: String? = "",// Yup
    @SerializedName("session_method_location_id")
    val sessionMethodLocationId: String? = "",// Yup
    @SerializedName("school_name")
    val schoolName: String? = "",// Yup
    @SerializedName("school_type")
    val school_type: String? = "" // Yup

) : Parcelable

@Parcelize
data class MenteeeMeeting(
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
) : Parcelable
