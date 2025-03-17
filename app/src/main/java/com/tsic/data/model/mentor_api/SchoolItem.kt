package com.tsic.data.model.mentor_api


import com.google.gson.annotations.SerializedName

data class SchoolItem(
    @SerializedName("address")
    val address: String? = "", // 2155 Bahia Vista Street, Sarasota, FL, USA
    @SerializedName("agency_id")
    val agencyId: Int? = 0, // 61
    @SerializedName("city")
    val city: String? = "", // Sarasota
    @SerializedName("created_at")
    val createdAt: String? = "", // 2019-07-30 15:24:42
    @SerializedName("id")
    val id: Int? = 0, // 2
    @SerializedName("latitude")
    val latitude: String? = "", // 27.32452
    @SerializedName("longitude")
    val longitude: String? = "", // -82.52686
    @SerializedName("name")
    val name: String? = "", // Sarasota High School
    @SerializedName("state")
    val state: String? = "", // FL
    @SerializedName("status")
    val status: Int? = 0, // 1
    @SerializedName("updated_at")
    val updatedAt: String? = "", // 0000-00-00 00:00:00
    @SerializedName("zip")
    val zip: Int? = 0 // 34241
)