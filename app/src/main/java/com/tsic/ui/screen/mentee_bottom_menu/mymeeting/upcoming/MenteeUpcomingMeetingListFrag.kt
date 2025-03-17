package com.tsic.ui.screen.mentee_bottom_menu.mymeeting.upcoming


import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.fragment.app.Fragment
import com.tsic.R
import com.tsic.databinding.FragmentMenteeUpcomingMeetingListBinding
import org.jetbrains.anko.support.v4.toast

/**
 * A simple [Fragment] subclass.
 */
class MenteeUpcomingMeetingListFrag : Fragment() {
    var binding: FragmentMenteeUpcomingMeetingListBinding? = null

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        binding = DataBindingUtil.inflate(
            inflater,
            R.layout.fragment_mentee_upcoming_meeting_list,
            container,
            false
        )
        binding?.fragment = this
        binding?.vm = UpcomingMeetingListViewModel(this)

        // Inflate the layout for this fragment
        return binding!!.root
    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.swipeRefreshLayout?.isRefreshing = yes
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        binding?.apply {
            swipeRefreshLayout?.setOnRefreshListener {
                vm?.fetchMenteeUpMeetingList()
            }
        }
    }

    override fun onResume() {
        super.onResume()
        binding?.vm?.fetchMenteeUpMeetingList()
    }

    fun showToast(msg: String?) {
        msg?.let { toast(it).show() }
    }
}

