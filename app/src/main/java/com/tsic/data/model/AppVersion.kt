package com.tsic.data.model

import com.google.gson.annotations.SerializedName

data class AppVersion(
    @SerializedName("app_version") val appVersion: VersionDetails
)

data class VersionDetails(
    @SerializedName("id") val id: Int,
    @SerializedName("version_code") val versionCode: Int,
    @SerializedName("updated_at") val updatedAt: String
)