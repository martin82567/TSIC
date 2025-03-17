package com.tsic.ui.screen.mentor_drawer_menu.report_list

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentor_api.MentorReport
import com.tsic.data.remote.api.MENTEE_REPORT_IMAGE_BASE_URL
import com.tsic.databinding.InflaterMentorReportBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.util_screens.FullscreenImageActivity
import com.tsic.util.INTENT_KEY_TITLE
import com.tsic.util.INTENT_KEY_URL
import org.jetbrains.anko.startActivity


class MentorReportRecyclerAdapter(listReport: List<MentorReport>) :
    BaseRecyclerAdapter<MentorReport?>(listReport) {


    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {
        val binding = DataBindingUtil.inflate<InflaterMentorReportBinding>(
            LayoutInflater.from(parent?.context),
            R.layout.inflater_mentor_report,
            parent,
            false
        )
        return SessionViewHolder(binding)
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) =
        (holder as SessionViewHolder).bind(list[position])

    inner class SessionViewHolder(val binding: InflaterMentorReportBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: MentorReport?) {
            val url = MENTEE_REPORT_IMAGE_BASE_URL + item?.image


            var obj = item?.run {
                MentorReport(
                    createdDate,
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
                /*val dialogBinding = DataBindingUtil.inflate<InflaterFullImageviewBinding>(
                            LayoutInflater.from(it.context),
                            R.layout.inflater_full_imageview, null, false
                        )
                        dialogBinding?.url = url

                        Dialog(it.context).apply {
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