package com.tsic.ui.screen.mentor_drawer_menu.meetings.requested

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.mentor_api.MentorPastMeeting
import com.tsic.data.remote.api.MentorApiService
import com.tsic.databinding.InflaterRequestedMeetingMentorListBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.mentor_drawer_menu.meetings.requested.editMeeting.MentorEditMeetingActivity
import com.tsic.util.INTENT_KEY_MEETING_MODEL
import org.jetbrains.anko.startActivity
import java.text.SimpleDateFormat


class MentorRequestedMeetingListAdapter(
    val listMeeting: List<MentorPastMeeting?>,
    val fragment: MentorRequestedMeetingListFrag
) :
    BaseRecyclerAdapter<MentorPastMeeting?>(listMeeting) {


    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(fragment.context!!, USER_PREF)
    }


    private val apiService by lazy { MentorApiService.create() }


    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {

        return MeetingViewHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_requested_meeting_mentor_list,
                parent,
                false
            )
        )
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) =
        (holder as MeetingViewHolder).bind(list[position])

    inner class MeetingViewHolder(val binding: InflaterRequestedMeetingMentorListBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: MentorPastMeeting?) {


            binding.model = item
            binding?.tvMentorLastLastsession?.text = SimpleDateFormat("hh:mm a").format(
                SimpleDateFormat("H:mm").parse(
                    item?.time
                )
            )
            if (item?.meetingRequests != "") {
                binding.apply {
                    btnCancel.visibility = View.VISIBLE
                    btnDeny.visibility = View.VISIBLE
                    noteLayout.visibility = View.VISIBLE
                    btnReschedule.text = "RESCHEDULE"
                }
            } else {
                binding.apply {
                    btnCancel.visibility = View.GONE
                    btnDeny.visibility = View.GONE
                }
            }
            if (item?.status == 3) {
                binding.statusCancelled.visibility = View.VISIBLE
                binding.layoutButton.visibility = View.GONE
            } else {
                binding.statusCancelled.visibility = View.GONE
                binding.layoutButton.visibility = View.VISIBLE
            }
            if (item?.description != "")
                binding.descLayout.visibility = View.VISIBLE
            else
                binding.descLayout.visibility = View.GONE
            binding.btnCancel.setOnClickListener {
                fragment.apply {
                    title = item?.title.toString()
                    time = "${item?.date} ${item?.time}"
                }
                fragment.binding?.vm?.cancelMeeting(item?.id.toString())
            }
            binding.btnDeny.setOnClickListener {
                fragment.binding?.vm?.noRescheduleMeeting(item?.id.toString())
            }
            binding.tvMentorSchool.text =
                if (item?.schoolName == "") item?.school_type else item?.schoolName
            binding.btnReschedule.setOnClickListener {
                fragment.activity?.startActivity<MentorEditMeetingActivity>(
                    INTENT_KEY_MEETING_MODEL to item
                )
            }


        }
    }

}