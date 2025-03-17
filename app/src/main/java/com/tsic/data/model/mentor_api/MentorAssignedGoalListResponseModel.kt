package com.tsic.data.model.mentor_api


import com.google.gson.annotations.SerializedName

data class MentorAssignedGoalListResponseModel(
    @SerializedName("assign_id")
    val assignId: Int? = 0, // 20
    @SerializedName("type")
    val type: String? = "" // goal
)