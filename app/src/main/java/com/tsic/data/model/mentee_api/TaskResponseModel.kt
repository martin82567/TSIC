package com.tsic.data.model.mentee_api

import com.google.gson.annotations.SerializedName

data class TaskListResponseModel(
    @SerializedName("datalist")
    var datalist: List<TaskDatalist> = listOf()
)

data class TaskDatalist(
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
    @SerializedName("mentor_name")
    var mentorName: String? = "",
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

/*
data class TaskDatalist(
    @SerializedName("assign_id")
    var assignId: Int? = 0,
    @SerializedName("created_by")
    var createdBy: Int? = 0,
    @SerializedName("created_date")
    var createdDate: String? = "",
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
    @SerializedName("reminder")
    var reminder: Int? = 0,
    @SerializedName("start_date")
    var startDate: String? = "",
    @SerializedName("status")
    var status: Int? = 0,
    @SerializedName("type")
    var type: String? = ""
)*/
