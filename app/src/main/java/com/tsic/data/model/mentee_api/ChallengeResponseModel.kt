package com.tsic.data.model.mentee_api

import com.google.gson.annotations.SerializedName


data class ChallengeListResponseModel(
    @SerializedName("datalist")
    var datalist: List<ChallengeData>? = listOf()
)

data class ChallengeData(
    @SerializedName("assign_id")
    var assignId: Int? = 0,
    @SerializedName("begin_time")
    var beginTime: String? = "",
    @SerializedName("complated_time")
    var complatedTime: String? = "",
    @SerializedName("created_by")
    var createdBy: Int? = 0,
    @SerializedName("created_date")
    var createdDate: String? = "",
    @SerializedName("datastatus")
    var datastatus: Int? = 0,
    @SerializedName("dead_line")
    var deadLine: String? = "",
    @SerializedName("description")
    var description: String? = "",
    @SerializedName("end_date")
    var endDate: String? = "",
    @SerializedName("frequency")
    var frequency: Int? = 0,
    @SerializedName("id")
    var id: Int? = 0,
    @SerializedName("name")
    var name: String? = "",
    @SerializedName("note")
    var note: String? = "",
    @SerializedName("reminder")
    var reminder: Int? = 0,
    @SerializedName("start_date")
    var startDate: String? = "",
    @SerializedName("status")
    var status: Int? = 0,
    @SerializedName("type")
    var type: String? = ""
)