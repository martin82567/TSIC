package com.tsic.ui.screen.mentee_bottom_menu.mychats.my_staff_list

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentee_api.MyStaffDetails
import com.tsic.data.remote.api.MENTOR_STAFF_IMAGE_URL
import com.tsic.databinding.InflaterMenteeStaffListBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.chat.TwilioChatActivity
import com.tsic.util.*
import org.jetbrains.anko.startActivity

class MenteeMyStaffListAdapter(
    val listStaff: List<MyStaffDetails?>,
    val activity: MenteeMyStaffListActivity
) :
    BaseRecyclerAdapter<MyStaffDetails?>(listStaff) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {

        return MyStaffListItemHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_mentee_staff_list,
                parent,
                false
            )
        )
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) {
        (holder as MyStaffListItemHolder).bind(listStaff[position])
    }

    inner class MyStaffListItemHolder(val binding: InflaterMenteeStaffListBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: MyStaffDetails?) {
            item?.imageUser = MENTOR_STAFF_IMAGE_URL + item?.imageUser
            binding.model = item
            binding.root.setOnClickListener {
                /* activity.startActivity<ChatDetailsActivity>(
                     INTENT_KEY_CHATTER_ID to item?.id?.toString(),
                     INTENT_KEY_CHATTER_NAME to item?.name,
                     INTENT_KEY_CHATTER_PIC to item?.imageUser,
                     INTENT_KEY_CHATTER_TYPE to TYPE_MENTEE_STAFF
                 )*/
                activity.startActivity<TwilioChatActivity>(
                    INTENT_KEY_CHATTER_ID to item?.id?.toString(),
                    INTENT_KEY_CHATTER_NAME to item?.name,
                    INTENT_KEY_CHATTER_PIC to item?.imageUser,
                    INTENT_KEY_CHATTER_TYPE to TYPE_MENTEE_STAFF,
                    INTENT_KEY_CHAT_SID to item?.channelSid,
                    INTENT_KEY_CHAT_CODE to item?.code,
                    //INTENT_KEY_FIREBASE_TOKEN to item?.firebaseId
                )
//                activity?.showToast("work in progress")
            }
        }
    }
}