package com.tsic.data.model.mentee_api


import com.google.gson.annotations.SerializedName

data class ResourceDetail(
    @SerializedName("resource_details")
    val resourceDetails: ResourceDetails? = ResourceDetails()
)

data class ResourceDetails(
    @SerializedName("address")
    val address: String? = "", // Camac Street, Camac Street, Elgin, Kolkata, West Bengal, India
    @SerializedName("category")
    val category: String? = "", // Doctor
    @SerializedName("cell_phone")
    val cellPhone: String? = "", // 769867868
    @SerializedName("description")
    val description: String? = "", // Desc
    @SerializedName("email")
    val email: String? = "",
    @SerializedName("name")
    val name: String? = "",// arnab1@gmail.com
    @SerializedName("firstname")
    val firstname: String? = "", // Doctor
    @SerializedName("id")
    val id: Int? = 0, // 1
    @SerializedName("lastname")
    val lastname: String? = "", // Arnab
    @SerializedName("middlename")
    val middlename: String? = "",
    @SerializedName("pic_url")
    val picUrl: String? = "",
    @SerializedName("resource_files_list")
    val resourceFilesList: List<ResourceFiles?>? = listOf(),
    @SerializedName("state")
    val state: String? = "", // AZ
    @SerializedName("website")
    val website: String? = "", // 8709878
    @SerializedName("work_phone")
    val workPhone: String? = "" // 9870987
) {
    data class ResourceFiles(
        @SerializedName("file_path")
        val filePath: String? = "", // http://209.59.156.100/~devmoretolife/public/uploads/resource_pic/test file.xlsx
        @SerializedName("name")
        val name: String? = "" // Documents
    )
}