package com.tsic.ui.screen.mentee_bottom_menu.mymeeting

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.material.bottomsheet.BottomSheetDialogFragment
import com.tsic.R
import com.tsic.data.model.mentee_api.MenteeAllList
import com.tsic.databinding.FragmentBottomSheetBinding
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.adapter.MenteeAllRequestedMeetingListAdapter
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.adapter.UpcomingAllMeetingListAdapter

class MenteeModalBottomSheet (
) : BottomSheetDialogFragment() {
     var upcomingData: ArrayList<MenteeAllList.Data.Upcoming?>? = null
     var requestedData: ArrayList<MenteeAllList.Data.Requested?>? = null
    lateinit var activity: MenteeMyMeetingActivity
    lateinit var binding: FragmentBottomSheetBinding
    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View?  {
        binding = DataBindingUtil.inflate<FragmentBottomSheetBinding>(inflater,
            R.layout.fragment_bottom_sheet,container,false)
        return binding.layout
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        if(upcomingData == null) {
            binding.upcomingMeetingLayout.visibility = View.GONE
        }
        if(requestedData == null) {
            binding.requestedMeetingLayout.visibility = View.GONE
        }
        if(upcomingData != null) {
            binding.tvAwaitingSessionOccurrence.visibility = View.VISIBLE
        }
        if(requestedData!=null){
            binding.tvAwaitingMenteeConfirmation.setText("Awaiting My Confirmation")
            binding.tvAwaitingMenteeConfirmation.visibility = View.VISIBLE

        }

        binding?.pastMeetingLayout?.visibility=View.GONE
        binding?.rvAwaitingSessionOccurrenceBottomView?.apply {
            layoutManager = LinearLayoutManager(
                getActivity(),
                LinearLayoutManager.HORIZONTAL,
                false
            )


            adapter =
                upcomingData?.let {
                    UpcomingAllMeetingListAdapter(
                        it,
                        activity
                    )
                }
        }

        binding?.rvAwaitingMenteeConfirmationBottomView?.apply {
            layoutManager = LinearLayoutManager(
                getActivity(),
                LinearLayoutManager.HORIZONTAL,
                false
            )
            setHasFixedSize(true)
            setItemViewCacheSize(50)
            adapter = requestedData?.let {
                if (it.size!=0) {

                }
                MenteeAllRequestedMeetingListAdapter(it, activity)
            }
        }


    }
    companion object {
        const val TAG = "ModalBottomSheet"
        fun newInstance(
            upComingData: ArrayList<MenteeAllList.Data.Upcoming?>?,
            requestedData: ArrayList<MenteeAllList.Data.Requested?>?,
            activity: MenteeMyMeetingActivity
        ): MenteeModalBottomSheet {
            val fragment = MenteeModalBottomSheet()
            fragment.upcomingData = upComingData
            fragment.requestedData = requestedData
            fragment.activity = activity
            return fragment
        }
    }

}