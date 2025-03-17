package com.tsic.ui.screen.mentee_bottom_menu.mychats.my_mentor_list

import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentee_api.MyMentorDetails

object MenteeMyMentorListBinding {
    @JvmStatic
    @BindingAdapter(value = ["list_mentor", "activity"], requireAll = true)
    fun RecyclerView.loadStaff(
        listStaff: List<MyMentorDetails>?,
        activity: MenteeMyMentorListActivity
    ) {
        this.apply {
            if (!listStaff.isNullOrEmpty()) {
                adapter =
                    MenteeMyMentorListAdapter(
                        listStaff,
                        activity
                    )
            }
        }
    }


}