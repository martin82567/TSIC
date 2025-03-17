package com.tsic.ui.screen.videocallscreen

import android.app.Activity
import android.content.Intent
import android.os.Bundle
import com.tsic.R
import com.tsic.ui.screen.mentor_bottom_menu.mysessions.add_session.MentorAddSessionActivity
import org.jetbrains.anko.alert

class AddSessionDialog : Activity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        alert(getString(R.string.log_session_message)) {
            positiveButton(getString(R.string.yes)) {
                startActivity(Intent(this@AddSessionDialog, MentorAddSessionActivity::class.java))
                finish()
            }
            negativeButton(getString(R.string.no)) {
                finish()
            }
            isCancelable = false
        }.show()

    }
}