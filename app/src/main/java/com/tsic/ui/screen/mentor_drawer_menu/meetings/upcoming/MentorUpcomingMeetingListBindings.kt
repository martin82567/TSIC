package com.tsic.ui.screen.mentor_drawer_menu.meetings.upcoming


import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentor_api.AcceptedMeeting


object MentorUpcomingMeetingListBindings {
    @JvmStatic
    @BindingAdapter(value = ["upcoming_meeting", "fragment"], requireAll = true)
    fun RecyclerView.loadFiles(
        listUpcomingMeeting: List<AcceptedMeeting?>,
        fragment: MentorUpcomingMeetingListFrag
    ) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            if (!listUpcomingMeeting.isNullOrEmpty()) {
                adapter = UpcomingMeetingMentorListAdapter(
                    listUpcomingMeeting,
                    fragment
                ) //else JobRecyclerAdapter(emptyList()
            }
        }
    }
}
