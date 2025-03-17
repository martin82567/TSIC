package com.tsic.ui.screen.chatdetails


import android.widget.TextView
import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.RecyclerView
import java.text.SimpleDateFormat
import java.util.*

object ChatDetailsBinding {
    var timezoneOffset = ""

    @JvmStatic
    @BindingAdapter(value = ["timezone_offset"], requireAll = true)
    fun RecyclerView.loadFiles(

        timeZone: String
    ) {
        this.apply {
            timezoneOffset = timeZone
            this@loadFiles.scrollToPosition(0)
        }
    }

    @JvmStatic
    @BindingAdapter(value = ["set_date"], requireAll = false)//,"timezone"
    fun TextView.setData(value: String) {//,timezoneOffset:String
        val sdf = getUTCTimeFormat("yyyy-MM-dd'T'HH:mm:ss.SSS'Z'")

        val date = sdf.parse(value)
        date?.let {
            val timeChat = getSimpleDateFormat("EEE, d MMM yyyy").format(date)
            val timeToday =
                getSimpleDateFormat(
                    "EEE, d MMM yyyy"
                ).format(Date(System.currentTimeMillis()))
            if (timeChat == timeToday) {
                this.text = getSimpleDateFormat("h:mm a").format(date)
            } else {
                this.text = getSimpleDateFormat("MM-dd-yyyy").format(date)
            }
        }

    }

    private fun getSimpleDateFormat(pattern: String): SimpleDateFormat {
        val sdf = SimpleDateFormat(pattern, Locale.getDefault())
        sdf.timeZone = SimpleTimeZone.getDefault()
        return sdf
    }

    private fun getUTCTimeFormat(pattern: String): SimpleDateFormat{
        val sdf = SimpleDateFormat(pattern, Locale.getDefault())
        sdf.timeZone = SimpleTimeZone.getTimeZone("UTC")
        return sdf
    }
}