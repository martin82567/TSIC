package com.tsic.ui.screen.mentor_drawer_menu.meetings.requested


import android.Manifest
import android.content.ContentUris
import android.content.pm.PackageManager
import android.os.Bundle
import android.provider.CalendarContract
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.app.ActivityCompat
import androidx.core.content.ContextCompat
import androidx.databinding.DataBindingUtil
import androidx.fragment.app.Fragment
import com.tsic.R
import com.tsic.databinding.FragmentMentorRequestedMeetingListBinding
import com.tsic.ui.screen.mentor_drawer_menu.meetings.requested.addMeeting.MentorAddMeetingActivity
import org.jetbrains.anko.support.v4.startActivity
import org.jetbrains.anko.support.v4.toast
import java.text.DateFormat
import java.text.SimpleDateFormat
import java.util.*

/**
 * A simple [Fragment] subclass.
 */
class MentorRequestedMeetingListFrag : Fragment() {
    var binding: FragmentMentorRequestedMeetingListBinding? = null
    var time: String = ""
    var title: String = ""
    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        binding = DataBindingUtil.inflate(
            inflater,
            R.layout.fragment_mentor_requested_meeting_list,
            container,
            false
        )
        binding?.fragment = this
        binding?.vm = RequestedMeetingMentorListViewModel(this)

        // Inflate the layout for this fragment
        return binding!!.root
    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.swipeRefreshLayout?.apply {
            setProgressViewOffset(true, 100, 200)
            isRefreshing = yes
            binding?.rVMentee?.visibility =
                if (yes) View.INVISIBLE else View.VISIBLE
        }
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        binding?.apply {
            vm?.fetchMentorRequestedList()
            swipeRefreshLayout.setOnRefreshListener {
                vm?.fetchMentorRequestedList()
            }

//            fragment?.binding?.btnAddMeeting?.setOnClickListener {
//                startActivity<MentorAddMeetingActivity>()
//            }
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
                            this@MentorRequestedMeetingListFrag.activity!!,
                            Manifest.permission.WRITE_CALENDAR
                        ) ===
                                PackageManager.PERMISSION_GRANTED)
                    ) deleteReminder()


                }
                return
            }
        }
    }

    override fun onResume() {
        super.onResume()
        binding?.vm?.fetchMentorRequestedList()
    }

    fun deleteReminder() {
        val eventIdList1 = getEventIdList().filter {
            it.title == title
        }
        eventIdList1
        val eventIdList = eventIdList1.filter {
            it.dtStart == time
        }
        eventIdList
        if (eventIdList.isNotEmpty())
            this@MentorRequestedMeetingListFrag.activity!!.contentResolver.delete(
                ContentUris.withAppendedId(
                    CalendarContract.Events.CONTENT_URI,
                    eventIdList[0].eventId
                ), null, null
            )
    }

    fun getEventIdList(): ArrayList<AllReminders> {
        val eventIdList = ArrayList<AllReminders>()

        val EVENT_PROJECTION: Array<String> = arrayOf(
            CalendarContract.Events._ID, // 0
            CalendarContract.Events.TITLE,  // 1
            CalendarContract.Events.DTSTART  //2
        )
        val PROJECTION_EVENT_ID_INDEX: Int = 0
        val PROJECTION_TITLE_INDEX: Int = 1
        val PROJECTION_DTSTART_INDEX: Int = 2

        if (ActivityCompat.checkSelfPermission(
                this@MentorRequestedMeetingListFrag.activity!!,
                Manifest.permission.READ_CALENDAR
            ) != PackageManager.PERMISSION_GRANTED
        ) return eventIdList

        this@MentorRequestedMeetingListFrag.activity!!.contentResolver.query(
            CalendarContract.Events.CONTENT_URI,
            EVENT_PROJECTION,
            "",
            arrayOf(),
            null
        )?.let {
            while (it.moveToNext() ?: false) {
                // Get the field values
                val eventId = it.getLong(PROJECTION_EVENT_ID_INDEX)
                val title = it.getString(PROJECTION_TITLE_INDEX)
                val dtStart = it.getString(PROJECTION_DTSTART_INDEX)
                val formatter: DateFormat = SimpleDateFormat("MM-dd-yyyy HH:mm:00")
                val milliSeconds = dtStart?.toLong() ?: 0L

                val calendar = Calendar.getInstance()
                calendar.timeInMillis = milliSeconds
                val v = formatter.format(calendar.time)
                eventIdList.add(AllReminders(eventId, title, v))
            }
        }
        return eventIdList
    }

    data class AllReminders(
        val eventId: Long,
        val title: String?,
        val dtStart: String?
    )

    fun showToast(msg: String?) {
        msg?.let { toast(it).show() }
    }
}

