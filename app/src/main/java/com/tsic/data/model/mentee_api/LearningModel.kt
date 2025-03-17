package com.tsic.data.model.mentee_api

import android.os.Parcelable
import com.google.gson.annotations.SerializedName
import kotlinx.android.parcel.Parcelize

data class LearningSearchResponseModel(
    @SerializedName("e_learning_list") val learningList: List<LearningResponseItem> = listOf()
)

//List<LearningResponseItem> = listOf()

//LearningResponseItem?
//)

data class LearningDetailResponseModel(
    @SerializedName("e_learning_details") val eLearningDetails: LearningResponseItem?
)

@Parcelize
data class LearningResponseItem(
    @SerializedName("id") val learningId: Int?,
    @SerializedName("article_link") val articleLink: String?,
    @SerializedName("type") val type: String?,
    @SerializedName("file") val fileUrl: String? = "",
    @SerializedName("name") val titleName: String?,
    @SerializedName("description") var description: String?,
    @SerializedName("url") var url: String?,
    @SerializedName("is_active") var isActive: String?,
    @SerializedName("added_by") var addedBy: String?,
    @SerializedName("created_date") var createdDate: String?

) : Parcelable