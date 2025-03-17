package com.tsic.ui.screen.message_center

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.common.MessageCenterResponse
import com.tsic.databinding.InflaterMessageCenterListBinding
import com.tsic.ui.base.BaseRecyclerAdapter

class MessageCenterListAdapter(
    listMessage: List<MessageCenterResponse.Message>,
) :
    BaseRecyclerAdapter<MessageCenterResponse.Message>(listMessage) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {

        return MyStaffListItemHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_message_center_list,
                parent,
                false
            )
        )
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) {
        (holder as MyStaffListItemHolder).bind(list[position])
    }

    inner class MyStaffListItemHolder(val binding: InflaterMessageCenterListBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: MessageCenterResponse.Message) {
            binding.model = item
            binding.tvCreateBy.text =
                if (item.createdBy == "1") " System Admin" else " Affiliate Office"
        }
    }
}