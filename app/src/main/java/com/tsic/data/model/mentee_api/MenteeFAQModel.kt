package com.tsic.data.model.mentee_api


import com.google.gson.annotations.SerializedName

data class MenteeFAQModel(
    @SerializedName("data")
    val `data`: Data,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: Boolean
) {
    data class Data(
        @SerializedName("mentee_faq")
        val menteeFaq: String
    )
}