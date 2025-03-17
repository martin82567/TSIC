package com.tsic.ui.screen.videocallscreen

import android.graphics.Color
import android.os.CountDownTimer
import android.os.Handler
import android.util.Log
import android.view.View
import java.text.DateFormat
import java.text.SimpleDateFormat
import java.util.*

class TimerClass(val activity: VideoCallActivity) {
    val TAG = "TimerClass"
    var timer: CountDownTimer? = null
    var callTimer: CountDownTimer? = null

    private val startflashTimer = Handler()
    private val startflashTimerRunnable by lazy {//for blink text view
        Runnable {
            activity.binding?.contentLayout?.tvShowStatus?.visibility = View.VISIBLE
        }
    }

    fun callClock() {

        activity.apply {
            Log.d(TAG, "timeLoss: $createdAt")
            if (callFrom!="web") {
                val loss = timeLoss(createdAt)

                Log.d(TAG, "timeLoss: $loss")
                Log.d(TAG, "timeLoss: $totalsec")
                totalsec -= loss + 4
                Log.d(TAG, "timeLoss: $totalsec")
            }
            callTimer = object : CountDownTimer(totalsec * 1000, 1000) {
                override fun onTick(millisUntilFinished: Long) {
                    if (showTime) {
                        val min = "${totalsec.div(60)}"
                        val sec = "${totalsec.rem(60)}"
                        when {

                            totalsec in 121..300 -> binding?.contentLayout?.tvShowStatus?.setTextColor(
                                Color.RED
                            )
                            totalsec <= 120 -> {
                                binding?.contentLayout?.tvShowStatus?.setTextColor(Color.RED)
                                binding?.contentLayout?.tvShowStatus?.visibility = View.INVISIBLE
                                startflashTimer.postDelayed(startflashTimerRunnable, 300)
                            }
                            else -> {
                                binding?.contentLayout?.tvShowStatus?.setTextColor(Color.WHITE)
                            }

                        }

                        binding?.contentLayout?.viewModel?.callStatus?.set(
                            "${if (min.length == 1) "0$min" else min}:${if (sec.length == 1) "0$sec" else sec}"
                        )
                    }
                    totalsec--
                    if (totalsec==0L){
                        onFinish()
                    }
                }

                override fun onFinish() {
                    finish()
                }
            }.start()
        }


    }

    fun setDisconnectClock() {
        timer = object : CountDownTimer(45000, 1000) {
            override fun onTick(millisUntilFinished: Long) {
            }

            override fun onFinish() {
                activity.apply {
                    if (isCallDisconnect) {
                        if (callFrom == "Web Call") {
                            Log.d(TAG, "call cancel: Web Call")

                            binding?.contentLayout?.viewModel?.roomUserData?.also {
                                Log.d(TAG, "call cancel: ${it.size} $it")
                                Log.d(TAG, "call cancel: $initSocket")
                                if (it.size != 0) {
                                    initSocket?.endBeforeReceived(
                                        it[RECEIVER_TYPE],
                                        it[RECEIVER_ID],
                                        it[SENDER_TYPE],
                                        it[SENDER_ID]
                                    )
                                }

                            }
                        }
                        binding?.contentLayout?.apply {
                            viewModel?.apply {
                                callDisconnect()
                            }
                        }
                    }
                }

            }
        }.start()

    }

    private fun timeLoss(inpDate: String): Long {
        val timeZone = TimeZone.getTimeZone("America/New_York")

        val format = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.US)
        format.timeZone = timeZone
        val inputDate: Date = format.parse(inpDate)
        Log.d(TAG, "input: $inputDate")

        val cal = Calendar.getInstance(timeZone)
        val currentLocalTime = cal.time
        val date: DateFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.US)
        date.timeZone = timeZone

        val localTime: String = date.format(currentLocalTime)
        Log.d(TAG, "timeLoss: $localTime")
        val currentTime: Date = format.parse(localTime)

        return Date(currentTime.time - inputDate.time).time.div(1000)
    }

    fun onDestroy() {
        callTimer?.onFinish()
        startflashTimer.removeCallbacks(startflashTimerRunnable)
    }

}