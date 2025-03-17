package com.tsic.ui.screen.mentor_drawer_menu.meetings.requested.editMeeting

import android.Manifest
import android.app.DatePickerDialog
import android.app.DatePickerDialog.OnDateSetListener
import android.app.TimePickerDialog
import android.content.pm.PackageManager
import android.os.Bundle
import android.provider.CalendarContract
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.core.app.ActivityCompat
import androidx.core.content.ContextCompat
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.data.local.prefs.KEY_TIMEZONE_OFFSET
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.mentor_api.MentorPastMeeting
import com.tsic.databinding.ActivityEditMeetingBinding
import com.tsic.util.INTENT_KEY_MEETING_MODEL
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.toast
import java.text.DateFormat
import java.text.SimpleDateFormat
import java.util.*


class MentorEditMeetingActivity : AppCompatActivity() {
    val userPrefs by lazy { PreferenceHelper.customPrefs(this, USER_PREF) }
    val timeZoneOffset by lazy { userPrefs?.getString(KEY_TIMEZONE_OFFSET, "") }
    val timeZoneStr by lazy { TimeZone.getTimeZone("GMT$timeZoneOffset") }
    val meetingIntent by lazy {
        intent?.getParcelableExtra<MentorPastMeeting>(
            INTENT_KEY_MEETING_MODEL
        )
    }


