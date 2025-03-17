package com.tsic.data.model.common

import com.google.gson.annotations.SerializedName

data class TimeZoneModel(
    @SerializedName("timezone") val timezone: String?,
    @SerializedName("timezone_offset") val timezoneOffset: String?
)