package com.tsic.data.model.common


import com.google.gson.annotations.SerializedName

data class DisclaimerModel(
    @SerializedName("data")
    val `data`: Data,
    @SerializedName("message")
    val message: String,
    @SerializedName("status")
    val status: Boolean
) {
    data class Data(
        @SerializedName("disclamer")
        val disclamer: Disclamer
    ) {
        data class Disclamer(
            @SerializedName("id")
            val id: Int? = 0,
            @SerializedName("statement")
            val statement: String? = "",
            @SerializedName("url")
            val url: String? = ""
        )
    }
}