    //declarations
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityEditMeetingBinding>(
            this,
            R.layout.activity_edit_meeting
        )
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()
    }
    //methods


    private fun initUiAndListeners() {
        binding.vm = MentorEditMeetingViewModel(this)
        setSupportActionBar(binding.toolbar)
        setStatusBarColor(R.color.colorStatusTranslucentGreen)

        supportActionBar?.apply {
            title = "Schedule A Session"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }

        meetingIntent?.let {
            loadMeetingData(it)
        }

        binding?.contentLayout?.apply {
            rescheduleSession.setOnClickListener {
                meetingIntent?.let { mIntent ->
                    binding?.vm?.rescheduleMeeting(
                        mIntent.mentees?.get(0)?.id.toString(),
                        mIntent.id.toString()
                    )
                }
            }

            sessionDate.setOnClickListener {
                showDatePicker(it)
            }

            sessionTimeFrom.setOnClickListener {
                showTimeFrom(it)
            }
        }
    }

    private fun loadMeetingData(model: MentorPastMeeting) {
        binding?.vm?.apply {
            if (model.mentees?.isNullOrEmpty() == false) {
                menteeName.set("${model.mentees[0]!!.firstname} ${model.mentees[0]!!.lastname}")
            }
            schoolLocation.set(if (model.schoolName?.length == 0) model.school_type else model.schoolName)
            title.set(model.title)
            description.set(model.description)
            schoolSpace.set(model.school_location)
            methodLocation.set(model.methodValue)
            date.set(model.date)
            time.set(model.time?.substringBeforeLast(':'))
            time12h.set(
                SimpleDateFormat("hh:mm a").format(
                    SimpleDateFormat("H:mm").parse(
                        time.get()
                    )
                )
            )
        }
    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.progressBar?.visibility = if (yes) View.VISIBLE else View.INVISIBLE
    }


    fun showToast(msg: String) {
        toast(msg)
    }

    override fun onPause() {
        super.onPause()
        binding?.vm?.onPause()
    }

    override fun onStop() {
        super.onStop()
        binding?.vm?.onStop()
    }

    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
    }


    fun showDatePicker(view: View) {
        val cal = Calendar.getInstance(timeZoneStr, Locale.US)
        if (binding?.vm?.date?.get()?.toString()?.isNotBlank() == true) {
            val splits = binding?.vm?.date?.get()?.toString()?.split('-') ?: emptyList()
            if (splits.isNotEmpty()) {
                val month = splits[0].toInt()
                val date = splits[1].toInt()
                val yr = splits[2].toInt()
                cal.set(yr, month - 1, date)
            }
        }


        val datePickerDialog = DatePickerDialog(
            this,
            OnDateSetListener { v, year, monthOfYear, dayOfMonth ->
                binding?.vm?.date?.set((monthOfYear + 1).toString() + "-" + dayOfMonth.toString() + "-" + (year))
            }, cal.get(Calendar.YEAR), cal.get(Calendar.MONTH), cal.get(Calendar.DAY_OF_MONTH)
        )

        datePickerDialog.datePicker.minDate =
            Calendar.getInstance(timeZoneStr, Locale.US).timeInMillis
        datePickerDialog.show()
    }

    fun showTimeFrom(view: View) {
        if (binding?.vm?.date?.get()?.isBlank() == true) {
            showToast("Please select a date first")
            return
        }
        val calSelectedDate = Calendar.getInstance(timeZoneStr, Locale.US)
        val splitsDate = binding?.vm?.date?.get()?.toString()?.split('-') ?: emptyList()
        if (splitsDate.isNotEmpty()) {
            val month = splitsDate[0].toInt()
            val date = splitsDate[1].toInt()
            val yr = splitsDate[2].toInt()
            calSelectedDate.set(yr, month - 1, date)
        }

        val cal = Calendar.getInstance(timeZoneStr, Locale.US)
        if (binding?.vm?.time?.get()?.toString()?.isNotBlank() == true) {
            val splits = binding?.vm?.time?.get()?.toString()?.split(':') ?: emptyList()
            if (splits.isNotEmpty()) {
                val hr = splits[0].toInt()
                val mins = splits[1].toInt()
                cal.set(Calendar.HOUR_OF_DAY, hr)
                cal.set(Calendar.MINUTE, mins)
            }
        }

        val t = calSelectedDate.get(Calendar.DAY_OF_YEAR)
        val tt = cal.get(Calendar.DAY_OF_YEAR)
        val isTodaySelected = t == tt

        val timePickerDialog = TimePickerDialog(
            this,
            TimePickerDialog.OnTimeSetListener { v, hourOfDay, minute ->
                if (isTodaySelected && hourOfDay <= cal.get(Calendar.HOUR_OF_DAY) && minute <= cal.get(
                        Calendar.MINUTE
                    )
                )
                    showToast("Meeting can't be schedules for a time which is already passed")
                else {
                    binding?.vm?.time?.set("$hourOfDay:${if (minute <= 9) "0$minute" else minute}")
                    binding?.vm?.time12h?.set(
                        SimpleDateFormat("hh:mm a").format(
                            SimpleDateFormat("H:mm").parse(
                                "$hourOfDay:${if (minute <= 9) "0$minute" else minute}"
                            )
                        )
                    )
                }
            },
            cal.get(Calendar.HOUR_OF_DAY), cal.get(Calendar.MINUTE), false
        )
        timePickerDialog.show()

    }

    fun calendarPermission() {
        if (ActivityCompat.checkSelfPermission(
                this,
                Manifest.permission.READ_CALENDAR
            ) != PackageManager.PERMISSION_GRANTED
        ) {
            ActivityCompat.requestPermissions(
                this@MentorEditMeetingActivity,
                arrayOf(Manifest.permission.READ_CALENDAR), 1
            )
        } else {
            meetingIntent?.let {
                deleteReminder(
                    it.title,
                    "${it.date} ${it.time}"
                )
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
                            this@MentorEditMeetingActivity,
                            Manifest.permission.READ_CALENDAR
                        ) ==
                                PackageManager.PERMISSION_GRANTED)
                    ) {
                        meetingIntent?.let {
                            deleteReminder(
                                it.title,
                                "${it.date} ${it.time}"
                            )
                        }
                    }
                }
                return
            }
        }
    }

    private fun deleteReminder(title: String?, time: String?) {
        val eventIdList1 = getEventIdList()/*.filter {
            it.title == title
        }*/
        val m = mutableListOf<AllReminders>()
        eventIdList1.forEach {
            if (it.title == title) {
                if (it.dtStart == time)
                    m.add(it)
            }
        }
        m
        val eventIdList = eventIdList1.filter {
            it.dtStart == time
        }
        eventIdList
        if (m.isNotEmpty())
            binding?.vm?.calenderReminder(
                m[0].eventId
            )
        else
            binding?.vm?.calenderReminder(-1L)
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
                this,
                Manifest.permission.READ_CALENDAR
            ) != PackageManager.PERMISSION_GRANTED
        )
            return eventIdList

        contentResolver.query(
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

}



