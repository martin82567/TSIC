package com.tsic.data.model.mentee_api

import android.os.Parcelable
import com.google.gson.annotations.SerializedName
import kotlinx.android.parcel.Parcelize

data class ResourceSearchRequestModel(
    @SerializedName("latitude") val latitude: String?,
    @SerializedName("longitude") val longitude: String?,
    @SerializedName("state_code") val stateCode: String?,
    @SerializedName("search_keyword") val searchKeyword: String?
)

data class ResourceSearchResponseModel(
    @SerializedName("resource")
    var resourceList: List<ResourceResponseItem> = listOf()

)

data class ResourceDetailsResponseModel(
    @SerializedName("resource_details") val resourceDetails: ResourceResponseItem
)


@Parcelize
data class ResourceResponseItem(
    @SerializedName("id") val resourceId: Int?,
    @SerializedName("pic_url") val picUrl: String?,
    @SerializedName("name") val name: String?,
    @SerializedName("category") val category: String?,
    @SerializedName("email") val email: String?,
    @SerializedName("address") val address: String?,
    @SerializedName("state") val state: String?,
    @SerializedName("cell_phone") val cellPhone: String?,
    @SerializedName("work_phone") val workPhone: String?,
    @SerializedName("website") val website: String? = "",
    @SerializedName("description") val description: String?,
    @SerializedName("resource_files_list") val resourceFilesList: List<Uploadedfile>?
) : Parcelable