package com.tsic.ui.screen.mentee_bottom_menu.mymeeting.upcoming


import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentee_api.UpcomingMeetingResponse


object MenteeUpcomingMeetingListBindings {
    @JvmStatic
    @BindingAdapter(value = ["upcoming_meeting", "fragment"], requireAll = true)
    fun RecyclerView.loadFiles(
        listUpcomingMeeting: List<UpcomingMeetingResponse?>,
        fragment: MenteeUpcomingMeetingListFrag
    ) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context) as RecyclerView.LayoutManager?
            if (!listUpcomingMeeting.isNullOrEmpty()) {
                adapter = UpcomingMeetingListAdapter(
                    listUpcomingMeeting,
                    fragment
                ) //else JobRecyclerAdapter(emptyList()
            }
        }
    }
}