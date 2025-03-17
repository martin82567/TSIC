package com.tsic.ui.screen.mentee_bottom_menu.mychats.my_mentor_list

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentee_api.MyMentorDetails
import com.tsic.data.remote.api.MENTOR_IMAGE_URL
import com.tsic.databinding.InflaterMenteeMyMonterListBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.chat.TwilioChatActivity
import com.tsic.ui.screen.chatdetails.ChatDetailsActivity
import com.tsic.util.*
import org.jetbrains.anko.startActivity

class MenteeMyMentorListAdapter(
    val listStaff: List<MyMentorDetails?>,
    val activity: MenteeMyMentorListActivity
) :
    BaseRecyclerAdapter<MyMentorDetails?>(listStaff) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {

        return MyMentorListItemHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_mentee_my_monter_list,
                parent,
                false
            )
        )
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) {
        (holder as MyMentorListItemHolder).bind(listStaff[position])
    }

    inner class MyMentorListItemHolder(val binding: InflaterMenteeMyMonterListBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: MyMentorDetails?) {
            item?.imageUser = MENTOR_IMAGE_URL + item?.imageUser
            binding.model = item
            binding.apply {
                when (item?.session_log_label_no) {
                    "2" -> {
                        ivBadge.setImageResource(
                            R.drawable.bronze_medal
                        )
                        tvNoSession.text = item.sessionLogCount
                    }
                    "3" -> {
                        ivBadge.setImageResource(
                            R.drawable.silver_medal
                        )
                        tvNoSession.text = item.sessionLogCount
                    }
                    "4" -> {
                        ivBadge.setImageResource(
                            R.drawable.gold_medal
                        )
                        tvNoSession.text = item.sessionLogCount
                    }
                }

            }

            binding.root.setOnClickListener {
                var s = ""
                item?.apply {
                    s = "$firstname $middlename $lastname"
                }
                /*activity.startActivity<ChatDetailsActivity>(
                    INTENT_KEY_CHATTER_ID to item?.id?.toString(),
                    INTENT_KEY_CHATTER_NAME to s,
                    INTENT_KEY_CHATTER_PIC to item?.imageUser,
                    INTENT_KEY_CHATTER_TYPE to TYPE_MENTOR
                )*/
                activity.startActivity<TwilioChatActivity>(
                    INTENT_KEY_CHATTER_ID to item?.id?.toString(),
                    INTENT_KEY_CHATTER_NAME to s,
                    INTENT_KEY_CHATTER_PIC to item?.imageUser,
                    INTENT_KEY_CHATTER_TYPE to TYPE_MENTOR,
                    INTENT_KEY_CHAT_SID to item?.channelSid,
                    INTENT_KEY_CHAT_CODE to item?.code,
                )
            }
        }
    }
}