package com.tsic.ui.screen.mentee_drawer_menu.learning


import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentee_api.LearningResponseItem


object MenteeElearningBindings {
    @JvmStatic
    @BindingAdapter(value = ["list_Elearning"], requireAll = true)
    fun RecyclerView.loadFiles(listElearning: List<LearningResponseItem?>) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            adapter =
                MenteeElearningListAdapter(listElearning) //else MenteeJobRecyclerAdapter(emptyList())
        }
    }
}