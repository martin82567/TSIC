package com.tsic.ui.screen.mentee_bottom_menu.mychats.my_staff_list

import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentee_api.MyStaffDetails

object MenteeMyChatsBinding {
    @JvmStatic
    @BindingAdapter(value = ["list_staff", "activity"], requireAll = true)
    fun RecyclerView.loadStaff(
        listStaff: List<MyStaffDetails>?,
        activity: MenteeMyStaffListActivity
    ) {
        this.apply {
            if (!listStaff.isNullOrEmpty()) {
                adapter =
                    MenteeMyStaffListAdapter(
                        listStaff,
                        activity
                    )
            }
        }
    }


}