package com.tsic.ui.screen.mentee_bottom_menu.mymeeting

/**
 * @author Kaiser Perwez
 */

import android.Manifest
import android.app.NotificationManager
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.content.res.Configuration
import android.os.Build
import android.provider.CalendarContract
import android.util.DisplayMetrics
import android.util.TypedValue
import android.view.View
import android.view.WindowManager
import android.widget.TextView
import androidx.annotation.RequiresApi
import androidx.core.app.ActivityCompat
import androidx.core.view.children
import androidx.core.view.doOnPreDraw
import androidx.databinding.DataBindingUtil
import com.kizitonwose.calendarview.model.CalendarDay
import com.kizitonwose.calendarview.model.CalendarMonth
import com.kizitonwose.calendarview.model.DayOwner
import com.kizitonwose.calendarview.ui.DayBinder
import com.kizitonwose.calendarview.ui.MonthHeaderFooterBinder
import com.kizitonwose.calendarview.ui.ViewContainer
import com.kizitonwose.calendarview.utils.Size
import com.kizitonwose.calendarview.utils.next
import com.kizitonwose.calendarview.utils.previous
import com.tsic.R
import com.tsic.databinding.ActivityMenteeMyMeetingBinding
import com.tsic.databinding.CalendarDayBinding
import com.tsic.databinding.CalendarHeaderBinding
import com.tsic.ui.base.BaseTabViewPagerAdapter
import com.tsic.ui.base.MenteeBaseMainActivity
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.detaillist.MenteeMyAllMeetingActivity
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.past.MenteePastMeetingListFrag
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.requested.MenteeRequestedMeetingListFrag
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.upcoming.MenteeUpcomingMeetingListFrag
import com.tsic.ui.screen.mentor_bottom_menu.mysessions.calendarView.daysOfWeekFromLocale
import com.tsic.ui.screen.mentor_bottom_menu.mysessions.calendarView.makeGone
import com.tsic.ui.screen.mentor_bottom_menu.mysessions.calendarView.makeVisible
import com.tsic.ui.screen.mentor_bottom_menu.mysessions.calendarView.setTextColorRes
import com.tsic.ui.screen.mentor_drawer_menu.meetings.ModalBottomSheet
import com.tsic.ui.screen.mentor_drawer_menu.meetings.alllist.MentorAllDetailsListActivity
import com.tsic.ui.screen.mentor_drawer_menu.meetings.requested.addMeeting.MentorAddMeetingActivity
import com.tsic.util.extension.dismissKeyboard
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.startActivity
import org.jetbrains.anko.toast
import java.text.DateFormat
import java.text.ParseException
import java.text.SimpleDateFormat
import java.time.LocalDate
import java.time.YearMonth
import java.time.format.DateTimeFormatter
import java.time.format.TextStyle
import java.util.*

class MenteeMyMeetingActivity : MenteeBaseMainActivity() {

    //declarations
    internal var binding: ActivityMenteeMyMeetingBinding? = null

    var date: String = ""
    var time: String = ""
    var title: String = ""
    var description: String = ""
    private var selectedDate: LocalDate? = null
    private val selectedDates = mutableSetOf<LocalDate>()
    var modalBottomSheet : MenteeModalBottomSheet? =null
    @RequiresApi(Build.VERSION_CODES.O)
    private val today = LocalDate.now()
    @RequiresApi(Build.VERSION_CODES.O)
    private val monthTitleFormatter = DateTimeFormatter.ofPattern("MMMM")

