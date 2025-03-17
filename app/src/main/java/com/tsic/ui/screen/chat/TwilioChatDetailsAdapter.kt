package com.tsic.ui.screen.chat

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.databinding.InflaterChatChatterBinding
import com.tsic.databinding.InflaterChatMeBinding
import com.tsic.databinding.InflaterTwilioChatChatterBinding
import com.tsic.databinding.InflaterTwilioChatMeBinding
import com.tsic.ui.base.BaseRecyclerAdapter


class TwilioChatDetailsAdapter(
    list: List<ChatMessage?>,
    val identity:List<String>
) :
    BaseRecyclerAdapter<ChatMessage?>(list) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {
        if (viewType == 0) {
            val myMsgBinding = DataBindingUtil.inflate<InflaterTwilioChatMeBinding>(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_twilio_chat_me,
                parent,
                false
            )
            return MyMsgViewHolder(myMsgBinding)

        } else {
            val chatterMsgBinding = DataBindingUtil.inflate<InflaterTwilioChatChatterBinding>(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_twilio_chat_chatter,
                parent,
                false
            )
            return ChatterMsgViewHolder(chatterMsgBinding)
        }
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) {
        if (getItemViewType(position) == 0)
            (holder as MyMsgViewHolder).bind(position)
        else
            (holder as ChatterMsgViewHolder).bind(position)
    }

    inner class MyMsgViewHolder(val binding: InflaterTwilioChatMeBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(position: Int) {
            binding.model = list[position]
            binding.executePendingBindings()
        }
    }

    inner class ChatterMsgViewHolder(val binding: InflaterTwilioChatChatterBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(position: Int) {
            binding.model = list[position]
            binding.executePendingBindings()
        }
    }


    override fun getItemViewType(position: Int): Int//0 for myMsg, 1 for chatter
    {
        val item = list[position] ?: return super.getItemViewType(position)

        return if (item.author==identity[0]) 0 else 1
    }
}