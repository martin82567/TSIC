package com.tsic.data.model.mentee_api

import android.os.Parcelable
import com.google.gson.annotations.SerializedName
import kotlinx.android.parcel.Parcelize

data class ChallengeDetailsModel(
    @SerializedName("datadetails") var datadetails: DataDetail? = DataDetail()
)

data class DataDetail(
    @SerializedName("adminuploadedfile")
    var adminuploadedfile: List<Uploadedfile?> = listOf(),
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
    var datastatus: Int? = 2,
    @SerializedName("dead_line")
    var deadLine: String? = "",
    @SerializedName("description")
    var description: String? = "",
    @SerializedName("end_date")
    var endDate: String? = "",
    @SerializedName("frequency")
    var frequency: Int? = 0,
    @SerializedName("mentor_name")
    var mentorName: String? = "",
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
    var type: String? = "",
    @SerializedName("useruploadedfile")
    var useruploadedfile: List<Uploadedfile?>? = mutableListOf(),
    @SerializedName("notes")
    var notes: List<NoteDetails?> = listOf()
)

data class NoteDetails(
    @SerializedName("created_date")
    var createdDate: String? = "",
    @SerializedName("goaltask_id")
    var goaltaskId: Int? = 0,
    @SerializedName("id")
    var id: Int? = 0,
    @SerializedName("note")
    var note: String? = "",
    @SerializedName("title")
    var title: String? = "",
    @SerializedName("type")
    var type: String? = "",
    @SerializedName("victim_id")
    var victimId: Int? = 0
)


@Parcelize
data class Uploadedfile(
    @SerializedName("file_name")
    var fileName: String? = "",
    @SerializedName("added_by")
    var addedBy: Int? = 0,
    @SerializedName("created_date")
    var createdDate: String? = "",
    @SerializedName("goaltask_id")
    var goaltaskId: Int? = 0,
    @SerializedName("id")
    var id: Int? = 0,
    @SerializedName("file_path")
    var filePath: String? = ""
) : Parcelable

data class UserFileUpload(
    @SerializedName("useruploadedfile")
    var useruploadedfile: List<Uploadedfile?> = listOf()
)

data class NoteAddedModel(
    @SerializedName("notes")
    var notes: List<NoteDetails?> = listOf()
)