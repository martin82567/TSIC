package com.tsic.ui.screen.mentee_drawer_menu.task.details

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentee_api.NoteDetails
import com.tsic.databinding.InflaterMenteeNotesBinding
import com.tsic.ui.base.BaseRecyclerAdapter

class MenteeCompletedTaskNotesAdapter(
    val listNotes: List<NoteDetails>,
    val activity: MenteeCompletedTaskDetailsActivity
) :
    BaseRecyclerAdapter<NoteDetails?>(listNotes) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {

        return TaskNotesItemHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_mentee_notes,
                parent,
                false
            )
        )
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) {
        (holder as TaskNotesItemHolder).bind(listNotes[position])
    }

    inner class TaskNotesItemHolder(val binding: InflaterMenteeNotesBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: NoteDetails?) {
            binding.model = item
        }
    }

    override fun getItemCount(): Int {
        return listNotes.size
    }
}