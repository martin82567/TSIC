package com.tsic.ui.screen.mentor_drawer_menu.meetings.upcoming

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentor_api.AcceptedMeeting
import com.tsic.databinding.InflaterUpcomingMeetingMentorListBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.videocallscreen.VideoCallActivity
import org.jetbrains.anko.startActivity
import org.jetbrains.anko.support.v4.selector
import java.text.SimpleDateFormat

class UpcomingMeetingMentorListAdapter(
    val listMeeting: List<AcceptedMeeting?>,
    val fragment: MentorUpcomingMeetingListFrag
) :
    BaseRecyclerAdapter<AcceptedMeeting?>(listMeeting) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {

        return MeetingViewHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_upcoming_meeting_mentor_list,
                parent,
                false
            )
        )
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) =
        (holder as MeetingViewHolder).bind(list[position])

    inner class MeetingViewHolder(val binding: InflaterUpcomingMeetingMentorListBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: AcceptedMeeting?) {


            binding.model = item
            binding?.tvMentorTime?.text = SimpleDateFormat("hh:mm a").format(
                SimpleDateFormat("H:mm").parse(
                    item?.time
                )
            )
            if (item?.description != "")
                binding.descLayout.visibility = View.VISIBLE
            else
                binding.descLayout.visibility = View.GONE
            binding.tvMentorSchool.text =
                if (item?.schoolName == "") "Affiliate Office" else item?.schoolName
            binding?.btnVideo?.setOnClickListener {

                    fragment?.activity?.startActivity<VideoCallActivity>(
                        "receiver_id" to (item?.mentees?.get(0)?.id ?: 0).toString(),
                        "call_from" to "Web Call"
                    )

                /*fragment?.activity?.startActivity<VideoCallActivity>(
                    "receiver_id" to (item?.mentees?.get(0)?.id ?: 0).toString()
                )*/
            }

        }
    }

}
