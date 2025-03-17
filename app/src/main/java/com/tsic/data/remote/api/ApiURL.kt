package com.tsic.data.remote.api

import com.tsic.BuildConfig

//val DEBUG = false       //false//for live false  and  for debug true
val DEBUG = BuildConfig.DEBUG       //false//for live false  and  for debug true
var busy = false
var timestamp: Long = 0
var isShowCallUIOneTime = 0
var finishUI = true
var isCallDisconnect = true
val BASE_URL: String =
    if (DEBUG) "https://test.tsicmentorapp.org/" else "https://tsicmentorapp.org"
/*val BASE_URL: String =
    if (DEBUG) "https://mentorappdev.tsic.org/" else "https://tsicmentorapp.org/"*/
val CHAT_URL: String =
    if (DEBUG) "https://test.tsicmentorapp.org:3700/" else "https://tsicmentorapp.org:3700/"

//val TWILIO_CHAT_URL: String =
//    if (DEBUG) "https://mentorappdev.tsic.org:3700/" else "https://mentorappdev.tsic.org:3700/"

val VIDEO_URL: String =
    if (DEBUG) "https://test.tsicmentorapp.org:3000/" else "https://tsicmentorapp.org:3000/"

//Mentor URL

val MENTOR_IMAGE_URL: String =
    if (DEBUG) "https://tsicdev.s3.us-east-2.amazonaws.com/mentor_pic/" else "https://tsic.s3.us-east-2.amazonaws.com/mentor_pic/"

val MENTOR_STAFF_IMAGE_URL: String =
    if (DEBUG) "https://tsicdev.s3.us-east-2.amazonaws.com/agency_pic/" else "https://tsic.s3.us-east-2.amazonaws.com/agency_pic/"

var MENTEE_REPORT_IMAGE_BASE_URL =
    if (DEBUG) "https://tsicdev.s3.us-east-2.amazonaws.com/report/" else "https://tsic.s3.us-east-2.amazonaws.com/report/"

//val MENTOR_HELP_URL =
//    "http://tsicmobileapp-faq.s3-website-us-east-1.amazonaws.com/mentor.html"

val MENTOR_MENTEE_TOOLKIT_URL =
    "https://drive.google.com/file/d/14ZEN4PAIwPTlhdUv5j9XnXsbv6oZO8AY/view"      //--->NEW LINK
//    "https://drive.google.com/file/d/1f45-CkpruU34gMB1t1qyGxApZSrdZo2D/view?usp=sharing"   //--->OLD LINK



//Mentee URL
val MENTEE_IMAGE_URL: String =
    if (DEBUG) "https://tsicdev.s3.us-east-2.amazonaws.com/userimage/" else "https://tsic.s3.us-east-2.amazonaws.com/userimage/"

val AGENCY_IMAGE_URL: String =
    if (DEBUG) "https://tsicdev.s3.us-east-2.amazonaws.com/agency_pic/" else "https://tsic.s3.us-east-2.amazonaws.com/agency_pic/"

val MEENTEE_REPORT_URL: String =
    if (DEBUG) "https://tsicdev.s3.us-east-2.amazonaws.com/report/" else "https://tsic.s3.us-east-2.amazonaws.com/report/"

val TASK_IMAGE_URL: String = "https://tsic.s3.us-east-2.amazonaws.com/goaltask/"

val E_Learning_BaseFile =
    if (DEBUG) "https://tsicdev.s3.us-east-2.amazonaws.com/e_learning/" else "https://tsic.s3.us-east-2.amazonaws.com/e_learning/"

//val MENTEE_HELP_URL =
//    "http://tsicmobileapp-faq.s3-website-us-east-1.amazonaws.com/mentee.html"


