package com.tsic.ui.screen.mentor_drawer_menu.meetings.upcoming


import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.fragment.app.Fragment
import com.tsic.R
import com.tsic.databinding.FragmentMentorUpcomingMeetingListBinding
import org.jetbrains.anko.support.v4.toast

/**
 * A simple [Fragment] subclass.
 */
class MentorUpcomingMeetingListFrag : Fragment() {
    var binding: FragmentMentorUpcomingMeetingListBinding? = null

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        binding = DataBindingUtil.inflate(
            inflater,
            R.layout.fragment_mentor_upcoming_meeting_list,
            container,
            false
        )
        binding?.fragment = this
        binding?.vm = UpcomingMeetingMentorListViewModel(this)

        // Inflate the layout for this fragment
        return binding!!.root
    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.swipeRefreshLayout?.isRefreshing = yes
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        binding?.apply {
            vm?.fetchMentorUpMeetingList()

            swipeRefreshLayout?.setOnRefreshListener {
                vm?.fetchMentorUpMeetingList()
            }
        }
    }

    fun showToast(msg: String?) {
        msg?.let { toast(it).show() }
    }
}

