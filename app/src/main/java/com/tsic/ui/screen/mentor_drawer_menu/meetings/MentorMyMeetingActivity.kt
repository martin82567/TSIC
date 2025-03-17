package com.tsic.ui.screen.mentor_drawer_menu.meetings

/**
 * @author Kaiser Perwez
 */
import android.app.NotificationManager
import android.content.Context
import android.content.res.Configuration
import android.os.Build
import android.util.DisplayMetrics
import android.util.TypedValue
import android.view.View
import android.view.WindowManager
import android.widget.TextView
import androidx.annotation.RequiresApi
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
import com.tsic.databinding.*
import com.tsic.ui.base.MentorBaseMainActivity
import com.tsic.ui.screen.mentor_bottom_menu.mysessions.calendarView.*
import com.tsic.ui.screen.mentor_bottom_menu.mysessions.calendarView.setTextColorRes
import com.tsic.ui.screen.mentor_drawer_menu.meetings.alllist.MentorAllDetailsListActivity
import com.tsic.ui.screen.mentor_drawer_menu.meetings.requested.addMeeting.MentorAddMeetingActivity
import com.tsic.ui.screen.mentor_drawer_menu.meetings.view_session_log.ViewSessionLogActivity
import com.tsic.util.extension.setStatusBarColor
import kotlinx.android.synthetic.main.content_mentor_my_meeting.*
import org.jetbrains.anko.configuration
import org.jetbrains.anko.startActivity
import org.jetbrains.anko.toast
import java.time.LocalDate
import java.time.YearMonth
import java.time.format.DateTimeFormatter
import java.time.format.TextStyle
import java.util.*

class MentorMyMeetingActivity : MentorBaseMainActivity() {

    //declarations
    internal var binding: ActivityMentorMyMeetingBinding? = null

    var time: String = ""
    private var selectedDate: LocalDate? = null
    @RequiresApi(Build.VERSION_CODES.O)
    private val monthTitleFormatter = DateTimeFormatter.ofPattern("MMMM")
    var title: String = ""
    private val selectedDates = mutableSetOf<LocalDate>()
    @RequiresApi(Build.VERSION_CODES.O)
    private val today = LocalDate.now()
     var modalBottomSheet : ModalBottomSheet? =null

