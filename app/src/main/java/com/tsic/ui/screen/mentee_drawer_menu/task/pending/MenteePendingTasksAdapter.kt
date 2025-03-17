package com.tsic.ui.screen.mentee_drawer_menu.task.pending

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentee_api.TaskDatalist
import com.tsic.databinding.InflaterMenteePendingTasksBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.mentee_drawer_menu.task.pending_task.MenteePendingTaskDetailsActivity
import com.tsic.util.INTENT_KEY_TASK_ID
import org.jetbrains.anko.startActivity


class MenteePendingTasksAdapter(
    val listPending: List<TaskDatalist>,
    val fragment: MenteePendingTasksFrag
) :
    BaseRecyclerAdapter<TaskDatalist?>(listPending) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {


        val binding: InflaterMenteePendingTasksBinding = DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_mentee_pending_tasks,
                parent,
                false
            )
        binding.badge?.visibility = View.GONE
        return PendingTasksItemHolder(binding)
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) {
        (holder as PendingTasksItemHolder).bind(listPending[position])
    }

    inner class PendingTasksItemHolder(val binding: InflaterMenteePendingTasksBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: TaskDatalist?) {
            binding.model = item

            binding.root.setOnClickListener {
                fragment.activity?.startActivity<MenteePendingTaskDetailsActivity>(
                    INTENT_KEY_TASK_ID to item?.assignId?.toString()
                 )
            }
        }
    }
}