package com.tsic.ui.screen.mentor_bottom_menu.mychats.my_mentee_list

import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentor_api.MentorMyMenteeModel

object MentorMyMenteeChatsBinding {
    @JvmStatic
    @BindingAdapter(value = ["mentee_list", "activity"], requireAll = true)
    fun RecyclerView.loadFiles(
        menteeList: List<MentorMyMenteeModel>?,
        activity: MentorMyMenteeChatListActivity?
    ) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            activity?.let {
                if (!menteeList.isNullOrEmpty()) {
                    adapter =
                        MentorMyMenteeListAdapter(
                            menteeList,
                            it
                        )
                }
            }//else JobRecyclerAdapter(emptyList())
        }
    }
}