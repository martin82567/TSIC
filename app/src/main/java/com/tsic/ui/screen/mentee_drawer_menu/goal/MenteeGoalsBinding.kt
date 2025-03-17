package com.tsic.ui.screen.mentee_drawer_menu.goal

import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentee_api.GoalData
import com.tsic.data.model.mentee_api.NoteDetails
import com.tsic.data.model.mentee_api.Uploadedfile
import com.tsic.ui.screen.mentee_drawer_menu.goal.completed.MenteeCompletedGoalsAdapter
import com.tsic.ui.screen.mentee_drawer_menu.goal.completed.MenteeCompletedGoalsFrag
import com.tsic.ui.screen.mentee_drawer_menu.goal.completed_details.MenteeCompletedGoalDetailsActivity
import com.tsic.ui.screen.mentee_drawer_menu.goal.completed_details.MenteeCompletedGoalFilesAdapter
import com.tsic.ui.screen.mentee_drawer_menu.goal.completed_details.MenteeCompletedGoalNotesAdapter
import com.tsic.ui.screen.mentee_drawer_menu.goal.pending.MenteePendingGoalsAdapter
import com.tsic.ui.screen.mentee_drawer_menu.goal.pending.MenteePendingGoalsFrag
import com.tsic.ui.screen.mentee_drawer_menu.goal.pending_details.MenteePendingGoalDetailsActivity
import com.tsic.ui.screen.mentee_drawer_menu.goal.pending_details.MenteePendingGoalFilesAdapter
import com.tsic.ui.screen.mentee_drawer_menu.goal.pending_details.MenteePendingGoalNotesAdapter

object MenteeGoalsBinding {
    @JvmStatic
    @BindingAdapter(value = ["list_pending", "fragment"], requireAll = true)
    fun RecyclerView.loadPendingGoals(
        listPending: List<GoalData>?,
        fragment: MenteePendingGoalsFrag
    ) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            if (!listPending.isNullOrEmpty()) {
                adapter = MenteePendingGoalsAdapter(listPending, fragment)
            }
        }
    }

    @JvmStatic
    @BindingAdapter(value = ["list_completed", "fragment"], requireAll = true)
    fun RecyclerView.loadCompletedGoals(
        listCompleted: List<GoalData>?,
        fragment: MenteeCompletedGoalsFrag
    ) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            if (!listCompleted.isNullOrEmpty()) {
                adapter = MenteeCompletedGoalsAdapter(listCompleted, fragment)
            }
        }
    }

    @JvmStatic
    @BindingAdapter(value = ["list_notes", "activity"], requireAll = true)
    fun RecyclerView.loadPendingGoalDetailNotes(
        listNotes: List<NoteDetails>?,
        activity: MenteePendingGoalDetailsActivity
    ) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            if (!listNotes.isNullOrEmpty()) {
                adapter = MenteePendingGoalNotesAdapter(listNotes, activity)
            }
        }
    }

    @JvmStatic
    @BindingAdapter(value = ["list_notes", "activity"], requireAll = true)
    fun RecyclerView.loadCompleteGoalDetailNotes(
        listNotes: List<NoteDetails>?,
        activity: MenteeCompletedGoalDetailsActivity
    ) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            if (!listNotes.isNullOrEmpty()) {
                adapter = MenteeCompletedGoalNotesAdapter(listNotes, activity)
            }
        }
    }

    @JvmStatic
    @BindingAdapter(value = ["list_files", "activity"], requireAll = true)
    fun RecyclerView.loadUserFilesPendingGoals(
        list_files: MutableList<Uploadedfile?>,
        activity: MenteePendingGoalDetailsActivity
    ) {
        val list = mutableListOf<Uploadedfile?>()
        list_files.forEach { list.add(it) }
        list.add(Uploadedfile("", 0, "", 0, 0, ""))
        this.apply {
            adapter = MenteePendingGoalFilesAdapter(list, activity)
        }
    }

    @JvmStatic
    @BindingAdapter(value = ["list_files", "activity"], requireAll = true)
    fun RecyclerView.loadUserFilesCompletedGoals(
        list_files: MutableList<Uploadedfile?>,
        activity: MenteeCompletedGoalDetailsActivity
    ) {
        this.apply {
            adapter = MenteeCompletedGoalFilesAdapter(list_files, activity)
        }
    }

}