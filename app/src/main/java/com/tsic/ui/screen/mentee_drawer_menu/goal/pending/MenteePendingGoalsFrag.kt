package com.tsic.ui.screen.mentee_drawer_menu.goal.pending


import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.fragment.app.Fragment
import com.tsic.R
import com.tsic.databinding.FragmentMenteePendingGoalsBinding
import org.jetbrains.anko.support.v4.toast


class MenteePendingGoalsFrag : Fragment() {
    var binding: FragmentMenteePendingGoalsBinding? = null

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        binding = DataBindingUtil.inflate(
            inflater,
            R.layout.fragment_mentee_pending_goals,
            container,
            false
        )
        binding?.fragment = this
        binding?.vm = MenteePendingGoalsViewModel(this)
        // Inflate the layout for this fragment
        return binding!!.root
    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.swipeRefreshLayout?.isRefreshing = yes
        // binding?.rvPendingGoals?.visibility = if (yes) View.INVISIBLE else View.VISIBLE
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        binding?.apply {
            vm?.fetchPendingGoals()
            swipeRefreshLayout?.setOnRefreshListener {
                vm?.fetchPendingGoals()
            }
        }
    }

    fun showToast(msg: String?) {
        msg?.let { toast(it).show() }
    }

    override fun onResume() {
        super.onResume()
        binding?.vm?.fetchPendingGoals()
    }
}
