package com.tsic.ui.screen.mentor_bottom_menu.mysessions.add_session

import android.app.DatePickerDialog
import android.app.DatePickerDialog.OnDateSetListener
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.data.local.prefs.KEY_TIMEZONE_OFFSET
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.databinding.ActivityMentorAddSessionBinding
import com.tsic.util.extension.setStatusBarColor
import kotlinx.android.synthetic.main.activity_mentor_add_session.*
import org.jetbrains.anko.selector
import org.jetbrains.anko.toast
import java.util.*


class MentorAddSessionActivity : AppCompatActivity() {

    val userPrefs by lazy { PreferenceHelper.customPrefs(this, USER_PREF) }
    val timeZoneOffset by lazy { userPrefs?.getString(KEY_TIMEZONE_OFFSET, "") }
    val timeZoneStr by lazy { TimeZone.getTimeZone("GMT$timeZoneOffset") }


    //declarations
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityMentorAddSessionBinding>(
            this,
            com.tsic.R.layout.activity_mentor_add_session
        )
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(com.tsic.R.layout.activity_mentor_add_session)
        setSupportActionBar(toolbar)
        initUiAndListeners()
    }


    //methods


    private fun initUiAndListeners() {
        binding.vm = MentorAddSessionViewModel(this)
        setSupportActionBar(binding.toolbar)
        setStatusBarColor(R.color.colorStatusTranslucentGreen)

        supportActionBar?.apply {
            title = "LOG A SESSION"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }


        binding?.contentLayout?.saveSession?.setOnClickListener {
            binding?.vm?.saveSession()
        }
        binding?.contentLayout?.sessionMenteeName?.setOnClickListener {
            menteeFetchList()
        }

        binding?.contentLayout?.sessionTimeFrom?.setOnClickListener {
            estimatedTime()
        }
        binding?.contentLayout?.sessionType?.setOnClickListener {
            sessionType()
        }
        binding?.contentLayout?.sessionMethodLocation?.setOnClickListener {
            getSessionMethodLocation()
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
                binding?.vm?.menteeId = listMenteeId[i]
            }
        }
    }


    fun estimatedTime() {
        val list = arrayListOf<String>()
        for (duration in 30..90 step 5)
            list.add(duration.toString())

        selector("Select Session Duration", list) { dialogInterface, i ->
            binding?.vm?.timeFrom?.set("${list[i]}")
            binding?.vm?.apply {
                // timeFrom.set("${list[i]}")
                //binding?.vm?.menteeId = "${listMenteeId[i]}"

            }
        }


    }

    fun sessionType() {
        val sessionType: Map<String, String> =
            mapOf("Group" to "1", "Individual" to "2",/* "Virtual" to "3"*/)
        val list = arrayListOf<String>()
        for (key in sessionType.keys)
            list.add(key)

        selector("Select Session Type", list) { _, i ->
            binding?.vm?.sessionTypeKey?.set(list[i])
            binding?.vm?.sessionTypeValue?.set(sessionType[list[i]])
        }

    }

    fun getSessionMethodLocation() {
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
            getSessionMethodLocationList()
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


    fun showDatePicker(view: View) {
        val cal = Calendar.getInstance(timeZoneStr, Locale.US)
        var maxDay = cal.timeInMillis
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
            OnDateSetListener { view, year, monthOfYear, dayOfMonth ->
                binding?.vm?.date?.set((monthOfYear + 1).toString() + "-" + dayOfMonth.toString() + "-" + year)
            }, cal.get(Calendar.YEAR), cal.get(Calendar.MONTH), cal.get(Calendar.DAY_OF_MONTH)
        )

        datePickerDialog.datePicker.maxDate = maxDay
        datePickerDialog.show()
    }


}



