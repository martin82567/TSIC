package com.tsic.ui.screen.mentee_drawer_menu.task.pending


import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.fragment.app.Fragment
import com.tsic.R
import com.tsic.databinding.FragmentMenteePendingTasksBinding
import org.jetbrains.anko.support.v4.toast


class MenteePendingTasksFrag : Fragment() {
    var binding: FragmentMenteePendingTasksBinding? = null

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        binding = DataBindingUtil.inflate(
            inflater,
            R.layout.fragment_mentee_pending_tasks,
            container,
            false
        )
        binding?.fragment = this
        binding?.vm = MenteePendingTasksViewModel(this)

        // Inflate the layout for this fragment
        return binding!!.root
    }

    fun isBusyLoadingData(yes: Boolean) {
        binding?.swipeRefreshLayout?.isRefreshing = yes
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        binding?.apply {
            vm?.fetchPendingTasks()
            swipeRefreshLayout?.setOnRefreshListener {
                vm?.fetchPendingTasks()
            }
        }
    }

    fun showToast(msg: String?) {
        msg?.let { toast(it).show() }
    }

    override fun onResume() {
        super.onResume()
        binding?.vm?.fetchPendingTasks()
    }
}
