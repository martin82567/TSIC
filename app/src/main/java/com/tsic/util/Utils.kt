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

    fun formatDate(inputDate: String): String {
        val inputFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
        val outputFormat = SimpleDateFormat("MM-dd-yyyy", Locale.getDefault())

        val date = inputFormat.parse(inputDate)
        return outputFormat.format(date!!)
    }

    fun convertToIso8601(input: String): String {
        val inputFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
        inputFormat.timeZone = TimeZone.getTimeZone("UTC") // Optional based on source timezone

        val date = inputFormat.parse(input)

        val outputFormat = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSS'Z'", Locale.getDefault())
        outputFormat.timeZone = TimeZone.getTimeZone("UTC")

        return outputFormat.format(date!!)
    }

}