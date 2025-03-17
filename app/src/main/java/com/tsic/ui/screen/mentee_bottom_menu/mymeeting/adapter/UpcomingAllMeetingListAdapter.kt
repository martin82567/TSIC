package com.tsic.ui.screen.mentee_bottom_menu.mymeeting.adapter

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentee_api.MenteeAllList
import com.tsic.databinding.InflaterAllUpcomingMeetingListBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.MenteeMyMeetingActivity
import com.tsic.ui.screen.videocallscreen.VideoCallActivity
import org.jetbrains.anko.startActivity

class UpcomingAllMeetingListAdapter(
    val listMeeting: List<MenteeAllList.Data.Upcoming?>,
    val activity: MenteeMyMeetingActivity
) :
    BaseRecyclerAdapter<MenteeAllList.Data.Upcoming?>(listMeeting) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {

        return MeetingViewHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_all_upcoming_meeting_list,
                parent,
                false
            )
        )
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) =
        (holder as MeetingViewHolder).bind(list[position])

    inner class MeetingViewHolder(val binding: InflaterAllUpcomingMeetingListBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: MenteeAllList.Data.Upcoming?) {


            binding.model = item
            binding.tvMentorLast.text =
                if (item?.schoolName == "") "Affiliate Office" else item?.schoolName
            binding.executePendingBindings()
            binding?.btnVideo?.setOnClickListener {
                activity?.startActivity<VideoCallActivity>(
                    "receiver_id" to item?.mentor_id.toString(),
                    "call_from" to "Web Call"
                )

                /*fragment?.activity?.startActivity<VideoCallActivity>(
                    "receiver_id" to item?.createdBy.toString()
                )*/
            }
        }
    }

}