package com.tsic.data.model.mentor_api


import android.os.Parcelable
import com.google.gson.annotations.SerializedName
import kotlinx.android.parcel.Parcelize


data class MentorGoalListResponseModel(
    @SerializedName("datalist")
    var dataList: List<MentorGoalList>? = listOf()
)

@Parcelize
data class MentorGoalList(
    @SerializedName("created_by")
    val createdBy: Int? = 0, // 61
    @SerializedName("created_date")
    val createdDate: String? = "", // 08-23-2019 03:37:41
    @SerializedName("dead_line")
    val deadLine: String? = "",
    @SerializedName("description")
    val description: String? = "", // Description Form Mentor
    @SerializedName("end_date")
    val endDate: String? = "", // 08-30-2019
    @SerializedName("frequency")
    val frequency: Int? = 0, // 0
    @SerializedName("id")
    val id: Int? = 0, // 39
    @SerializedName("name")
    val name: String? = "", // test from Mentor
    @SerializedName("point")
    val point: Int? = 0, // 0
    @SerializedName("reminder")
    val reminder: Int? = 0, // 0
    @SerializedName("staff_id")
    val staffId: Int? = 0, // 10
    @SerializedName("start_date")
    val startDate: String? = "", // 08-21-2019
    @SerializedName("status")
    val status: Int? = 0, // 1
    @SerializedName("type")
    val type: String? = "", // goal
    @SerializedName("user_type")
    val userType: String? = "" // mentor
) : Parcelable