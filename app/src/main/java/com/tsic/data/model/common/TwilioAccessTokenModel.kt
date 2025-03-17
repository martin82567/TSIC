package com.tsic.data.model.common


import com.google.gson.annotations.SerializedName

data class TwilioAccessTokenModel(
    @SerializedName("token")
    val token: String? = "",
    @SerializedName("identity")
    val identity: String? = ""
)