package com.tsic.ui.screen.mentor_bottom_menu.mychats.my_staff_list

import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentor_api.MentorMyStaffModel

object MentorMyStaffChatsBinding {

    @JvmStatic
    @BindingAdapter(value = ["staff_list", "activity"], requireAll = true)
    fun RecyclerView.loadFiles(
        staffList: List<MentorMyStaffModel>?,
        activity: MentorMyStaffChatListActivity?
    ) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            activity?.let {
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