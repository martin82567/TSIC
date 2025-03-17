package com.tsic.ui.screen.mentor_bottom_menu.mychats.my_staff_list

import android.util.Log
import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentor_api.MentorMyStaffModel
import com.tsic.data.remote.api.MENTOR_STAFF_IMAGE_URL
import com.tsic.databinding.InflaterMentorStaffChatListBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.chat.TwilioChatActivity
import com.tsic.ui.screen.chatdetails.ChatDetailsActivity
import com.tsic.util.*
import org.jetbrains.anko.startActivity

class MentorMyStaffListAdapter(


    val staffList: List<MentorMyStaffModel>,
    val activity: MentorMyStaffChatListActivity
) : BaseRecyclerAdapter<MentorMyStaffModel?>(staffList) {


    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {

        return ListItemHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_mentor_staff_chat_list,
                parent,
                false
            )
        )
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) {
        (holder as ListItemHolder).bind(staffList[position])
    }

    inner class ListItemHolder(val binding: InflaterMentorStaffChatListBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: MentorMyStaffModel?) {
            val url = MENTOR_STAFF_IMAGE_URL + item?.profilePic

            var obj = item?.run {
                MentorMyStaffModel(
                    address, email, id, name, unreadChat, url, channelSid, code, timezone,
                )
            }


          /*  binding.root.setOnClickListener {
                fragment.activity?.startActivity<MentorMyChatsActivity>(INTENT_KEY_MENTEE_MODEL to obj)
            }*/

            binding.model = obj
            binding.root.setOnClickListener {
                /*activity.startActivity<ChatDetailsActivity>(
                    INTENT_KEY_CHATTER_ID to item?.id?.toString(),
                    INTENT_KEY_CHATTER_NAME to item?.name,
                    INTENT_KEY_CHATTER_PIC to url,
                    INTENT_KEY_CHATTER_TYPE to TYPE_MENTOR_STAFF
                )*/
                activity.startActivity<TwilioChatActivity>(
                    INTENT_KEY_CHATTER_ID to item?.id?.toString(),
                    INTENT_KEY_CHATTER_NAME to item?.name,
                    INTENT_KEY_CHATTER_PIC to url,
                    INTENT_KEY_CHATTER_TYPE to TYPE_MENTOR_STAFF,
                    INTENT_KEY_CHAT_SID to item?.channelSid,
                    INTENT_KEY_CHAT_CODE to item?.code,
//                    //INTENT_KEY_FIREBASE_TOKEN to item?.firebaseId
                )
//                activity?.showToast("work in progress")

            }
            //Log.d("TAG", "bind: ${item?.channelSid} ${item?.code}")

        }

    }

}