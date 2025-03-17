package com.tsic.ui.screen.mentor_drawer_menu.meetings.past

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentor_api.MentorPastMeeting
import com.tsic.databinding.InflaterMentorPastMeetingListBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.mentor_bottom_menu.mysessions.autofilladdsession.MentorAutoFillAddSessionActivity
import org.jetbrains.anko.startActivity
import java.text.SimpleDateFormat


class MentorPastMeetingListAdapter(
    val listMeeting: List<MentorPastMeeting?>,
    val fragment: MentorPastMeetingListFrag
) :
    BaseRecyclerAdapter<MentorPastMeeting?>(listMeeting) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {

        return MeetingViewHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_mentor_past_meeting_list,
                parent,
                false
            )
        )
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) =
        (holder as MeetingViewHolder).bind(list[position])

    inner class MeetingViewHolder(val binding: InflaterMentorPastMeetingListBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: MentorPastMeeting?) {


            binding.model = item
            if (item?.status ?: 0 == 3 /*|| item?.status ?: 0 == 0*/) {
                binding.imgCancelled?.visibility = View.VISIBLE
            }
            binding?.tvMentorTime?.text =
                SimpleDateFormat("hh:mm a").format(SimpleDateFormat("H:mm").parse(item?.time))

            if (item?.description != "")
                binding.descLayout.visibility = View.VISIBLE
            else
                binding.descLayout.visibility = View.GONE

            binding.tvMentorPastMeeting.text =
                if (item?.schoolName == "") item?.school_type else item?.schoolName
            if (item?.isLogged == "1")
                binding?.btnAddLog?.visibility = View.GONE
            else
                binding?.btnAddLog?.visibility = View.VISIBLE
            binding?.btnAddLog?.setOnClickListener {

                it.context.startActivity<MentorAutoFillAddSessionActivity>(
                    "agenda_name" to item?.title,
                    "description" to item?.description,
                    "mentee_name" to "${item?.mentees?.get(0)?.firstname} ${item?.mentees?.get(0)?.middlename} ${
                        item?.mentees?.get(
                            0
                        )?.lastname
                    }",
                    "mentee_id" to item?.mentees?.get(0)?.id.toString(),
                    "meeting_id" to item?.id?.toString(),
                    "date" to item?.date,
                    "session_method_location" to item?.methodValue,
                    "session_method_location_id" to item?.sessionMethodLocationId.toString(),
                )
            }


        }
    }

}