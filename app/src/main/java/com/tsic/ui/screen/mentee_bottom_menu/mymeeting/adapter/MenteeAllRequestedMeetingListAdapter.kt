package com.tsic.ui.screen.mentee_bottom_menu.mymeeting.adapter

import android.os.Build
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.annotation.RequiresApi
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.Status
import com.tsic.data.model.mentee_api.MenteeAllList
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.databinding.InflaterAllRequestedMeetingBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.MenteeMyMeetingActivity
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.detaillist.MenteeMyAllMeetingActivity
import com.tsic.ui.screen.mentee_bottom_menu.mymeeting.requested.reschedule.MenteeMyMeetingRescheduleActivity
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.disposables.Disposable
import io.reactivex.schedulers.Schedulers
import org.jetbrains.anko.alert
import org.jetbrains.anko.startActivity


class MenteeAllRequestedMeetingListAdapter(
    val listMeeting: List<MenteeAllList.Data.Requested?>,
    val activity: MenteeMyMeetingActivity,
) :
    BaseRecyclerAdapter<MenteeAllList.Data.Requested?>(listMeeting) {

    private val apiService by lazy { MenteeApiService.create() }

    private val userPrefs by lazy {
        PreferenceHelper.customPrefs(activity, USER_PREF)
    }

    private var disposable: Disposable? = null


    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {


        val binding: InflaterAllRequestedMeetingBinding = DataBindingUtil.inflate(
            LayoutInflater.from(parent?.context),
            R.layout.inflater_all_requested_meeting,
            parent,
            false
        )

        return MeetingViewHolder(binding)


    }

    @RequiresApi(Build.VERSION_CODES.O)
    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) =
        (holder as MeetingViewHolder).bind(list[position])

    inner class MeetingViewHolder(val binding: InflaterAllRequestedMeetingBinding) :
        RecyclerView.ViewHolder(binding.root) {
        @RequiresApi(Build.VERSION_CODES.O)
        fun bind(item: MenteeAllList.Data.Requested?) {
            binding.model = item


            if (item?.status ?: 0 == 3) {
                binding.imgCancelled?.visibility = View.VISIBLE
            }
            /*  if(item?.title?:""=="vbjju") {
                  if (item?.isRequestSent == true) {
                      binding.acceptMeeting?.visibility = View.GONE
                      binding?.rejectMeeting?.visibility = View.GONE
                  }
              }*/
            binding.tvMentorLastSession.text =
                if (item?.schoolName == "") "Affiliate Office" else item?.schoolName
            binding.rejectMeeting.setOnClickListener {
                activity.startActivity<MenteeMyMeetingRescheduleActivity>("id" to item?.id.toString())
                /*val mDialogView = LayoutInflater.from(it.context)
                    .inflate(R.layout.request_reschedule_dialog, null)
                val mBuilder = AlertDialog.Builder(it.context)
                    .setView(mDialogView)
                val mAlertDialog = mBuilder.show()
                mDialogView.btn_submit.setOnClickListener { it ->
                    mDialogView.edit_request_note.isEnabled = false
                    fragment?.activity?.removeKeyboard()
                    val imm =
                        it.context?.getSystemService(Context.INPUT_METHOD_SERVICE) as InputMethodManager
                    imm.hideSoftInputFromWindow(View(it.context).windowToken, 0)
                    val progressDialog =
                        fragment?.activity?.indeterminateProgressDialog("Submitting Details")
                            ?.apply { setCancelable(false) }

                    val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
                    apiService.getRequestedNote(
                        token, item?.id?.toString(),
                        mDialogView.edit_request_note.text.toString()

                    ).subscribeOn(Schedulers.io())
                        .observeOn(AndroidSchedulers.mainThread())
                        .doOnSubscribe {
                            fragment.runOnUiThread {
                                progressDialog?.show()
                            }
                        }
                        .doAfterTerminate {
                            fragment.runOnUiThread {
                                progressDialog?.dismiss()
                            }
                        }.subscribe(
                            { result ->
                                mAlertDialog.dismiss()
                                if (result.status == Status.SUCCESS) {
                                    fragment.showToast("Reschedule request has been sent successfully")

                                    //                              val note = mDialogView.edit_request_note.text.toString()
                                    *//* val imm = fragment?.activity?.getSystemService(Context.INPUT_METHOD_SERVICE) as InputMethodManager
                                     val view = fragment?.activity?.currentFocus ?: View(fragment?.context)
                                     imm.hideSoftInputFromWindow(view.windowToken, 0)*//*
                                    binding?.layoutButton?.visibility = View.GONE
                                    binding?.rejectMeeting?.visibility = View.GONE
                                    binding?.acceptMeeting?.visibility = View.GONE
                                } else {
                                    fragment.showToast(result.message)
                                }

                            },
                            { error ->
                                fragment.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                            })


                    *//*binding.acceptMeeting?.setOnClickListener {
                        viewModel.acceptMeeting(item?.id?.toString() ?: "0")
                    }*//*
                }*/
            }

            binding.acceptMeeting?.setOnClickListener {

                val token = userPrefs?.getString(KEY_AUTH_TOKEN, "")
                disposable = apiService.getRequestedMenteeMeeting(
                    token,
                    item?.id.toString(),
                    "1"
                )
                    .subscribeOn(Schedulers.io())
                    .observeOn(AndroidSchedulers.mainThread())
                    .doOnSubscribe {
                        activity.isBusyLoadingData(true)
                    }
                    .doAfterTerminate {
                        activity.isBusyLoadingData(false)
                    }
                    .subscribe(
                        { result ->
                            if (result.status == Status.SUCCESS) {
                                activity.showToast("Accepted")
                                activity.alert("Link newly created Session to save it to system calendar") {
                                    isCancelable = false
                                    positiveButton("Yes") {
                                        /*calenderReminder(
                                            item?.date.toString(),
                                            item?.time.toString(),
                                            item?.title.toString(),
                                            item?.description.toString()
                                        )*/
                                        activity.apply {
                                            date = item?.date.toString()
                                            time = item?.time.toString()
                                            title = item?.title.toString()
                                            description = item?.description.toString()
                                            calendarPermission()
                                        }
                                        binding.acceptMeeting.visibility = View.GONE
                                        binding.rejectMeeting.visibility = View.GONE
                                        binding.cardReqstMeeting.visibility = View.GONE
                                        activity?.binding?.vm?.fetchAllData()
                                        if(activity?.modalBottomSheet!=null&& (activity?.modalBottomSheet!!.isVisible)){
                                            activity?.modalBottomSheet!!.dismiss()
                                        }

                                    }
                                    negativeButton("No") {
                                        binding.acceptMeeting.visibility = View.GONE
                                        binding.rejectMeeting.visibility = View.GONE
                                        binding.cardReqstMeeting.visibility = View.GONE
                                        activity?.binding?.vm?.fetchAllData()
                                        if(activity?.modalBottomSheet!=null&& (activity?.modalBottomSheet!!.isVisible)){
                                            activity?.modalBottomSheet!!.dismiss()
                                        }
                                    }
                                }?.show()

                            } else {
                                activity.showToast(result.message)
                                activity.isBusyLoadingData(false)
                            }
                        },
                        { error ->
                            activity.showToast(error.message ?: "Error: \n The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.")
                        }
                    )

            }

        }

    }
}

