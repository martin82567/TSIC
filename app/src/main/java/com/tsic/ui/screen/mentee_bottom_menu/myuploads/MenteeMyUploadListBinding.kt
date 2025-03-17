package com.tsic.ui.screen.mentee_bottom_menu.myuploads

import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentor_api.MenteeReportList

object MenteeMyUploadListBinding {

    @JvmStatic
    @BindingAdapter(value = ["report_list"], requireAll = true)
    fun RecyclerView.loadFiles(listReport: List<MenteeReportList>) {
        this.adapter = MentorMyUploadRecyclerAdapter(listReport)
    }


}