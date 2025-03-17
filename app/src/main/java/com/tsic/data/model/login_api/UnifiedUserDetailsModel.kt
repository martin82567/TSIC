package com.tsic.data.model.login_api

import com.google.gson.annotations.SerializedName
import com.tsic.data.model.mentor_api.AffiliateSystemMessaging

data class UnifiedUserLoginModel(
    @SerializedName("email") val email: String?,
    @SerializedName("password") val password: String?,
    @SerializedName("mode") val loginMode: String?,
    @SerializedName("firebase_id") val firebaseToken: String?,
    @SerializedName("waiver_statement_id") val waiverStatementId: String?,
    @SerializedName("device_type") val deviceType: String?,
    @SerializedName("latitude") val latitude: String?,
    @SerializedName("longitude") val longitude: String?,
    @SerializedName("is_waiver_acknowledged") val is_waiver_acknowledged: Int?,
)

data class UnifiedUserDetailsModel(
    @SerializedName("id") var id: Int? = 0,
    @SerializedName("name") var name: String? = "",
    @SerializedName("email") var email: String? = "",
    @SerializedName("phone") var phone: String? = "",
    @SerializedName("device_type") var deviceType: String? = "",
    @SerializedName("latitude") var latitude: String? = "",
    @SerializedName("longitude") var longitude: String? = "",
    @SerializedName("firebase_token") var firebaseToken: String? = "",
    @SerializedName("image_user") var imageUser: String? = ""
)

data class UserCheckModel(
    @SerializedName("email") val email: String?
)


data class UserLoginWaiverModel(
    @SerializedName("email") val email: String?,

    )
/*
data class LoginResponseDetails(
    @SerializedName("data")
    var `data`: SessionResponse? = SessionResponse(),
    @SerializedName("message")
    var message: String? = "",
    @SerializedName("status")
    var status: Boolean? = false
)*/
data class UserCheckResponseDetails(
    @SerializedName("status") var status: String? = "",
    @SerializedName("message") var mesage: String? = "",
    @SerializedName("data") var userDetails: com.tsic.data.model.mentee_api.UserDetails?
)

data class UserResponseDetails(
    @SerializedName("token") var token: String? = "",
    @SerializedName("submited_tips_token") var submitedTipsToken: String? = "",
    @SerializedName("user_details") var userDetails: UserDetails?,
    @SerializedName("user_type") var userType: String?
)

data class UserResponseDetailsWaiver(
    @SerializedName("status") var status: String? = "",
    @SerializedName("message") var mesage: String? = "",
    @SerializedName("data") var userDetails: UserDetailsWaiver?
)
data class UserDetailsWaiver(
    @SerializedName("is_waiver_acknowledged") var is_waiver_acknowledged: Int? = 0,
)

data class UserDetails(
    @SerializedName("activation_code") var activationCode: Int? = 0,
    @SerializedName("created_at") var createdAt: Any? = Any(),
    @SerializedName("device_type") var deviceType: String? = "",
    @SerializedName("user_type") var userType: String? = "",
    @SerializedName("email") var email: String? = "",
    @SerializedName("firebase_id") var firebaseId: String? = "",
    @SerializedName("firstname") var firstname: String? = "",
    @SerializedName("id") var id: Int? = 0,
    @SerializedName("image") var image: String? = "",
    @SerializedName("is_new") var isNew: Int? = 0,
    @SerializedName("lastname") var lastname: String? = "",
    @SerializedName("latitude") var latitude: String? = "",
    @SerializedName("longitude") var longitude: String? = "",
    @SerializedName("middlename") var middlename: String? = "",
    @SerializedName("password") var password: String? = "",
    @SerializedName("status") var status: Int? = 0,
    @SerializedName("message_center_count") var message_center_count: Int? = 0,
    @SerializedName("updated_at") var updatedAt: Any? = Any(),
    @SerializedName("country") var country: String? = "",
    @SerializedName("current_living_details") var currentLivingDetails: String? = "",
    @SerializedName("linked_agency_name") var linkedAgencyName: String? = "",
    @SerializedName("linked_agency_firstname") var linkedAgencyFirstname: String? = "",
    @SerializedName("linked_agency_lastname") var linkedAgencyLastname: String? = "",
    @SerializedName("linked_agency_middlename") var linkedAgencyMiddlename: String? = "",
    @SerializedName("sum_mentor_session_log_count") var sum_mentor_session_log_count: String? = "0",
    @SerializedName("mentor_mentee_chat_count") var mentor_mentee_chat_count: String? = "0",
    @SerializedName("mentee_staff_chat_count") var mentee_staff_chat_count: Int? = 0,
    @SerializedName("schedule_session_count") var schedule_session_count: String? = "0",
    @SerializedName("unread_task") var unread_task: String? = "0",
    @SerializedName("unread_goal") var unread_goal: String? = "0",
    @SerializedName("session_log_label") var session_log_label: String? = "",
    @SerializedName("session_log_label_no") var session_log_label_no: String? = "0",
    @SerializedName("affiliate_system_messaging") val affiliateSystemMessaging: List<AffiliateSystemMessaging>?

)


data class ChangePasswordModel(
    @SerializedName("current_password") val currentPassword: String?,
    @SerializedName("new_password") val newPassword: String?,
    @SerializedName("confirm_new_password") val confirm_new_password: String?
)

data class NewChangePasswordModel(
    @SerializedName("current_password") val currentPassword: String?,
    @SerializedName("new_password") val newPassword: String?,
    @SerializedName("confirm_new_password") val confirm_new_password: String?
)

data class ForgotPasswordModel(
    @SerializedName("email") val email: String?,
    @SerializedName("password") val password: String?,
    @SerializedName("otp") val code: String?
)

data class MyMentorResponseDetails(
    @SerializedName("mentor_details") var listMyMentor: List<MyMentorDetails>?
)

data class MyMentorDetails(
    @SerializedName("id") var id: Int? = 0,
    @SerializedName("firstname") var firstname: String? = "",
    @SerializedName("middlename") var middlename: String? = "",
    @SerializedName("lastname") var lastname: String? = "",
    @SerializedName("email") var email: String? = "",
    @SerializedName("phone") var phone: String? = "",
    @SerializedName("address") var address: String? = "",
    @SerializedName("latitude") var latitude: String? = "",
    @SerializedName("longitude") var longitude: String? = "",
    @SerializedName("firebase_id") var firebaseToken: String? = "",
    @SerializedName("image") var imageUser: String? = "",
    @SerializedName("code") var code: String? = "",
    @SerializedName("channel_sid") var channelSid: String? = "",
    @SerializedName("last_session_date") var lastSessionDate: String? = "",
    @SerializedName("session_count") var sessionCount: String? = "",
    @SerializedName("unread_chat_count") var unread_chat_count: String? = "0",
    @SerializedName("session_log_count") var sessionLogCount: String? = "1",
    @SerializedName("session_log_label") var sessionLogLabel: String? = "",
    @SerializedName("session_log_label_no") var session_log_label_no: String? = "1"

)


data class MyStaffListResponseDetails(
    @SerializedName("staffs") var listMyStaffs: List<MyStaffDetails>?
)

data class MyStaffDetails(
    @SerializedName("id") var id: Int? = 0,
    @SerializedName("name") var name: String? = "",
    @SerializedName("email") var email: String? = "",
    @SerializedName("code") var code: String? = "",
    @SerializedName("channel_sid") var channelSid: String? = "",
    @SerializedName("unread_chat_count") var unreadChat: Int? = 0,
    @SerializedName("profile_pic") var imageUser: String? = "avatar"
)