package com.tsic.data.model.mentee_api

import com.google.gson.annotations.SerializedName

data class JobListResponseModel(
    @SerializedName("datalist")
    var datalist: List<JobDatalist?> = listOf()
)

data class JobDatalist(
    @SerializedName("end_date")
    var endDate: String? = "",
    @SerializedName("exp_type")
    var expType: String? = "",
    @SerializedName("id")
    var id: Int? = 0,
    @SerializedName("job_title")
    var jobTitle: String? = "",
    @SerializedName("location")
    var location: String? = "",
    @SerializedName("start_date")
    var startDate: String? = "",
    @SerializedName("application_url")
    var applicationUrl: String? = "",
    @SerializedName("status")
    var status: Int? = 0,
    @SerializedName("yr_of_exp")
    var yrOfExp: String? = "0",
    @SerializedName("is_applied")
    var isApplied: Int? = 1,
    @SerializedName("company")
    var company: String? = "",
    @SerializedName("summary")
    var description: String? = ""
)

data class JobDetailsModel(
    @SerializedName("datadetails")
    var datadetails: JobDatadetails? = JobDatadetails()
)

data class JobDatadetails(
    @SerializedName("application_type")
    var applicationType: String? = "",
    @SerializedName("benefits")
    var benefits: String? = "",
    @SerializedName("company")
    var company: String? = "",
    @SerializedName("email_to_notify")
    var emailToNotify: String? = "",
    @SerializedName("end_date")
    var endDate: String? = "",
    @SerializedName("id")
    var id: Int? = 0,
    @SerializedName("if_resume")
    var ifResume: String? = "",
    @SerializedName("application_url")
    var applicationUrl: String? = "",
    @SerializedName("is_applied")
    var isApplied: Int? = 0,
    @SerializedName("job_education")
    var jobEducation: JobEducation? = JobEducation(),
    @SerializedName("job_experience")
    var jobExperience: JobExperience? = JobExperience(),
    @SerializedName("job_preferred_language")
    var jobPreferredLanguage: JobPreferredLanguage? = JobPreferredLanguage(),
    @SerializedName("job_preferred_location")
    var jobPreferredLocation: JobPreferredLocation? = JobPreferredLocation(),
    @SerializedName("job_title")
    var jobTitle: String? = "",
    @SerializedName("job_type")
    var jobType: String? = "",
    @SerializedName("location")
    var location: String? = "",
    @SerializedName("no_of_postion")
    var noOfPostion: String? = "",
    @SerializedName("responsibilities")
    var responsibilities: String? = "",
    @SerializedName("sallery_ending_range")
    var salleryEndingRange: String? = "",
    @SerializedName("sallery_starting_range")
    var salleryStartingRange: String? = "",
    @SerializedName("skills_qualification")
    var skillsQualification: String? = "",
    @SerializedName("start_date")
    var startDate: String? = "",
    @SerializedName("status")
    var status: Int? = 0,
    @SerializedName("summary")
    var summary: String? = "",
    @SerializedName("upload_desc")
    var uploadDescription: String? = ""
)


data class JobPreferredLanguage(
    @SerializedName("id")
    var id: Int? = 0,
    @SerializedName("job_id")
    var jobId: Int? = 0,
    @SerializedName("language")
    var language: String? = "",
    @SerializedName("language_cat")
    var languageCat: String? = ""
)

data class JobPreferredLocation(
    @SerializedName("id")
    var id: Int? = 0,
    @SerializedName("job_id")
    var jobId: Int? = 0,
    @SerializedName("location_cat")
    var locationCat: String? = "",
    @SerializedName("preferred_location")
    var preferredLocation: String? = ""
)

data class JobEducation(
    @SerializedName("edu_cat")
    var eduCat: String? = "",
    @SerializedName("id")
    var id: Int? = 0,
    @SerializedName("job_id")
    var jobId: Int? = 0,
    @SerializedName("min_education")
    var minEducation: String? = ""
)

data class JobExperience(
    @SerializedName("exp_cat")
    var expCat: String? = "",
    @SerializedName("exp_type")
    var expType: String? = "",
    @SerializedName("id")
    var id: Int? = 0,
    @SerializedName("job_id")
    var jobId: Int? = 0,
    @SerializedName("yr_of_exp")
    var yrOfExp: Int? = 0
)



