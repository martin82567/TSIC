package com.tsic.ui.screen.mentee_bottom_menu.mymeeting.requested


import android.Manifest
import android.content.pm.PackageManager
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.app.ActivityCompat
import androidx.core.content.ContextCompat
import androidx.databinding.DataBindingUtil
import androidx.fragment.app.Fragment
import com.tsic.databinding.FragmentMenteeRequestedMeetingListBinding
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.MenteeMyMeetingActivity
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.detaillist.MenteeMyAllMeetingActivity
import org.jetbrains.anko.support.v4.toast


/**
 * A simple [Fragment] subclass.
 */
class MenteeRequestedMeetingListFrag() : Fragment() {
    var activity: MenteeMyAllMeetingActivity? = null

    constructor(_activity: MenteeMyAllMeetingActivity) : this() {
        activity = _activity
    }

    var binding: FragmentMenteeRequestedMeetingListBinding? = null
    var date: String = ""
    var time: String = ""
    var title: String = ""
    var description: String = ""
    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {


        binding = DataBindingUtil.inflate(
            inflater,
            com.tsic.R.layout.fragment_mentee_requested_meeting_list,
            container,
            false
        )
        binding?.fragment = this
        binding?.vm = MenteeRequestedMeetingListViewModel(this)


        // Inflate the layout for this fragment
        return binding!!.root
    }

    fun isBusyLoadingData(yes: Boolean) {

        binding?.swipeRefreshLayout?.isRefreshing = yes

    }
    /*fun dismissKeyboard(){
        activity?.dismiss()
    }*/

    fun calendarPermission() {
        if (ActivityCompat.checkSelfPermission(
                this@MenteeRequestedMeetingListFrag.context!!,
                Manifest.permission.WRITE_CALENDAR
            ) != PackageManager.PERMISSION_GRANTED
        ) {
            ActivityCompat.requestPermissions(
                this@MenteeRequestedMeetingListFrag.activity!!,
                arrayOf(Manifest.permission.WRITE_CALENDAR), 1
            )
        } else {
            binding?.vm?.calenderReminder(date, time, title, description)
        }
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        binding?.apply {
            swipeRefreshLayout?.setOnRefreshListener {
                vm?.fetchMeetingList()
            }

        }
    }

    override fun onRequestPermissionsResult(
        requestCode: Int, permissions: Array<String>,
        grantResults: IntArray
    ) {
        when (requestCode) {
            1 -> {
                if (grantResults.isNotEmpty() && grantResults[0] ==
                    PackageManager.PERMISSION_GRANTED
                ) {
                    if ((ContextCompat.checkSelfPermission(
                            this@MenteeRequestedMeetingListFrag.activity!!,
                            Manifest.permission.WRITE_CALENDAR
                        ) ==
                                PackageManager.PERMISSION_GRANTED)
                    ) {
                        binding?.vm?.calenderReminder(date, time, title, description)
                    }
                }
                return
            }
        }
    }

    override fun onResume() {
        super.onResume()
        binding?.vm?.fetchMeetingList()
    }

    fun showToast(msg: String?) {
        msg?.let { toast(it).show() }
    }
}

