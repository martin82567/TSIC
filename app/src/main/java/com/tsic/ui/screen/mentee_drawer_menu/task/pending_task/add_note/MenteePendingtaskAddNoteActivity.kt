package com.tsic.ui.screen.mentee_drawer_menu.task.pending_task.add_note

/**
 * @author Kaiser Perwez
 */

import android.content.res.Configuration
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityMenteeTaskAddNoteBinding
import com.tsic.util.INTENT_KEY_TASK_ID
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.toast


class MenteePendingtaskAddNoteActivity : AppCompatActivity() {

    //declarations
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityMenteeTaskAddNoteBinding>(
            this,
            R.layout.activity_mentee_task_add_note
        )
    }
    private val taskId by lazy { intent.getStringExtra(INTENT_KEY_TASK_ID) ?: "" }


    //methods
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()
    }

    private fun initUiAndListeners() {
        binding?.rootLayout?.setBackgroundResource(
            when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
                Configuration.UI_MODE_NIGHT_NO ->
                    R.drawable.bg_all_white
                Configuration.UI_MODE_NIGHT_YES ->
                    R.drawable.bg3
                else -> R.drawable.bg_all_white
            }
        )
        binding.vm = MenteePendingTaskAddNoteViewModel(this)
        setStatusBarColor(R.color.colorStatusTranslucentGreen)
        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            title = "Add Task Notes"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }
        binding?.contentLayout?.saveNote?.setOnClickListener {
            binding?.vm?.saveNote(taskId)
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
