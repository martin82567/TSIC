package com.tsic.ui.screen.mentee_bottom_menu.mymeeting.upcoming

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentee_api.UpcomingMeetingResponse
import com.tsic.databinding.InflaterUpcomingMeetingListBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.videocallscreen.VideoCallActivity
import org.jetbrains.anko.startActivity
import org.jetbrains.anko.support.v4.toast

class UpcomingMeetingListAdapter(
    val listMeeting: List<UpcomingMeetingResponse?>,
    val fragment: MenteeUpcomingMeetingListFrag
) :
    BaseRecyclerAdapter<UpcomingMeetingResponse?>(listMeeting) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {

        return MeetingViewHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_upcoming_meeting_list,
                parent,
                false
            )
        )
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) =
        (holder as MeetingViewHolder).bind(list[position])

    inner class MeetingViewHolder(val binding: InflaterUpcomingMeetingListBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: UpcomingMeetingResponse?) {


            binding.model = item
            binding.tvMentorLast.text =
                if (item?.school_name == "") "Affiliate Office" else item?.school_name
            binding.executePendingBindings()
            binding?.btnVideo?.setOnClickListener {
                fragment?.activity?.startActivity<VideoCallActivity>(
                    "receiver_id" to item?.createdBy.toString(),
                    "call_from" to "Web Call"
                )

            }

            /*fragment?.activity?.startActivity<VideoCallActivity>(
                "receiver_id" to item?.createdBy.toString()
            )*/
        }
    }
}

