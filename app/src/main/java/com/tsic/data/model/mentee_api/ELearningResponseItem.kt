package com.tsic.data.model.mentee_api


import com.google.gson.annotations.SerializedName


data class ELearingSearchResponseModel(
    @SerializedName("resource")
    var eLearningLst: List<ELearningResponseItem> = listOf()

)

data class ELearningDetailsResponseModel(
    @SerializedName("e_learning_list") val eLearningDetails: ELearningResponseItem
)

data class ELearningResponseItem(
    @SerializedName("added_by")
    val addedBy: Int? = 0, // 3
    @SerializedName("article_link")
    val articleLink: String? = "",
    @SerializedName("created_date")
    val createdDate: String? = "", // 2018-12-27 11:39:53
    @SerializedName("description")
    val description: String? = "", // E Learning test Description
    @SerializedName("file")
    val `file`: String? = "",
    @SerializedName("id")
    val id: Int? = 0, // 1
    @SerializedName("is_active")
    val isActive: Int? = 0, // 1
    @SerializedName("name")
    val name: String? = "", // E Learning test
    @SerializedName("type")
    val type: String? = "" // image
)