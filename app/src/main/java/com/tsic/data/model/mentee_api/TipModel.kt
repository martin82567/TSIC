package com.tsic.data.model.mentee_api

import android.os.Parcelable
import com.google.gson.annotations.SerializedName
import kotlinx.android.parcel.Parcelize

//-----------------------Agency  ---------------//

data class SelectAgencyResponseModel(
    val status: Boolean,
    val message: String?,
    @SerializedName("agency_details") val nearby: List<SelectAgencyResponseItem>,
    @SerializedName("state_agency") val statewise: List<SelectAgencyResponseItem>
)

data class SelectAgencyResponseItem(
    @SerializedName("status") var status: Int? = null,
    @SerializedName("type") var type: Int? = null,
    @SerializedName("jurisdiction_area") var jurisdictionArea: String? = null,
    @SerializedName("latitude") var latitude: String? = null,
    @SerializedName("longitude") var longitude: String? = null,
    @SerializedName("name") var name: String? = null,
    @SerializedName("id") var id: Int? = null,
    @SerializedName("location_id") var locationId: Int? = null
)


//-----------------------Person  ---------------//
@Parcelize
data class PersonItem(
    @SerializedName("id") var id: Int,// 0 denotes new person,other numbers denote updation
    @SerializedName("tips_id") var tipsId: Int,
    @SerializedName("suspect_person_type") val personType: String,
    @SerializedName("suspect_name") val name: String,
    @SerializedName("suspect_sex") val sex: String,
    @SerializedName("suspect_race") val race: String,
    @SerializedName("suspect_height") val height: String,
    @SerializedName("suspect_weight") val weight: String,
    @SerializedName("suspect_eye_color") val eyeColor: String,
    @SerializedName("suspect_hair_color") val hairColor: String,
    @SerializedName("suspect_age") val age: String,
    @SerializedName("suspect_tattoos_marks") val tatoosMark: String,
    @SerializedName("suspect_phone") val phone: String,
    @SerializedName("suspect_other_info") val otherInfo: String
) : Parcelable


//-----------------------Vehicle  ---------------//
@Parcelize
data class VehicleItem(
    @SerializedName("id") var id: Int,// 0 denotes new Vehicle,other numbers denote updation
    @SerializedName("tips_id") var tipsId: Int,
    @SerializedName("vehicle_year") val vehicleYear: String,
    @SerializedName("vehicle_make") val make: String,
    @SerializedName("vehicle_model") val model: String,
    @SerializedName("vehicle_type") val type: String,
    @SerializedName("vehicle_color") val color: String,
    @SerializedName("vehicle_tag") val tag: String,
    @SerializedName("vehicle_state") val state: String,
    @SerializedName("vehicle_description") val description: String
) : Parcelable


//-----------------------Submit Tip  ---------------//
@Parcelize
data class TipDetail(
    @SerializedName("address") var address: String? = "",
    @SerializedName("agency_id") var agencyId: Int? = 0,
    @SerializedName("broadcast_id") var broadcastId: Int? = 0,
    @SerializedName("comes_from") var comesFrom: String? = "",
    @SerializedName("created_date") var createdDate: String? = "",
    @SerializedName("hear_about") var hearAbout: String? = "",
    @SerializedName("id") var id: Int? = 0,
    @SerializedName("is_anonymous") var isAnonymous: Int? = 0,
    @SerializedName("is_chat_anonymous") var isChatAnonymous: Int? = 0,
    @SerializedName("is_chat_unread") var isChatUnread: Int? = 0,
    @SerializedName("is_keyword_get") var isKeywordGet: Int? = 0,
    @SerializedName("known_person") var knownPerson: String? = "",
    @SerializedName("latitude") var latitude: String? = "",
    @SerializedName("longitude") var longitude: String? = "",
    @SerializedName("name") var name: String? = "",
    @SerializedName("state") var state: String? = "",
    @SerializedName("status_name") var statusName: String? = "",
    @SerializedName("timezone") var timezone: String? = "",
    @SerializedName("tips_information") var tipsInformation: String? = "",
    @SerializedName("tips_status") var tipsStatus: Int? = 0,
    @SerializedName("tips_type") var tipsType: String? = "",
    @SerializedName("updated_date") var updatedDate: String? = "",
    @SerializedName("user_address") var userAddress: String? = "",
    @SerializedName("user_details") var userDetails: String? = "",
    @SerializedName("user_id") var userId: Int? = 0,
    @SerializedName("user_lat") var userLat: String? = "",
    @SerializedName("user_long") var userLong: String? = "",
    @SerializedName("suspects") var suspects: List<PersonItem> = emptyList(),
    @SerializedName("vehicles") var vehicles: List<VehicleItem> = emptyList(),
    @SerializedName("tips_attachments") var tips_attachments: List<TipAttachmentItem> = emptyList(),
    var forNotification: Boolean? = false
) : Parcelable

