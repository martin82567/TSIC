package com.tsic.ui.screen.mentor_drawer_menu.resource


import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentor_api.ELearning


object MentorResourceBindings {
    @JvmStatic
    @BindingAdapter(value = ["list_Elearning"], requireAll = true)
    fun RecyclerView.loadFiles(listElearning: List<ELearning?>) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            adapter =
                MentorResourceListAdapter(listElearning) //else MenteeJobRecyclerAdapter(emptyList())
        }
    }
}