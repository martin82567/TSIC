package com.tsic.ui.screen.chatdetails

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.local.prefs.KEY_LOGIN_MODE
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.mentee_api.ChatMsg
import com.tsic.databinding.InflaterChatChatterBinding
import com.tsic.databinding.InflaterChatMeBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.util.TYPE_MENTEE
import com.tsic.util.TYPE_MENTOR


class ChatDetailsAdapter(
    list: List<ChatMsg?>,
    val activity: ChatDetailsActivity?
) :
    BaseRecyclerAdapter<ChatMsg?>(list) {


    private val userLoginMode by lazy {
        activity?.applicationContext?.let {
            PreferenceHelper.customPrefs(it, USER_PREF)?.getString(
                KEY_LOGIN_MODE,
                TYPE_MENTEE
            )
        } ?: TYPE_MENTEE
    }

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {
        if (viewType == 0) {
            val myMsgBinding = DataBindingUtil.inflate<InflaterChatMeBinding>(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_chat_me,
                parent,
                false
            )
            return MyMsgViewHolder(myMsgBinding)

        } else {
            val chatterMsgBinding = DataBindingUtil.inflate<InflaterChatChatterBinding>(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_chat_chatter,
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

    inner class MyMsgViewHolder(val binding: InflaterChatMeBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(position: Int) {
            binding.model = list[position]
            activity?.binding?.apply {
                if (vm?.chatterType?.get() == TYPE_MENTOR || vm?.chatterType?.get() == TYPE_MENTEE) {
                    if (position == list.size - 1 && vm?.page!! < vm?.chatCount ?: 0) {
                        contentChatMessage?.pageLoader?.visibility = View.VISIBLE
                        vm?.apply {
                            page++
                            fetchMsgList(false)
                        }
                    }
                } else {
                    if (position == list.size - 1 && vm?.moreData!!) {
                        contentChatMessage?.pageLoader?.visibility = View.VISIBLE
                        vm?.apply {
                            page++
                            fetchMsgList(false)
                        }
                    }
                }
            }
            binding.executePendingBindings()
        }
    }

    inner class ChatterMsgViewHolder(val binding: InflaterChatChatterBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(position: Int) {
            binding.model = list[position]
            activity?.binding?.apply {
                if (vm?.chatterType?.get() == TYPE_MENTOR || vm?.chatterType?.get() == TYPE_MENTEE) {
                    if (position == list.size - 1 && vm?.page!! < vm?.chatCount ?: 0) {
                        contentChatMessage?.pageLoader?.visibility = View.VISIBLE
                        vm?.apply {
                            page++
                            fetchMsgList(false)
                        }
                    }
                } else {
                    if (position == list.size - 1 && vm?.moreData!!) {
                        contentChatMessage?.pageLoader?.visibility = View.VISIBLE
                        vm?.apply {
                            page++
                            fetchMsgList(false)
                        }
                    }
                }
            }
            binding.executePendingBindings()
        }
    }


    override fun getItemViewType(position: Int): Int//0 for myMsg, 1 for chatter
    {
        val item = list.get(position) ?: return super.getItemViewType(position)

        return if (item.fromWhere == userLoginMode) 0 else 1
    }
}