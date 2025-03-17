package com.tsic.ui.screen.mentee_bottom_menu.mymeeting.past

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentee_api.Meeting
import com.tsic.databinding.InflaterMenteePastMeetingListBinding
import com.tsic.ui.base.BaseRecyclerAdapter


class MenteePastMeetingListAdapter(
    val listMeeting: List<Meeting?>,
    val fragment: MenteePastMeetingListFrag
) :
    BaseRecyclerAdapter<Meeting?>(listMeeting) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {

        return MeetingViewHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_mentee_past_meeting_list,
                parent,
                false
            )
        )
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) =
        (holder as MeetingViewHolder).bind(list[position])

    inner class MeetingViewHolder(val binding: InflaterMenteePastMeetingListBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: Meeting?) {


            binding.model = item

            binding.tvMentorLastSession.text =
                if (item?.school_name == "") "Affiliate Office" else item?.school_name
            binding.executePendingBindings()
        }
    }

}