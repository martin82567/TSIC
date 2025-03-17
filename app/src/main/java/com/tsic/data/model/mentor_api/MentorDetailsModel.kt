package com.tsic.data.model.mentor_api

import android.os.Parcelable
import com.google.gson.annotations.SerializedName
import com.tsic.data.model.common.PastMeetings
import com.tsic.data.model.common.UpcominMeetings
import kotlinx.android.parcel.Parcelize


data class MentorLoginResponseDetails(
    @SerializedName("token") var token: String? = "",
    @SerializedName("user_details") var userDetails: MentorLoginDetails? = MentorLoginDetails()
)

data class MentorLoginDetails(
    @SerializedName("id") var id: Int? = 0,
    @SerializedName("firstname") var firstname: String? = "",
    @SerializedName("middlename") var middlename: String? = "",
    @SerializedName("lastname") var lastname: String? = "",
    @SerializedName("email") var email: String? = "",
    @SerializedName("address") var address: String? = "",
    @SerializedName("latitude") var latitude: String? = "",
    @SerializedName("longitude") var longitude: String? = "",
    @SerializedName("firebase_id") var firebaseId: String? = "",
    @SerializedName("device_type") var deviceType: String? = "",
    @SerializedName("profile_pic") var image: String? = "",
    @SerializedName("phone") var phone: String? = "",
    @SerializedName("agency_name") var linkedAgencyName: String? = "",
    @SerializedName("linked_agency_image") var linkedAgencyImage: String? = ""
)

data class MentorMyProfile(
    val status: Boolean,
    val message: String?,
    val data: MentorMyProfileDetails,
    val error: Throwable?
)

data class MentorMyProfileDetails(
    @SerializedName("id") var id: Int? = 0,
    @SerializedName("firstname") var firstname: String? = "",
    @SerializedName("middlename") var middlename: String? = "",
    @SerializedName("lastname") var lastname: String? = "",
    @SerializedName("email") var email: String? = "",
    @SerializedName("address") var address: String? = "",
    @SerializedName("latitude") var latitude: String? = "",
    @SerializedName("longitude") var longitude: String? = "",
    @SerializedName("firebase_id") var firebaseId: String? = "",
    @SerializedName("device_type") var deviceType: String? = "",
    @SerializedName("profile_pic") var image: String? = "",
    @SerializedName("phone") var phone: String? = "",
    @SerializedName("agency_name") var linkedAgencyName: String? = "",
    @SerializedName("mentor_mentee_chat_count") var mentor_mentee_chat_count: String? = "0",
    @SerializedName("session_log_count") var session_log_count: String? = "0",
    @SerializedName("mentor_staff_chat_count") var mentor_staff_chat_count: Int? = 0,
    @SerializedName("message_center_count") var message_center_count: Int? = 0,
    @SerializedName("schedule_session_count") var schedule_session_count: String? = "0",
    @SerializedName("session_log_label_no") var sessionLogLabelNo: String? = "",
    @SerializedName("linked_agency_image") var linkedAgencyImage: String? = "",
    @SerializedName("affiliate_system_messaging") var affiliateSystemMessaging: List<AffiliateSystemMessaging>,
    @SerializedName("past_meeting") var past_meeting: PastMeetings?,
    @SerializedName("upcoming_meeting") var upcoming_meeting: UpcominMeetings?
    )

data class AffiliateSystemMessaging(
    @SerializedName("id") var id: Int? = 0,
    @SerializedName("start_datetime") var startDateTime: String? = "",
    @SerializedName("message") var message: String? = "",
    @SerializedName("end_datetime") var endDateTime: String? = "",
    @SerializedName("app_id") var app_id: String? = "",
)

@Parcelize
data class MentorMyMenteeModel(
    @SerializedName("age")
    val age: String? = "", // 40
    @SerializedName("current_living_details")
    val currentLivingDetails: String? = "", // Sarasota, FL, USA
    @SerializedName("dob")
    val dob: String? = "", // 1979-04-30
    @SerializedName("email")
    val email: String? = "", // aquatechios@gmail.com
    @SerializedName("firstname")
    val firstname: String? = "", // Mentee
    @SerializedName("gender")
    val gender: String? = "", // Male
    @SerializedName("id")
    val id: Int? = 0, // 1
    @SerializedName("image")
    val image: String? = "", // 156524332316636871715d4bb7bbabe95.png
    @SerializedName("lastname")
    val lastname: String? = "", // One
    @SerializedName("middlename")
    val middlename: String? = "", // two
    @SerializedName("timezone")
    val timezone: String? = "", // America/New_York
    @SerializedName("cell_phone_number")
    val cell_phone_number: String? = "",
    @SerializedName("firebase_id")
    val firebaseId: String? = "",
    @SerializedName("last_session_date")
    val last_session_date: String? = "",
    @SerializedName("school_name")
    val schoolName: String? = "",
    @SerializedName("school_id")
    val schoolId: String? = "",
    @SerializedName("upcoming_meeting_date")
    val upcomingMeetingDate: String? = "NA",
    @SerializedName("unread_chat_count")
    val unread_chat_count: String? = "0",
    @SerializedName("channel_sid")
    val channelSid: String? = "",
    @SerializedName("code")
    val code: String? = ""

) : Parcelable


