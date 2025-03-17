package com.tsic.util

import com.tsic.ui.screen.chatdetails.ChatDetailsBinding
import java.text.SimpleDateFormat
import java.util.*

object Utils {


    private fun getUSTimeFormat(pattern: String): SimpleDateFormat {
        val sdf = SimpleDateFormat(pattern, Locale.US)
        sdf.timeZone = SimpleTimeZone.getDefault()
        return sdf
    }

    fun getSimplifiedDate(dateOriginal: String?): String {
        if (dateOriginal == null) {
            return ""
        }
        val sdf = getUSTimeFormat("yyyy-MM-dd HH:mm:ss")
        sdf.parse(dateOriginal)?.let { date ->
            val timeChat = getSimpleDateFormat("EEE, d MMM yyyy").format(date)
            val timeToday =
                getSimpleDateFormat(
                    "EEE, d MMM yyyy"
                ).format(Date(System.currentTimeMillis()))
            return if (timeChat == timeToday) {
                getSimpleDateFormat("h:mm a").format(date)
            } else {
                getSimpleDateFormat("MM-dd-yyyy h:mm a").format(date)
            }
        } ?: run {
            return ""
        }
    }


    private fun getSimpleDateFormat(pattern: String): SimpleDateFormat {
        val sdf = SimpleDateFormat(pattern, Locale.getDefault())
        sdf.timeZone = SimpleTimeZone.getDefault()
        return sdf
    }

}