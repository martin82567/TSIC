package com.tsic.ui.screen.mentor_drawer_menu.meetings.past


import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentor_api.MentorPastMeeting


object MentorPastMeetingListBindings {
    @JvmStatic
    @BindingAdapter(value = ["past_meeting", "fragment"], requireAll = true)
    fun RecyclerView.loadFiles(
        listPastMeeting: List<MentorPastMeeting?>,
        fragment: MentorPastMeetingListFrag
    ) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            if (!listPastMeeting.isNullOrEmpty()) {
                adapter = MentorPastMeetingListAdapter(
                    listPastMeeting,
                    fragment
                ) //else JobRecyclerAdapter(emptyList()
            }
        }
    }
}