@Parcelize
data class TipAttachmentItem(
    @SerializedName("id") var id: Int,
    @SerializedName("tips_id") var tipsId: Int,
    @SerializedName("type") var type: String,
    @SerializedName("image") var image: String
) : Parcelable


data class SubmitTipResponse(
    @SerializedName("message") var message: String?, // Post added successfully
    @SerializedName("status") var status: Boolean?, // true
    @SerializedName("tips_id") var tipsId: Int?, // 740
    @SerializedName("token") var token: String? // Bearer 5c39d39c47cd4_1_5c39d39c47d1f
)


class PersonVehicleRequest(
    @SerializedName("tips_id") var tipsId: String,
    @SerializedName("arrPerson") var personInformations: List<HashMap<String, String>>,
    @SerializedName("arrVehicle") var vehicleInformations: List<HashMap<String, String>>
)


data class PersonVehicleResponse(
    @SerializedName("data") var `data`: Data?,
    @SerializedName("status") var status: Boolean?, // true
    @SerializedName("tips_id") var tipsId: String?, // 755
    @SerializedName("token") var token: String? // Bearer 5c39d39c47cd4_1_5c39d39c47d1f
) {
    data class Data(
        @SerializedName("arrPerson") var arrPerson: List<ArrPerson?>?,
        @SerializedName("arrVehicle") val arrVehicle: List<ArrVehicle?>?,
        @SerializedName("tips_id") val tipsId: String? // 755
    ) {
        data class ArrVehicle(
            @SerializedName("vehicle_color") var vehicleColor: String?,
            @SerializedName("vehicle_description") var vehicleDescription: String?,
            @SerializedName("vehicle_make") var vehicleMake: String?, // name 2
            @SerializedName("vehicle_model") var vehicleModel: String?,
            @SerializedName("vehicle_state") var vehicleState: String?,
            @SerializedName("vehicle_tag") var vehicleTag: String?,
            @SerializedName("vehicle_type") var vehicleType: String?,
            @SerializedName("vehicle_year") var vehicleYear: String? // pt
        )

        data class ArrPerson(
            @SerializedName("suspect_age") var suspectAge: String?, // 66
            @SerializedName("suspect_eye_color") var suspectEyeColor: String?, // look
            @SerializedName("suspect_hair_color") var suspectHairColor: String?, // blkh
            @SerializedName("suspect_height") var suspectHeight: String?, // 45
            @SerializedName("suspect_name") var suspectName: String?, // name 2
            @SerializedName("suspect_other_info") var suspectOtherInfo: String?, // oye
            @SerializedName("suspect_person_type") var suspectPersonType: String?, // witness
            @SerializedName("suspect_phone") var suspectPhone: String?, // 5
            @SerializedName("suspect_race") var suspectRace: String?, // blk
            @SerializedName("suspect_sex") var suspectSex: String?, // female
            @SerializedName("suspect_tattoos_marks") var suspectTattoosMarks: String?, // toto
            @SerializedName("suspect_weight") var suspectWeight: String? // 4
        )
    }
}


//-----------------------Submitted Tip  ---------------//
data class SubmitedTipsResponseModel(
    @SerializedName("details") var details: List<TipDetail?> = listOf(),
    @SerializedName("status") var status: Boolean = false,
    @SerializedName("token") var token: String? = ""
)

