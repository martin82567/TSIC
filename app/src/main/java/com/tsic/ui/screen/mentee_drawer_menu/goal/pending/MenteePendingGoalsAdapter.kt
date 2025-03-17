package com.tsic.ui.screen.mentee_drawer_menu.goal.pending

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentee_api.GoalData
import com.tsic.databinding.InflaterMenteePendingGoalsBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.mentee_drawer_menu.goal.pending_details.MenteePendingGoalDetailsActivity
import com.tsic.util.INTENT_KEY_GOAL_ID
import org.jetbrains.anko.startActivity

class MenteePendingGoalsAdapter(
    val listPending: List<GoalData>,
    val fragment: MenteePendingGoalsFrag
) :
    BaseRecyclerAdapter<GoalData?>(listPending) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {
        val binding: InflaterMenteePendingGoalsBinding = DataBindingUtil.inflate(
            LayoutInflater.from(parent?.context),
            R.layout.inflater_mentee_pending_goals,
            parent,
            false
        )
        binding.badge?.visibility = View.GONE
        return PendingGoalsItemHolder(binding)
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) {
        (holder as PendingGoalsItemHolder).bind(listPending[position])
    }

    inner class PendingGoalsItemHolder(val binding: InflaterMenteePendingGoalsBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: GoalData?) {
            binding.model = item

            binding.root.setOnClickListener {
                fragment.activity?.startActivity<MenteePendingGoalDetailsActivity>(
                    INTENT_KEY_GOAL_ID to item?.assignId?.toString()
                )
            }
        }
    }
}