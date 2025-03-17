package com.tsic.ui.screen.mentor_drawer_menu.report_list

import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentor_api.MentorReport

object MentorReportListBinding {

    @JvmStatic
    @BindingAdapter(value = ["report_list"], requireAll = true)
    fun RecyclerView.loadFiles(listReport: List<MentorReport>) {
        this.adapter = MentorReportRecyclerAdapter(listReport)
    }


}