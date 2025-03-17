package com.tsic.ui.screen.chat


import android.widget.TextView
import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.RecyclerView
import java.text.SimpleDateFormat
import java.util.*

object ChatTimeBinding {
    @JvmStatic
    @BindingAdapter(value = ["set_chat_date"], requireAll = false)//,"timezone"
    fun TextView.setData(value: String) {//,timezoneOffset:String
        val sdf = getSimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSS'Z'")

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
        val sdf = SimpleDateFormat(pattern, Locale.US)
        sdf.timeZone = SimpleTimeZone.getTimeZone("GMT")
        return sdf
    }
}