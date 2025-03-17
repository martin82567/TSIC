package com.tsic.ui.screen.mentor_drawer_menu.meetings.view_session_log

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentor_api.MentorPastMeeting
import com.tsic.databinding.InflaterViewSessionLogListBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import java.text.SimpleDateFormat


class ViewSessionLogListAdapter(
    val listMeeting: List<MentorPastMeeting?>,
    val activity: ViewSessionLogActivity
) :
    BaseRecyclerAdapter<MentorPastMeeting?>(listMeeting) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {

        return MeetingViewHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_view_session_log_list,
                parent,
                false
            )
        )
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) =
        (holder as MeetingViewHolder).bind(position)

    inner class MeetingViewHolder(val binding: InflaterViewSessionLogListBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(position: Int) {

            val item: MentorPastMeeting? = list[position]
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

            if (position == list.size - 1) {
                activity.binding?.vm?.apply {
                    if (!isCalling) {
                        isCalling = true
                        currentPage++
                        fetchViewSessionLogList()
                    }
                }
            }
        }
    }

}