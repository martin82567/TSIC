package com.tsic.ui.screen.mentor_bottom_menu.mysessions

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentor_api.SessionResponse
import com.tsic.databinding.InflaterMentorSessionBinding
import com.tsic.ui.base.BaseRecyclerAdapter

class MentorSessionListRecycleAdapter(listSession: List<SessionResponse>):
    BaseRecyclerAdapter<SessionResponse?>(listSession)
{
        override fun onCreateViewHolderBase(
            parent: ViewGroup?,
            viewType: Int
        ): RecyclerView.ViewHolder {
            val binding = DataBindingUtil.inflate<InflaterMentorSessionBinding>(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_mentor_session,
                parent,
                false
            )
            return SessionViewHolder(binding)
        }


    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) =
        (holder as SessionViewHolder).bind(list[position])


}


class SessionViewHolder(val binding: InflaterMentorSessionBinding) :
    RecyclerView.ViewHolder(binding.root) {
    fun bind(item: SessionResponse?) {
        val sessionType =
            mapOf("1" to "Group", "2" to "Individual","3" to "Virtual")
        item?.apply {
            type = sessionType[type]
        }
        binding.model = item
    }
}

