package com.tsic.ui.screen.mentee_drawer_menu.goal.completed

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentee_api.GoalData
import com.tsic.databinding.InflaterMenteeCompletedGoalsBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.mentee_drawer_menu.goal.completed_details.MenteeCompletedGoalDetailsActivity
import com.tsic.util.INTENT_KEY_GOAL_ID
import org.jetbrains.anko.startActivity

class MenteeCompletedGoalsAdapter(
    val listCompleted: List<GoalData>,
    val fragment: MenteeCompletedGoalsFrag
) :
    BaseRecyclerAdapter<GoalData?>(listCompleted) {

    override fun onCreateViewHolderBase(parent: ViewGroup?, viewType: Int): RecyclerView.ViewHolder {

        return PendingGoalsItemHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_mentee_completed_goals,
                parent,
                false
            )
        )
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) {
        (holder as PendingGoalsItemHolder).bind(listCompleted[position])
    }

    inner class PendingGoalsItemHolder(val binding: InflaterMenteeCompletedGoalsBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: GoalData?) {
            binding.model = item

            binding.root.setOnClickListener {
                fragment.activity?.startActivity<MenteeCompletedGoalDetailsActivity>(
                    INTENT_KEY_GOAL_ID to item?.assignId?.toString()
                )
            }
        }
    }
}