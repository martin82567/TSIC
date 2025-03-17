package com.tsic.data.model.common


import androidx.annotation.Keep
import com.google.gson.annotations.SerializedName

@Keep
data class SystemMessage(
    @SerializedName("messaging")
    val messaging: List<Messaging>
) {
    @Keep
    data class Messaging(
        @SerializedName("app_id")
        val appId: Int,
        @SerializedName("end_datetime")
        var endDatetime: String,
        @SerializedName("id")
        val id: Int,
        @SerializedName("message")
        val message: String,
        @SerializedName("start_datetime")
        var startDatetime: String,
        var shouldVisible : Boolean = false
    ){
        // Null System Message
        constructor() : this(-1, "", -1, "", "", false)
    }
}