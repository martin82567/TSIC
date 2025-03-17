package com.tsic.data.model.mentor_api

import android.os.Parcelable
import com.google.gson.annotations.SerializedName
import kotlinx.android.parcel.Parcelize


data class ResourceModel(
    val data: ELearningResponse
)


data class ELearningResponse(
    @SerializedName("e_learning_list")
    val elearninglist: List<ELearning> = listOf()
)

@Parcelize
data class ELearning(
    val affiliate_id: String? = "",
    val description: String? = "",
    val file: String? = "",
    val id: String? = "",
    val name: String? = "",
    val type: String? = "",
    val url: String? = ""
) : Parcelable