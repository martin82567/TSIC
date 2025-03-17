package com.tsic.ui.screen.mentee_drawer_menu.goal.completed


import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.fragment.app.Fragment
import com.tsic.R
import com.tsic.databinding.FragmentMenteeCompletedGoalsBinding
import org.jetbrains.anko.support.v4.toast

class MenteeCompletedGoalsFrag : Fragment() {

    var binding: FragmentMenteeCompletedGoalsBinding? = null

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        binding = DataBindingUtil.inflate(
            inflater,
            R.layout.fragment_mentee_completed_goals,
            container,
            false
        )
        binding?.fragment = this
        binding?.vm = MenteeCompletedGoalsViewModel(this)

        // Inflate the layout for this fragment
        return binding!!.root
    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.swipeRefreshLayout?.isRefreshing = yes
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        binding?.apply {
            vm?.fetchCompletedGoals()
            swipeRefreshLayout?.setOnRefreshListener {
                vm?.fetchCompletedGoals()
            }
        }
    }

    fun showToast(msg: String?) {
        msg?.let { toast(it).show() }
    }

    fun reloadCompletedGoals() {
        binding?.vm?.fetchCompletedGoals()
    }
}
