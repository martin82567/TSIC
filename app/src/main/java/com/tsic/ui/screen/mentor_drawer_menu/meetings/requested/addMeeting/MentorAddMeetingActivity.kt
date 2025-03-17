package com.tsic.ui.screen.mentor_drawer_menu.meetings.requested.addMeeting

import android.Manifest
import android.app.DatePickerDialog
import android.app.DatePickerDialog.OnDateSetListener
import android.app.TimePickerDialog
import android.content.Intent
import android.content.pm.PackageManager
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.core.app.ActivityCompat
import androidx.core.content.ContextCompat
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.data.local.prefs.KEY_TIMEZONE_OFFSET
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.databinding.ActivityAddMeetingBinding
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.selector
import org.jetbrains.anko.toast
import java.text.SimpleDateFormat
import java.util.*



class MentorAddMeetingActivity : AppCompatActivity() {
    val userPrefs by lazy { PreferenceHelper.customPrefs(this, USER_PREF) }
    val timeZoneOffset by lazy { userPrefs?.getString(KEY_TIMEZONE_OFFSET, "") }
    val timeZoneStr by lazy { TimeZone.getTimeZone("GMT$timeZoneOffset") }


    //declarations
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityAddMeetingBinding>(
            this,
            R.layout.activity_add_meeting
        )
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()
    }
    //methods


    private fun initUiAndListeners() {
        binding.vm = MentorAddMeetingViewModel(this)
        setSupportActionBar(binding.toolbar)
        setStatusBarColor(R.color.colorStatusTranslucentGreen)

        supportActionBar?.apply {
            title = "Schedule A Session"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }
        binding?.contentLayout?.menteeName?.setOnClickListener {
            dismissKeyboard()
            menteeFetchList()
        }

        binding?.contentLayout?.schoolAdress?.setOnClickListener {
            dismissKeyboard()
            schoolList()
        }

        binding?.contentLayout?.sessionDate?.setOnClickListener {
            dismissKeyboard()
            showDatePicker(it)
        }

        binding?.contentLayout?.sessionTimeFrom?.setOnClickListener {
            dismissKeyboard()
            showTimeFrom(it)
        }
        binding?.contentLayout?.saveSession?.setOnClickListener {
            binding?.vm?.saveSession()
        }
        binding?.contentLayout?.sessionMethodLocation?.setOnClickListener {
            getSessionMethodLocation()
        }


    }

    fun getSessionMethodLocation() {
        if (binding?.vm?.menteeId.isNullOrEmpty()) {
            showToast("Please Select Assign To First")
            return
        }
        if (binding?.vm?.listSessionMethodLocation?.isEmpty() == true) {
            showToast("Please wait..Fetching your session method location list")
            binding?.vm?.fetchMenteeList()
            return
        }
        val list = arrayListOf<String>()
        for (key in binding?.vm?.listSessionMethodLocation?.keys!!)
            list.add(key)

        selector("Select Session Method Location", list) { _, i ->
            binding?.vm?.sessionMethodLocationKey?.set(list[i])
            binding?.vm?.sessionMethodLocationValue?.set(binding?.vm?.listSessionMethodLocation!![list[i]])
        }

    }

    fun menteeFetchList() {
        val list = arrayListOf<String>()
        binding?.vm?.listMenteeNames?.forEach {
            list.add(it)
        }

        if (list.isEmpty()) {
            showToast("Please wait..Fetching your mentee list")
            //binding?.vm?.fetchMenteeList()
            return
        }

        selector("Select Mentee Name", list) { dialogInterface, i ->
            binding?.vm?.apply {
                menteeName.set(list[i])
                menteeId = listMenteeId[i]
                listMentee.filter {
                    it.id == menteeId.toInt()
                }
                getSessionMethodLocationList(menteeId)
            }

        }

    }


    /*fun schoolList() {
        val list = arrayListOf<String>()
        binding?.vm?.listSchoolNames?.forEach {
            list.add(it)
        }

        if (list.isEmpty()) {
            showToast("Please wait..Fetching your location list")
            binding?.vm?.fetchSchoolList()
            return
        }

        selector("Select Location Name", list) { dialogInterface, i ->
            binding?.vm?.apply {
                schoolLocation.set(list[i])
                binding?.vm?.schoolId = listSchoolId[i]
            }
        }
    }*/


    fun schoolList() {
        //val list = listOf("Student's Actual School", "Take Stock Affiliate Office")
        binding?.vm?.apply {
            if (menteeId.isNullOrEmpty()) {
                showToast("Please Select Assign To First")
                return
            }
            val list = arrayListOf<String>()
            val listId = arrayListOf<String>()
            val l = listMentee?.filter {
                it.id == menteeId.toInt()
            }
            l?.map {
                it.let { it1 ->
                    list.add(it1.schoolName.toString())
                    listId.add(it1.schoolId.toString())
                }
            }
            list.add("Affiliate Office")
            listId.add("0")
            list.add("Virtual Session")
            listId.add("0")
            selector("Select The Session Location", list) { _, i ->
                schoolLocation.set(list[i])
                binding?.vm?.schoolId = listId[i]
                if (listId[i] == "0") {
                    binding?.contentLayout?.sessionSpaceLabel?.visibility = View.GONE
                    binding?.contentLayout?.sessionSpaceCard?.visibility = View.GONE
                } else {
                    binding?.contentLayout?.sessionSpaceLabel?.visibility = View.VISIBLE
                    binding?.contentLayout?.sessionSpaceCard?.visibility = View.VISIBLE
                }
            }
        }
    }

    fun calendarPermission() {

        if (ActivityCompat.checkSelfPermission(
                this,
                Manifest.permission.WRITE_CALENDAR
            ) != PackageManager.PERMISSION_GRANTED
        ) {
            ActivityCompat.requestPermissions(
                this@MentorAddMeetingActivity,
                arrayOf(Manifest.permission.WRITE_CALENDAR), 1
            )
        } else {
            binding?.vm?.calenderReminder()
        }
    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.progressBar?.visibility = if (yes) View.VISIBLE else View.INVISIBLE
    }


    fun showToast(msg: String) {
        toast(msg)
    }

    override fun onResume() {
        super.onResume()
        binding?.vm?.apply {
            firstLaunch = true
            onResume()

        }
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

//    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
//        super.onActivityResult(requestCode, resultCode, data)
//        requestCode
//    }
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
                            this@MentorAddMeetingActivity,
                            Manifest.permission.WRITE_CALENDAR
                        ) ===
                                PackageManager.PERMISSION_GRANTED)
                    ) {
                        binding?.vm?.calenderReminder()
                    }
                }
                return
            }
            2 -> {
                if (requestCode == 101) {
                    binding?.vm?.calenderReminder()
                }
            }
        }
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
                    val sdf = SimpleDateFormat("H:mm")
                    val dateObj = sdf.parse("$hourOfDay:${if (minute <= 9) "0$minute" else minute}")
                    binding?.vm?.time12h?.set(SimpleDateFormat("hh:mm a").format(dateObj))
                }
            },
            cal.get(Calendar.HOUR_OF_DAY), cal.get(Calendar.MINUTE), false
        )
        timePickerDialog.show()

    }


}