    @RequiresApi(Build.VERSION_CODES.O)
    override fun getContentView() {
        val stub = bindingBase.appBarMain.viewstub.viewStub
        stub?.layoutResource = R.layout.activity_mentee_my_meeting
        stub?.setOnInflateListener { _, inflatedView ->
            binding = DataBindingUtil.bind(inflatedView)
            //	binding?.model = JobSearchViewModel(this)
            initUiAndListeners()
            when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
                Configuration.UI_MODE_NIGHT_NO -> {
                    binding?.rootLayout?.setBackgroundResource(R.drawable.bg_all_white)
                } // Night mode is not active, we're using the light theme
                Configuration.UI_MODE_NIGHT_YES -> {
                    binding?.rootLayout?.setBackgroundResource(R.drawable.bg3)
                } // Night mode is active, we're using dark theme
            }
        }
        stub?.inflate()
    }

    override fun getNavigationMenuItemId(): Int {
        return R.id.nav_bottom_mentee_my_meeting
    }
    //methods


    @RequiresApi(Build.VERSION_CODES.O)
    private fun initUiAndListeners() {
        binding?.apply {
            vm = MenteeMyMeetingViewModel(this@MenteeMyMeetingActivity)
            //	setSupportActionBar(toolbar)
            supportActionBar?.title = "SESSIONS"
        }

        bindingBase?.appBarMain?.toolbar?.setBackgroundColor(resources.getColor(R.color.colorToolbarGreen))
        bindingBase?.appBarMain?.toolbar?.title = "SESSIONS"
        setStatusBarColor(R.color.colorStatusTranslucentGreen)
        showStuffChatBadge()
        binding?.vm?.fetchAllData()

        binding?.contentLayout?.toggleSessionBtn?.setOnCheckedChangeListener { _, isChecked ->
            if (isChecked) {//calendar view on
                binding?.contentLayout?.calendarLayout?.root?.makeVisible()
                binding?.contentLayout?.tvAwaitingMenteeConfirmation?.visibility =
                    View.GONE
                binding?.contentLayout?.tvAwaitingMenteeConfirmationViewAll?.visibility =
                    View.GONE
                binding?.contentLayout?.rvAwaitingMenteeConfirmation?.visibility =
                    View.GONE
                binding?.contentLayout?.tvAwaitingSessionOccurrence?.visibility =
                    View.GONE
                binding?.contentLayout?.tvAwaitingSessionOccurrenceViewAll?.visibility =
                    View.GONE
                binding?.contentLayout?.rvAwaitingSessionOccurrence?.visibility =
                    View.GONE
            }
            if (!isChecked) {//card view on
                binding?.contentLayout?.calendarLayout?.root?.makeGone()

                binding?.vm?.upcomingDateVsDataMap?.let {
                    if (it.size != 0) {
                        binding?.contentLayout?.tvAwaitingSessionOccurrence?.visibility =
                            View.VISIBLE
                        binding?.contentLayout?.tvAwaitingSessionOccurrenceViewAll?.visibility =
                            View.VISIBLE
                        binding?.contentLayout?.rvAwaitingSessionOccurrence?.visibility =
                            View.VISIBLE
                        binding?.contentLayout?.rvAwaitingMenteeConfirmation?.visibility =
                            View.VISIBLE
                    }
                }

                binding?.vm?.requestedDateVsDataMap?.let {
                    if (it.size != 0) {
                        binding?.contentLayout?.tvAwaitingMenteeConfirmation?.visibility =
                            View.VISIBLE
                        binding?.contentLayout?.tvAwaitingMenteeConfirmationViewAll?.visibility =
                            View.VISIBLE
                        binding?.contentLayout?.rvAwaitingSessionOccurrence?.visibility =
                            View.VISIBLE
                        binding?.contentLayout?.rvAwaitingMenteeConfirmation?.visibility =
                            View.VISIBLE
                    }
                }
                binding?.vm?.isCardViewDisplayed = true

            }
        }

            binding?.contentLayout?.apply {
            tvAwaitingMenteeConfirmationViewAll.setOnClickListener {
                startActivity<MenteeMyAllMeetingActivity>("page" to 1)
            }
            tvAwaitingSessionOccurrenceViewAll.setOnClickListener {
                startActivity<MenteeMyAllMeetingActivity>("page" to 2)
            }

        }
        val notificationManager =
            getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.cancel(102)
        setMentorSessionBadge(0)
        val daysOfWeek = daysOfWeekFromLocale()
        val currentMonth = YearMonth.now()
        binding?.contentLayout?.calendarLayout?.calendarView?.setup(
            currentMonth.minusMonths(10), currentMonth.plusMonths(
                10
            ), daysOfWeek.first()
        )
        binding?.contentLayout?.calendarLayout?.calendarView?.scrollToMonth(currentMonth)
        var w:Int
        var h:Int
        val dm = DisplayMetrics()
        val wm = getSystemService(Context.WINDOW_SERVICE) as WindowManager
        wm.defaultDisplay.getMetrics(dm)
        binding?.contentLayout?.calendarLayout?.calendarView?.apply {
            val dayWidth = dm.widthPixels / 7
            w=dayWidth
            val dayHeight = (dayWidth * .777).toInt()
            h=dayHeight
            daySize = Size(dayWidth, dayHeight)

        }
        binding?.contentLayout?.calendarLayout?.calendarView?.doOnPreDraw {
            binding?.contentLayout?.calendarLayout?.calendarView?.daySize
        }

        class DayViewContainer(view: View) : ViewContainer(view) {
            // Will be set when this container is bound. See the dayBinder.
            lateinit var day: CalendarDay
            val textView = CalendarDayBinding.bind(view).clOneDayText
            val dayLayoutView = CalendarDayBinding.bind(view).clOneDayLayout
            val  awaitingDotView = CalendarDayBinding.bind(view).awaitingDotView
            val  confirmationDotView = CalendarDayBinding.bind(view).confirmationDotView
            val  passedDotView = CalendarDayBinding.bind(view).passedDotView

            init {
                view.setOnClickListener {
                    val upcomingData = binding?.vm?.upcomingDateVsDataMap?.get(day.date)
                    val requestedData = binding?.vm?.requestedDateVsDataMap?.get(day.date)
                    if(upcomingData != null || requestedData != null) {
                         modalBottomSheet = MenteeModalBottomSheet.newInstance(upcomingData,
                            requestedData, this@MenteeMyMeetingActivity)
                        modalBottomSheet!!.show(supportFragmentManager, MenteeModalBottomSheet.TAG)
                    }
                    if (day.owner == DayOwner.THIS_MONTH) {
                        if (selectedDates.contains(day.date)) {
                            selectedDates.remove(day.date)
                        } else {
                            selectedDates.add(day.date)
                        }
                        binding?.contentLayout?.calendarLayout?.calendarView?.notifyDayChanged(day)
                    }
                }
            }
        }

        binding?.contentLayout?.calendarLayout?.calendarView?.dayBinder = object : DayBinder<DayViewContainer> {
            // Called only when a new container is needed.
            override fun create(view: View) = DayViewContainer(view)

            // Called every time we need to reuse a container.
            @RequiresApi(Build.VERSION_CODES.O)
            override fun bind(container: DayViewContainer, day: CalendarDay) {
                container.textView.text = day.date.dayOfMonth.toString()
                container.day = day
                val textView = container.textView
                val dayLayoutView = container.dayLayoutView
                val awaitingDotView = container.awaitingDotView
                val confirmationDotView = container.confirmationDotView
                val passedDotView = container.passedDotView

                textView.text = day.date.dayOfMonth.toString()

                passedDotView.makeGone()
                awaitingDotView.makeGone()
                confirmationDotView.makeGone()
                if (day.owner == DayOwner.THIS_MONTH) {
                    dayLayoutView.background = null
                    textView.setTextColorRes(R.color.black)
                    if (binding?.vm?.upcomingDateVsDataMap?.containsKey(day.date) == true) {
                        confirmationDotView.makeVisible()
                    }
                    if (binding?.vm?.requestedDateVsDataMap?.containsKey(day.date) == true) {
                        awaitingDotView.makeVisible()
                    }
                    if (today == day.date) {
                        dayLayoutView.setBackgroundResource(R.drawable.calendar_day_today_bg)
                    }
                }
                else{
                    textView.setTextColorRes(R.color.example_1_white_light)
                    textView.background = null
                }

            }
        }

        class MonthViewContainer(view: View) : ViewContainer(view) {
            val legendLayout = CalendarHeaderBinding.bind(view).legendLayout.root
        }

        binding?.contentLayout?.calendarLayout?.calendarView?.monthHeaderBinder = object :
            MonthHeaderFooterBinder<MonthViewContainer> {
            override fun create(view: View) = MonthViewContainer(view)
            @RequiresApi(Build.VERSION_CODES.O)
            override fun bind(container: MonthViewContainer, month: CalendarMonth) {
                // Setup each header day text if we have not done that already.
                if (container.legendLayout.tag == null) {
                    container.legendLayout.tag = month.yearMonth
                    container.legendLayout.children.map { it as TextView }.forEachIndexed { index, tv ->
                        tv.text = daysOfWeek[index].getDisplayName(TextStyle.SHORT, Locale.ENGLISH)
                            .toUpperCase(Locale.ENGLISH)
                        tv.setTextColorRes(R.color.black)
                        tv.setTextSize(TypedValue.COMPLEX_UNIT_SP, 12f)
                    }
                    month.yearMonth
                }
            }
        }

        binding?.contentLayout?.calendarLayout?.calendarView?.monthScrollListener = { month ->
            val title = "${monthTitleFormatter.format(month.yearMonth)} ${month.yearMonth.year}"
            binding?.contentLayout?.calendarLayout?.clMonthYearText?.text = title

            selectedDate?.let {
                // Clear selection if we scroll to a new month.
                selectedDate = null
                binding?.contentLayout?.calendarLayout?.calendarView?.notifyDateChanged(it)
//                updateAdapterForDate(null)
            }
        }

        binding?.contentLayout?.calendarLayout?.clNextMonthImage?.setOnClickListener {
            binding?.contentLayout?.calendarLayout?.calendarView?.findFirstVisibleMonth()?.let {
                binding?.contentLayout?.calendarLayout?.calendarView?.smoothScrollToMonth(it.yearMonth.next)
            }
        }

        binding?.contentLayout?.calendarLayout?.clPreviousMonthImage?.setOnClickListener {
            binding?.contentLayout?.calendarLayout?.calendarView?.findFirstVisibleMonth()?.let {
                binding?.contentLayout?.calendarLayout?.calendarView?.smoothScrollToMonth(it.yearMonth.previous)
            }
        }

    }

    fun showToast(msg: String?) {
        msg?.let { toast(it).show() }
    }

    fun isBusyLoadingData(yes: Boolean) {

    }
