package com.tsic.ui.screen.mentor_bottom_menu.mychats

import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentor_api.MentorMyMenteeModel
import com.tsic.data.model.mentor_api.MentorMyStaffModel
import com.tsic.ui.screen.mentor_bottom_menu.mychats.my_mentee_list.MentorMyMenteeChatListActivity
import com.tsic.ui.screen.mentor_bottom_menu.mychats.my_mentee_list.MentorMyMenteeListAdapter
import com.tsic.ui.screen.mentor_bottom_menu.mychats.my_staff_list.MentorMyStaffChatListActivity
import com.tsic.ui.screen.mentor_bottom_menu.mychats.my_staff_list.MentorMyStaffListAdapter

object MentorMyChatsBinding {
    @JvmStatic
    @BindingAdapter(value = ["mentee_list", "fragment"], requireAll = true)
    fun RecyclerView.loadFiles(
        menteeList: List<MentorMyMenteeModel>?,
        fragment: MentorMyMenteeChatListActivity?
    ) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            fragment?.let {
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

    @JvmStatic
    @BindingAdapter(value = ["staff_list", "fragment"], requireAll = true)
    fun RecyclerView.loadFiles(
        staffList: List<MentorMyStaffModel>?,
        fragment: MentorMyStaffChatListActivity?
    ) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            fragment?.let {
                if (!staffList.isNullOrEmpty()) {
                    adapter =
                        MentorMyStaffListAdapter(
                            staffList,
                            it
                        )
                }
            }//else JobRecyclerAdapter(emptyList())
        }
    }
}