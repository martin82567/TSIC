package com.tsic.data.model.mentor_api

import com.google.gson.annotations.SerializedName

data class TodaysMeetingModel(
    @SerializedName("school_name") val school_name: String = "",
    @SerializedName("title") val title: String = "",
    @SerializedName("description") val description: String,
    @SerializedName("latitude") val latitude: Double,
    @SerializedName("longitude") val longitude: Double,
    @SerializedName("schedule_time") val schedule_time: String,
    @SerializedName("id") val id: String
)