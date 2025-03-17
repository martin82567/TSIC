package com.tsic.ui.screen.mentee_bottom_menu.mymeeting.past


import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentee_api.Meeting


object MenteePastMeetingListBindings {
    @JvmStatic
    @BindingAdapter(value = ["past_meeting", "fragment"], requireAll = true)
    fun RecyclerView.loadFiles(
        listPastMeeting: List<Meeting?>,
        fragment: MenteePastMeetingListFrag
    ) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            if (!listPastMeeting.isNullOrEmpty()) {
                adapter = MenteePastMeetingListAdapter(
                    listPastMeeting,
                    fragment
                ) //else JobRecyclerAdapter(emptyList()
            }
        }
    }
}