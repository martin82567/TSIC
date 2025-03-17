package com.tsic.data.model.mentor_api

import com.google.gson.annotations.SerializedName

class SessionMethodLocationList(
    @SerializedName("session_method_location")
    val sessionMethodLocation: List<SessionMethodLocation>?
)

class SessionMethodLocation(
    @SerializedName("id")
    val id: String? = "",
    @SerializedName("method_id")
    val method_id: String? = "",
    @SerializedName("method_value")
    val method_value: String? = "",
    @SerializedName("status")
    val status: String? = "",
    @SerializedName("created_date")
    val created_date: String? = ""
)