    @RequiresApi(Build.VERSION_CODES.O)
    override fun getContentView() {
        val stub = bindingBase.appBarMain.viewstub.viewStub
        stub?.layoutResource = R.layout.activity_mentor_my_meeting
        stub?.setOnInflateListener { _, inflatedView ->
            binding = DataBindingUtil.bind(inflatedView)
            initUiAndListeners()
            when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
                Configuration.UI_MODE_NIGHT_NO -> {
                    binding?.rootLayout?.setBackgroundResource(R.drawable.bg_all_white)
                } // Night mode is not active, we're using the light theme
                Configuration.UI_MODE_NIGHT_YES -> {
                    binding?.rootLayout?.setBackgroundResource(R.drawable.bg3)
                } // Night mode oƒ active, we're using dark theme
            }
        }
        stub?.inflate()
    }

    override fun getNavigationMenuItemId(): Int {
        return R.id.nav_bottom_mentor_my_meeting

    }
    //methods

    @RequiresApi(Build.VERSION_CODES.O)
    private fun initUiAndListeners() {
        binding?.apply {
            vm = MentorMyMeetingViewModel(this@MentorMyMeetingActivity)
            setStatusBarColor(R.color.colorStatusTranslucentGreen)
        }

        supportActionBar?.apply {
            title = "Meeting"
        }

        bindingBase?.appBarMain?.toolbar?.title = "SESSIONS"
        bindingBase?.appBarMain?.toolbar?.setBackgroundColor(resources.getColor(R.color.colorToolbarGreen))
        showBadge()
        setMentorSessionBadge(0)
        binding?.vm?.fetchAllData()
        binding?.btnAddMeeting?.setOnClickListener {
            startActivity<MentorAddMeetingActivity>()
        }
        binding?.contentLayout?.button2?.setOnClickListener {
            startActivity<ViewSessionLogActivity>()
        }
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
                binding?.contentLayout?.tvScheduledSessionPassed?.visibility =
                    View.GONE
                binding?.contentLayout?.tvScheduledSessionPassedViewAll?.visibility =
                    View.GONE
                binding?.contentLayout?.rvScheduledSessionPassedViewAll?.visibility =
                    View.GONE
            }
            if (!isChecked) {//card view on
                binding?.contentLayout?.calendarLayout?.root?.makeGone()
                binding?.vm?.pastDateVsDataMap?.let {
                    if (it.size != 0) {
                        binding?.contentLayout?.tvScheduledSessionPassed?.visibility =
                            View.VISIBLE
                        binding?.contentLayout?.tvScheduledSessionPassedViewAll?.visibility =
                            View.VISIBLE
                        binding?.contentLayout?.rvScheduledSessionPassedViewAll?.visibility =
                            View.VISIBLE
                    }
                }

                binding?.vm?.upcomingDateVsDataMap?.let {
                    if (it.size != 0) {
                        binding?.contentLayout?.tvAwaitingSessionOccurrence?.visibility =
                            View.VISIBLE
                        binding?.contentLayout?.tvAwaitingSessionOccurrenceViewAll?.visibility =
                            View.VISIBLE
                        binding?.contentLayout?.rvAwaitingSessionOccurrence?.visibility =
                            View.VISIBLE
                    }
                }

                binding?.vm?.requestedDateVsDataMap?.let {
                    if (it.size != 0) {
                        binding?.contentLayout?.tvAwaitingMenteeConfirmation?.visibility =
                            View.VISIBLE
                        binding?.contentLayout?.tvAwaitingMenteeConfirmationViewAll?.visibility =
                            View.VISIBLE
                        binding?.contentLayout?.rvAwaitingMenteeConfirmation?.visibility =
                            View.VISIBLE
                    }
                }
                binding?.vm?.isCardViewDisplayed = true
            }


        }
        val notificationManager =
            getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager
        notificationManager.cancel(102)
        binding?.contentLayout?.apply {
            tvAwaitingMenteeConfirmationViewAll.setOnClickListener {
                startActivity<MentorAllDetailsListActivity>("page" to 1)
            }
            tvAwaitingSessionOccurrenceViewAll.setOnClickListener {
                startActivity<MentorAllDetailsListActivity>("page" to 2)
            }
            tvScheduledSessionPassedViewAll.setOnClickListener {
                startActivity<MentorAllDetailsListActivity>("page" to 3)
            }
            btnCreateSession?.setOnClickListener {
                startActivity<MentorAddMeetingActivity>()
            }
        }

//        binding?.contentLayout?.exFiveRv?.apply {
//            layoutManager = LinearLayoutManager(this?.context, RecyclerView.VERTICAL, false)
////            adapter = flightsAdapter
//            addItemDecoration(DividerItemDecoration(this?.context, RecyclerView.VERTICAL))
//        }
//        flightsAdapter.notifyDataSetChanged()

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
                    val pastData = binding?.vm?.pastDateVsDataMap?.get(day.date)
                    val upcomingData = binding?.vm?.upcomingDateVsDataMap?.get(day.date)
                    val requestedData = binding?.vm?.requestedDateVsDataMap?.get(day.date)
                    if(pastData != null || upcomingData != null || requestedData != null) {
                         modalBottomSheet = ModalBottomSheet(pastData,upcomingData,
                            requestedData, this@MentorMyMeetingActivity)
                        modalBottomSheet!!.show(supportFragmentManager, ModalBottomSheet.TAG)
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

                awaitingDotView.makeGone()
                confirmationDotView.makeGone()
                passedDotView.makeGone()
                if (day.owner == DayOwner.THIS_MONTH) {
                        dayLayoutView.background = null
                         textView.setTextColorRes(R.color.black)
                        if(binding?.vm?.upcomingDateVsDataMap?.containsKey(day.date) == true) {
                            confirmationDotView.makeVisible()
                        }
                        if(binding?.vm?.requestedDateVsDataMap?.containsKey(day.date) == true)  {
                            awaitingDotView.makeVisible()
                        }
                        if(binding?.vm?.pastDateVsDataMap?.containsKey(day.date) == true ) {
                            passedDotView.makeVisible()
                        }
                        if(today == day.date ) {
                            dayLayoutView.setBackgroundResource(R.drawable.calendar_day_today_bg)
                        }

                } else {
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
                selectedDate = null
                binding?.contentLayout?.calendarLayout?.calendarView?.notifyDateChanged(it)
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

    @RequiresApi(Build.VERSION_CODES.O)
    override fun onResume() {
        super.onResume()
        binding?.vm?.fetchAllData()
        if(modalBottomSheet!=null && (modalBottomSheet!!.isVisible)){
            modalBottomSheet!!.dismiss()
        }
    }

}
