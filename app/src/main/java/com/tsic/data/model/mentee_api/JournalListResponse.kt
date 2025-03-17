package com.tsic.data.model.mentee_api
import android.os.Parcelable
import com.google.gson.annotations.SerializedName
import kotlinx.android.parcel.Parcelize


data class JournalListResponse(
    @SerializedName("data")
    var journalList: List<Journal> = listOf(),
    @SerializedName("message")
    var message: String = "",
    @SerializedName("status")
    var status: Boolean = false
)

@Parcelize
data class Journal(
    @SerializedName("created_at")
    var createdAt: String = "",
    @SerializedName("description")
    var description: String = "",
    @SerializedName("id")
    var id: Int = 0,
    @SerializedName("title")
    var title: String = "",
    @SerializedName("updated_at")
    var updatedAt: String = "",
    @SerializedName("victim_id")
    var victimId: Int = 0,
    var updatedDate: String = "",
    var updatedTime: String = ""
) : Parcelable