package com.tsic.ui.screen.mentor_bottom_menu.mychats.my_mentee_list

import android.util.Log
import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentor_api.MentorMyMenteeModel
import com.tsic.data.remote.api.MENTEE_IMAGE_URL
import com.tsic.databinding.InflaterMentorMenteeChatListBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.chat.TwilioChatActivity
import com.tsic.ui.screen.chatdetails.ChatDetailsActivity
import com.tsic.util.*
import org.jetbrains.anko.startActivity


class MentorMyMenteeListAdapter(
    val menteeList: List<MentorMyMenteeModel>,
    val activity: MentorMyMenteeChatListActivity
) : BaseRecyclerAdapter<MentorMyMenteeModel?>(menteeList) {


    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {

        return ListItemHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_mentor_mentee_chat_list,
                parent,
                false
            )
        )
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) {
        (holder as ListItemHolder).bind(menteeList[position])
    }

    inner class ListItemHolder(val binding: InflaterMentorMenteeChatListBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: MentorMyMenteeModel?) {

            val url = MENTEE_IMAGE_URL + item?.image

            var obj = item?.run {
                MentorMyMenteeModel(
                    age,
                    currentLivingDetails,
                    dob,
                    email,
                    firstname,
                    gender,
                    id,
                    url,
                    lastname,
                    middlename,
                    timezone,
                    cell_phone_number,
                    firebaseId,
                    last_session_date,
                    schoolName,
                    schoolId,
                    upcomingMeetingDate,
                    unread_chat_count
                )
            }


            /*binding.root.setOnClickListener {
                fragment.activity?.startActivity<MentorMyChatsActivity>(INTENT_KEY_MENTEE_MODEL to obj)
            }*/

            binding.model = obj
            binding.root.setOnClickListener {
                activity.startActivity<TwilioChatActivity>(
                    INTENT_KEY_CHATTER_ID to item?.id?.toString(),
                    INTENT_KEY_CHATTER_NAME to "${item?.firstname} ${item?.middlename} ${item?.lastname}",
                    INTENT_KEY_CHATTER_PIC to url,
                    INTENT_KEY_CHATTER_TYPE to TYPE_MENTEE,
                    INTENT_KEY_CHAT_SID to item?.channelSid,
                    INTENT_KEY_CHAT_CODE to item?.code,
                )/*activity.startActivity<ChatDetailsActivity>(
                    INTENT_KEY_CHATTER_ID to item?.id?.toString(),
                    INTENT_KEY_CHATTER_NAME to "${item?.firstname} ${item?.middlename} ${item?.lastname}",
                    INTENT_KEY_CHATTER_PIC to url,
                    INTENT_KEY_CHATTER_TYPE to TYPE_MENTEE
                    //INTENT_KEY_FIREBASE_TOKEN to item?.firebaseId
                )*/
            }
            //Log.d("TAG", "bind: ${item?.channelSid} ${item?.code}")
            /*    binding.iVCall?.setOnClickListener {
                    item?.cell_phone_number?.let { it1 -> it?.context?.dialCallIntent(it1) }
                }*/

        }

    }
}