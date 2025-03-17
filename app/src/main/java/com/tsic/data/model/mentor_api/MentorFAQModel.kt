package com.tsic.data.model.mentor_api


import com.google.gson.annotations.SerializedName

data class MentorFAQModel(
    @SerializedName("data")
    val `data`: Data,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: Boolean
) {
    data class Data(
        @SerializedName("mentor_faq")
        val mentorFaq: String
    )
}