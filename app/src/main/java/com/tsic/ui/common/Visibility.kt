package com.tsic.ui.common

import android.view.View
import android.widget.TextView
import androidx.databinding.BindingAdapter

object Visibility {
    @JvmStatic
    @BindingAdapter(value = ["visibility"], requireAll = false)
    fun TextView.setImage(value: String?) {
        visibility = if (value == "" || value == "0") View.INVISIBLE else View.VISIBLE
    }
}