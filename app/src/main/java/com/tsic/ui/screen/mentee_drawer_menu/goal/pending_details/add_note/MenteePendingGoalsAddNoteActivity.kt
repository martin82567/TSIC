package com.tsic.ui.screen.mentee_drawer_menu.goal.pending_details.add_note

/**
 * @author Kaiser Perwez
 */

import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityMenteeAddNoteBinding
import com.tsic.util.INTENT_KEY_GOAL_ID
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.toast


class MenteePendingGoalsAddNoteActivity : AppCompatActivity() {

    //declarations
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityMenteeAddNoteBinding>(
            this,
            R.layout.activity_mentee_add_note
        )
    }
    private val goalId by lazy { intent.getStringExtra(INTENT_KEY_GOAL_ID) ?: "" }


    //methods
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()
    }

    private fun initUiAndListeners() {
        binding.vm = MenteePendingGoalsAddNoteViewModel(this)
        setStatusBarColor(R.color.colorStatusTranslucentGreen)
        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            title = "Add Goal Notes"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }
        binding?.contentLayout?.saveNote?.setOnClickListener {
            binding?.vm?.saveNote(goalId)
        }
    }

    fun showToast(msg: String) {
        toast(msg)
    }
    fun isBusyLoadingData(yes: Boolean) {
        binding?.progressBar?.visibility = if (yes) View.VISIBLE else View.INVISIBLE
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
}
