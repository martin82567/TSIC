package com.tsic.ui.screen.mentor_bottom_menu.mysessions

import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentor_api.SessionResponse

object MentorSessionListBindings {
    @JvmStatic
    @BindingAdapter(value = ["list_session"], requireAll = true)
    fun RecyclerView.loadFiles(listSession: List<SessionResponse>) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            setHasFixedSize(true)
            setItemViewCacheSize(listSession.size)
            adapter =
                MentorSessionListRecycleAdapter(listSession) //else JobRecyclerAdapter(emptyList())
        }
    }
}