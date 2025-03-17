package com.tsic.ui.screen.mentee_bottom_menu.myuploads

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentor_api.MenteeReportList
import com.tsic.data.remote.api.MENTEE_REPORT_IMAGE_BASE_URL
import com.tsic.databinding.InflaterFullImageviewBinding
import com.tsic.databinding.InflaterMenteeUploadBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.util_screens.FullscreenImageActivity
import com.tsic.util.INTENT_KEY_TITLE
import com.tsic.util.INTENT_KEY_URL
import org.jetbrains.anko.startActivity


class MentorMyUploadRecyclerAdapter(listReport: List<MenteeReportList>) :
    BaseRecyclerAdapter<MenteeReportList?>(listReport) {


    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {
        val binding = DataBindingUtil.inflate<InflaterMenteeUploadBinding>(
            LayoutInflater.from(parent?.context),
            R.layout.inflater_mentee_upload,
            parent,
            false
        )
        return SessionViewHolder(binding)
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) =
        (holder as SessionViewHolder).bind(list[position])

    inner class SessionViewHolder(val binding: InflaterMenteeUploadBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: MenteeReportList?) {
            val url = MENTEE_REPORT_IMAGE_BASE_URL + item?.image


            var obj = item?.run {
                MenteeReportList(
                    created_date,
                    id,
                    url,
                    menteeId,
                    name,
                    status
                )
            }
            binding.model = obj
            binding.root.setOnClickListener {
                it.context.startActivity<FullscreenImageActivity>(
                    INTENT_KEY_URL to url,
                    INTENT_KEY_TITLE to item?.name
                )
                val dialogBinding = DataBindingUtil.inflate<InflaterFullImageviewBinding>(
                            LayoutInflater.from(it.context),
                            R.layout.inflater_full_imageview, null, false
                        )
                        dialogBinding?.url = url

                /* Dialog(it.context).apply {
                     requestWindowFeature(Window.FEATURE_NO_TITLE)
                     this.window?.setLayout(ViewGroup.LayoutParams.MATCH_PARENT,ViewGroup.LayoutParams.MATCH_PARENT)
                     setContentView(dialogBinding.root)
                     show()
                 }*/
            }
            binding.executePendingBindings()
        }
    }
}