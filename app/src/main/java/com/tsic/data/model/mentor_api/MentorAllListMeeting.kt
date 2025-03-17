package com.tsic.data.model.mentor_api


import com.google.gson.annotations.SerializedName

data class MentorAllListMeeting(
    @SerializedName("data")
    val `data`: Data?,
    @SerializedName("message")
    val message: String?,
    @SerializedName("status")
    val status: Boolean?
){
    data class Data(
        @SerializedName("past")
        val past: List<Past?>?,
        @SerializedName("requested")
        val requested: List<MentorPastMeeting?>?,
        @SerializedName("upcoming")
        val upcoming: List<Upcoming?>?
    ) {
        data class Past(
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
            @SerializedName("date")
            val date: String?,
            @SerializedName("description")
            val description: String?,
            @SerializedName("firstname")
            val firstname: String?,
            @SerializedName("id")
            val id: Int?,
            @SerializedName("is_mentor_created")
            val isMentorCreated: Boolean?,
            @SerializedName("lastname")
            val lastname: String?,
            @SerializedName("latitude")
            val latitude: String?,
            @SerializedName("longitude")
            val longitude: String?,
            @SerializedName("mentee_id")
            val menteeId: String?,
            @SerializedName("is_logged")
            val isLogged: String?,
            @SerializedName("mentees")
            val mentees: List<Mentee?>?,
            @SerializedName("method_value")
            val methodValue: String?,
            @SerializedName("middlename")
            val middlename: String?,
            @SerializedName("note")
            val note: String?,
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
            val title: String?
        ) {
            data class Mentee(
                @SerializedName("firstname")
                val firstname: String?,
                @SerializedName("id")
                val id: Int?,
                @SerializedName("image")
                val image: String?,
                @SerializedName("lastname")
                val lastname: String?,
                @SerializedName("middlename")
                val middlename: String?
            )
        }

        data class Requested(
            @SerializedName("address")
            val address: String?,
            @SerializedName("agency_id")
            val agencyId: Int?,
            @SerializedName("created_by")
            val createdBy: String?,
            @SerializedName("created_by_type")
            val createdByType: String?,
            @SerializedName("created_date")
            val createdDate: String?,
            @SerializedName("date")
            val date: String?,
            @SerializedName("description")
            val description: String?,
            @SerializedName("firstname")
            val firstname: String?,
            @SerializedName("id")
            val id: Int?,
            @SerializedName("is_mentor_created")
            val isMentorCreated: Boolean?,
            @SerializedName("lastname")
            val lastname: String?,
            @SerializedName("latitude")
            val latitude: String?,
            @SerializedName("longitude")
            val longitude: String?,
            @SerializedName("mentee_id")
            val menteeId: String?,
            @SerializedName("mentees")
            val mentees: List<Mentee?>?,
            @SerializedName("method_value")
            val methodValue: String?,
            @SerializedName("middlename")
            val middlename: String?,
            @SerializedName("note")
            val note: String?,
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
            val sessionMethodLocationId: String?,
            @SerializedName("status")
            val status: Int?,
            @SerializedName("time")
            val time: String?,
            @SerializedName("title")
            val title: String?
        ) {
            data class Mentee(
                @SerializedName("firstname")
                val firstname: String?,
                @SerializedName("id")
                val id: Int?,
                @SerializedName("image")
                val image: String?,
                @SerializedName("lastname")
                val lastname: String?,
                @SerializedName("middlename")
                val middlename: String?
            )
        }

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
            @SerializedName("date")
            val date: String?,
            @SerializedName("description")
            val description: String?,
            @SerializedName("firstname")
            val firstname: String?,
            @SerializedName("id")
            val id: Int?,
            @SerializedName("is_mentor_created")
            val isMentorCreated: Boolean?,
            @SerializedName("lastname")
            val lastname: String?,
            @SerializedName("latitude")
            val latitude: String?,
            @SerializedName("longitude")
            val longitude: String?,
            @SerializedName("mentee_id")
            val menteeId: String?,
            @SerializedName("mentees")
            val mentees: List<Mentee?>?,
            @SerializedName("method_value")
            val methodValue: String?,
            @SerializedName("middlename")
            val middlename: String?,
            @SerializedName("note")
            val note: String?,
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
            val title: String?
        ) {
            data class Mentee(
                @SerializedName("firstname")
                val firstname: String?,
                @SerializedName("id")
                val id: Int?,
                @SerializedName("image")
                val image: String?,
                @SerializedName("lastname")
                val lastname: String?,
                @SerializedName("middlename")
                val middlename: String?
            )
        }
    }
}