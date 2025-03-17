package com.tsic.ui.screen.mentor_bottom_menu.myprofile.dialog

import android.app.Activity
import android.app.Dialog
import android.os.Bundle
import android.view.View
import com.tsic.R
import com.tsic.databinding.DialogReminderLogSessionBinding

class DialogSessionReminder(
    private val activity: Activity,
    private val title: String,
    private val message: String,
    private val onClick: () -> Unit,
) : Dialog(activity) {


    lateinit var binding: DialogReminderLogSessionBinding
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = DialogReminderLogSessionBinding.inflate(layoutInflater)
        setContentView(binding.root)
        // binding.userInfo = userInfoModel
        setCancelable(false)

        binding.tvDialogHeader.text = title
        binding.tvSessionDialog.text = message


        val titleUpcomingSession = activity.getString(R.string.header_reminder_upcoming_session)
        val titlePassedSession = activity.getString(R.string.header_reminder_log_session)

        when (title) {
            titleUpcomingSession -> {
                binding.btnLogNow.visibility = View.GONE
                binding.btnAlreadyDone.text = "OK"
                binding.btnAlreadyDone.setOnClickListener {
                    dismiss()
                }
            }
            titlePassedSession -> {
                binding.btnAlreadyDone.setOnClickListener {
                    dismiss()
                }

                binding.btnLogNow.setOnClickListener {
                    dismiss()
                    onClick()
                }
            }
        }
    }

}