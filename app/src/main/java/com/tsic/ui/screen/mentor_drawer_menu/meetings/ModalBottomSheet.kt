package com.tsic.ui.screen.mentor_drawer_menu.meetings

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.bottomsheet.BottomSheetDialogFragment
import com.tsic.R
import com.tsic.data.model.mentor_api.MentorAllListMeeting
import com.tsic.data.model.mentor_api.MentorPastMeeting
import com.tsic.databinding.FragmentBottomSheetBinding
import com.tsic.ui.screen.mentor_drawer_menu.meetings.adapter.AllUpcomingMeetingMentorListAdapter
import com.tsic.ui.screen.mentor_drawer_menu.meetings.adapter.MentorAllPastMeetingListAdapter
import com.tsic.ui.screen.mentor_drawer_menu.meetings.adapter.MentorAllRequestedMeetingListAdapter
import java.util.*

class ModalBottomSheet(
    val pastData: ArrayList<MentorAllListMeeting.Data.Past?>?,
    val upcomingData: ArrayList<MentorAllListMeeting.Data.Upcoming?>?,
    val requestedData: ArrayList<MentorPastMeeting?>?,
    val activity: MentorMyMeetingActivity) : BottomSheetDialogFragment() {

    lateinit var binding: FragmentBottomSheetBinding

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View?  {
        binding = DataBindingUtil.inflate<FragmentBottomSheetBinding>(inflater,R.layout.fragment_bottom_sheet,container,false)
        return binding.layout
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        if(pastData == null) {
            binding.pastMeetingLayout.visibility = View.GONE
        }
        if(upcomingData == null) {
            binding.upcomingMeetingLayout.visibility = View.GONE
        }
        if(requestedData == null) {
            binding.requestedMeetingLayout.visibility = View.GONE
        }
        if(pastData != null) {
            binding.tvScheduledSessionPassed.visibility = View.VISIBLE
        }
        if(upcomingData != null) {
            binding.tvAwaitingSessionOccurrence.visibility = View.VISIBLE
        }
        if(requestedData != null) {
            binding.tvAwaitingMenteeConfirmation.visibility = View.VISIBLE
        }
        binding?.rvAwaitingSessionOccurrenceBottomView?.apply {
            layoutManager = LinearLayoutManager(
                activity,
                LinearLayoutManager.HORIZONTAL,
                false
            )


            adapter =
                upcomingData?.let {
//                    if (it.size!=0) {
//                        it.iterator().forEach { upcomingDateVsDataMap.put(
//                            LocalDate.parse(it?.scheduleTime,DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss")), it)}
//                        activity.binding?.contentLayout?.tvAwaitingSessionOccurrence?.visibility =
//                            View.GONE
//                        activity.binding?.contentLayout?.tvAwaitingSessionOccurrenceViewAll?.visibility =
//                            View.GONE
//                        activity.binding?.contentLayout?.rvAwaitingSessionOccurrence?.visibility =
//                            View.GONE
//                    }
                    AllUpcomingMeetingMentorListAdapter(
                        it,
                        activity
                    )
                }
        }


        binding?.rvScheduledSessionPassedBottomView?.apply {
            layoutManager = LinearLayoutManager(
                activity,
                LinearLayoutManager.HORIZONTAL,
                false
            )
            setHasFixedSize(true)
            setItemViewCacheSize(50)
            adapter = pastData?.let {
                if (it.size!=0) {
//                    it.iterator().forEach { pastDateVsDataMap.put(
//                        LocalDate.parse(it?.scheduleTime, DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss")), it) }
//                    activity.binding?.contentLayout?.tvScheduledSessionPassed?.visibility =
//                        View.GONE
//                    activity.binding?.contentLayout?.tvScheduledSessionPassedViewAll?.visibility =
//                        View.GONE
//                    activity.binding?.contentLayout?.rvScheduledSessionPassedViewAll?.visibility =
//                        View.GONE
                }
                MentorAllPastMeetingListAdapter(it)
            }
        }


        binding?.rvAwaitingMenteeConfirmationBottomView?.apply {
            layoutManager = LinearLayoutManager(
                activity,
                LinearLayoutManager.HORIZONTAL,
                false
            )
            setHasFixedSize(true)
            setItemViewCacheSize(50)
            adapter = requestedData?.let {
                if (it.size!=0) {
//                    it.iterator().forEach { pastDateVsDataMap.put(
//                        LocalDate.parse(it?.scheduleTime, DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss")), it) }
//                    activity.binding?.contentLayout?.tvScheduledSessionPassed?.visibility =
//                        View.GONE
//                    activity.binding?.contentLayout?.tvScheduledSessionPassedViewAll?.visibility =
//                        View.GONE
//                    activity.binding?.contentLayout?.rvScheduledSessionPassedViewAll?.visibility =
//                        View.GONE
                }
                MentorAllRequestedMeetingListAdapter(it, activity)
            }
        }


    }

    companion object {
        const val TAG = "ModalBottomSheet"
    }
}