//    override fun onPause() {
//        super.onPause()
//        binding?.vm?.onPause()
//    }

    @RequiresApi(Build.VERSION_CODES.O)
    override fun onResume() {
        super.onResume()
        binding?.vm?.fetchAllData()
        if(modalBottomSheet!=null && (modalBottomSheet!!.isVisible)){
            modalBottomSheet!!.dismiss()
        }
    }

    fun calendarPermission() {
        if (ActivityCompat.checkSelfPermission(
                this@MenteeMyMeetingActivity,
                Manifest.permission.WRITE_CALENDAR
            ) != PackageManager.PERMISSION_GRANTED
        ) {
            ActivityCompat.requestPermissions(
                this@MenteeMyMeetingActivity,
                arrayOf(Manifest.permission.WRITE_CALENDAR), 1
            )
        } else {
            calenderReminder(date, time, title, description)
        }
    }

//    override fun onStop() {
//        super.onStop()
//        binding?.vm?.onStop()
//    }
    fun calenderReminder(
        date: String,
        time: String,
        title: String,
        description: String
    ) {
        var mDate = date.split("-")
        val df: DateFormat = SimpleDateFormat("hh:mm aa")
        val outputformat: DateFormat = SimpleDateFormat("HH:mm")
        var date: Date? = null
        var output: String? = null
        try {
            date = df.parse(time)
            output = outputformat.format(date)

        } catch (pe: ParseException) {
            pe.printStackTrace()
        }
        var mTime = output?.split(":")
        val startMillis: Long = Calendar.getInstance().run {
            mTime?.get(0)?.toInt()?.let {
                set(
                    mDate[2].toInt(),
                    mDate[0].toInt() - 1,
                    mDate[1].toInt(),
                    it,
                    mTime[1].toInt()
                )
            }
            timeInMillis
        }
        val endMillis: Long = startMillis + 3600000


        /* val values = ContentValues().apply {
             put(CalendarContract.Events.DTSTART, startMillis)
             put(CalendarContract.Events.DTEND, endMillis)
             put(CalendarContract.Events.TITLE, title)
             put(CalendarContract.Events.DESCRIPTION, description)
             put(CalendarContract.Events.CALENDAR_ID, 3)
             put(CalendarContract.Events.EVENT_TIMEZONE, "EDT")
         }
         if (ActivityCompat.checkSelfPermission(
                 fragment.activity!!,
                 Manifest.permission.WRITE_CALENDAR
             ) != PackageManager.PERMISSION_GRANTED
         ) return

         fragment.activity!!.contentResolver.insert(CalendarContract.Events.CONTENT_URI, values)*/
        val intent = Intent(Intent.ACTION_INSERT)
            .setData(CalendarContract.Events.CONTENT_URI)
            .putExtra(CalendarContract.EXTRA_EVENT_BEGIN_TIME, startMillis)
            .putExtra(CalendarContract.Events.TITLE, title)
            .putExtra(CalendarContract.Events.DESCRIPTION, description)
            // .putExtra(CalendarContract.Events.EVENT_LOCATION, "TSIC")
            .putExtra(
                CalendarContract.Events.AVAILABILITY,
                CalendarContract.Events.AVAILABILITY_BUSY
            )
        //.putExtra(Intent.EXTRA_EMAIL, "rowan@example.com,trevor@example.com")
        startActivity(intent)
    }


}
