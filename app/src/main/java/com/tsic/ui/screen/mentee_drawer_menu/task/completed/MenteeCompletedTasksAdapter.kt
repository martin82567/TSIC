package com.tsic.ui.screen.mentee_drawer_menu.task.completed

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentee_api.TaskDatalist
import com.tsic.databinding.InflaterMenteeCompletedTasksBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.mentee_drawer_menu.task.details.MenteeCompletedTaskDetailsActivity
import com.tsic.util.INTENT_KEY_TASK_ID
import org.jetbrains.anko.startActivity


class MenteeCompletedTasksAdapter(
    val listCompleted: List<TaskDatalist>,
    val fragment: MenteeCompletedTasksFrag
) :
    BaseRecyclerAdapter<TaskDatalist?>(listCompleted) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {

        return CompletedTasksItemHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_mentee_completed_tasks,
                parent,
                false
            )
        )
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) {
        (holder as MenteeCompletedTasksAdapter.CompletedTasksItemHolder).bind(listCompleted[position])
    }

    inner class CompletedTasksItemHolder(val binding: InflaterMenteeCompletedTasksBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: TaskDatalist?) {
            binding.model = item
            binding.root.setOnClickListener {
                fragment.activity?.startActivity<MenteeCompletedTaskDetailsActivity>(
                    INTENT_KEY_TASK_ID to item?.assignId?.toString()
                 )
            }
        }
    }
}