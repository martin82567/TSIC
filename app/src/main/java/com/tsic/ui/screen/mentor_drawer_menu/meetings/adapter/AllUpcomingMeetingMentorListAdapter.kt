package com.tsic.ui.screen.mentor_drawer_menu.meetings.adapter

import android.os.Build
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.annotation.RequiresApi
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentor_api.MentorAllListMeeting
import com.tsic.databinding.InflaterAllUpcomingMeetingMentorListBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.mentor_drawer_menu.meetings.MentorMyMeetingActivity
import com.tsic.ui.screen.videocallscreen.VideoCallActivity
import org.jetbrains.anko.startActivity
import java.text.SimpleDateFormat

class AllUpcomingMeetingMentorListAdapter(
    val listMeeting: List<MentorAllListMeeting.Data.Upcoming?>,
    val activity: MentorMyMeetingActivity
) :
    BaseRecyclerAdapter<MentorAllListMeeting.Data.Upcoming?>(listMeeting) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {

        return MeetingViewHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_all_upcoming_meeting_mentor_list,
                parent,
                false
            )
        )
    }

    @RequiresApi(Build.VERSION_CODES.O)
    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) =
        (holder as MeetingViewHolder).bind(list[position])

    inner class MeetingViewHolder(val binding: InflaterAllUpcomingMeetingMentorListBinding) :
        RecyclerView.ViewHolder(binding.root) {
        @RequiresApi(Build.VERSION_CODES.O)
        fun bind(item: MentorAllListMeeting.Data.Upcoming?) {


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
                activity?.startActivity<VideoCallActivity>(
                    "receiver_id" to (item?.mentees?.get(0)?.id ?: 0).toString(),
                    "call_from" to "Web Call"
                )
            }
            binding.btnDelete.setOnClickListener {
                activity.binding?.vm?.deleteSession(item?.id.toString())
            }

        }
    }

}
