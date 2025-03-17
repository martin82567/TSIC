package com.tsic.ui.screen.mentee_drawer_menu.task

import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.mentee_api.NoteDetails
import com.tsic.data.model.mentee_api.TaskDatalist
import com.tsic.data.model.mentee_api.Uploadedfile
import com.tsic.ui.screen.mentee_drawer_menu.task.completed.MenteeCompletedTasksAdapter
import com.tsic.ui.screen.mentee_drawer_menu.task.completed.MenteeCompletedTasksFrag
import com.tsic.ui.screen.mentee_drawer_menu.task.details.MenteeCompletedTaskDetailsActivity
import com.tsic.ui.screen.mentee_drawer_menu.task.details.MenteeCompletedTaskNotesAdapter
import com.tsic.ui.screen.mentee_drawer_menu.task.details.MenteeCompletedTasksFilesAdapter
import com.tsic.ui.screen.mentee_drawer_menu.task.pending.MenteePendingTasksAdapter
import com.tsic.ui.screen.mentee_drawer_menu.task.pending.MenteePendingTasksFrag
import com.tsic.ui.screen.mentee_drawer_menu.task.pending_task.MenteePendingTaskDetailsActivity
import com.tsic.ui.screen.mentee_drawer_menu.task.pending_task.MenteePendingTaskFilesAdapter
import com.tsic.ui.screen.mentee_drawer_menu.task.pending_task.MenteePendingTaskNotesAdapter

object MenteeTasksBinding {
    @JvmStatic
    @BindingAdapter(value = ["list_pending_tasks", "fragment"], requireAll = true)
    fun RecyclerView.loadPendingTasks(
        listPending: List<TaskDatalist>?,
        fragment: MenteePendingTasksFrag
    ) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            if (!listPending.isNullOrEmpty()) {
                adapter = MenteePendingTasksAdapter(listPending, fragment)
            }
        }
    }

    @JvmStatic
    @BindingAdapter(value = ["list_notes", "activity"], requireAll = true)
    fun RecyclerView.loadPendingTaskDetailNotes(
        listNotes: List<NoteDetails>?,
        activity: MenteePendingTaskDetailsActivity
    ) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            if (!listNotes.isNullOrEmpty()) {
                adapter = MenteePendingTaskNotesAdapter(listNotes, activity)
            }
        }
    }

    @JvmStatic
     @BindingAdapter(value = ["list_completed", "fragment"], requireAll = true)
    fun RecyclerView.loadCompletedTask(
        listCompleted: List<TaskDatalist>?,
        fragment: MenteeCompletedTasksFrag
     ) {
         this.apply {
             layoutManager = LinearLayoutManager(this.context)
             if (!listCompleted.isNullOrEmpty()) {
                 adapter = MenteeCompletedTasksAdapter(listCompleted, fragment)
             }
         }
     }

    @JvmStatic
    @BindingAdapter(value = ["list_notes", "activity"], requireAll = true)
    fun RecyclerView.loadDetailNotes(
        listNotes: List<NoteDetails>?,
        activity: MenteeCompletedTaskDetailsActivity
    ) {
        this.apply {
            layoutManager = LinearLayoutManager(this.context)
            if (!listNotes.isNullOrEmpty()) {
                adapter = MenteeCompletedTaskNotesAdapter(listNotes, activity)
            }
        }
    }

    @JvmStatic
    @BindingAdapter(value = ["list_files", "activity"], requireAll = true)
    fun RecyclerView.loadUserFiles(
        list_files: MutableList<Uploadedfile?>,
        activity: MenteePendingTaskDetailsActivity
    ) {
        val list = mutableListOf<Uploadedfile?>()
        list_files.forEach { list.add(it) }
        list.add(Uploadedfile("", 0, "", 0, 0, ""))
        this.apply {
            adapter = MenteePendingTaskFilesAdapter(list, activity)
        }
    }

    @JvmStatic
    @BindingAdapter(value = ["list_files", "activity"], requireAll = true)
    fun RecyclerView.loadUserFilesCompletedTask(
        list_files: MutableList<Uploadedfile?>,
        activity: MenteeCompletedTaskDetailsActivity
    ) {
        this.apply {
            adapter = MenteeCompletedTasksFilesAdapter(list_files, activity)
        }
    }
}