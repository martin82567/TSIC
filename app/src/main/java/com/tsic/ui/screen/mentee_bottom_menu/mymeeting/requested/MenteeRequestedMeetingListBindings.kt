package com.tsic.ui.screen.mentee_bottom_menu.mymeeting.requested


import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentee_api.RequestedMenteeMeeting


object MenteeRequestedMeetingListBindings {
    @JvmStatic
    @BindingAdapter(value = ["past_meeting", "fragment", "vm"], requireAll = true)
    fun RecyclerView.loadFiles(
        listPastMeeting: List<RequestedMenteeMeeting?>,
        fragment: MenteeRequestedMeetingListFrag,
        vm: MenteeRequestedMeetingListViewModel
    ) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            if (!listPastMeeting.isNullOrEmpty()) {
                adapter = MenteeRequestedMeetingListAdapter(
                    listPastMeeting,
                    fragment,
                    vm
                ) //else JobRecyclerAdapter(emptyList()
            }
        }
    }
}