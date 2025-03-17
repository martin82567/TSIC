package com.tsic.ui.screen.message_center

import androidx.databinding.BindingAdapter
import androidx.recyclerview.widget.RecyclerView
import com.tsic.data.model.common.MessageCenterResponse
import com.tsic.data.model.mentee_api.MyStaffDetails

object MessageCenterBinding {
    @JvmStatic
    @BindingAdapter(value = ["list_message"], requireAll = true)
    fun RecyclerView.loadStaff(
        listStaff: List<MessageCenterResponse.Message>?,
    ) {
        this.apply {
            if (!listStaff.isNullOrEmpty()) {
                adapter =
                    MessageCenterListAdapter(
                        listStaff,
                    )
            }
        }
    }


}