package com.tsic.ui.screen.mentee_drawer_menu.learning


import android.text.Html
import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentee_api.LearningResponseItem
import com.tsic.data.remote.api.E_Learning_BaseFile
import com.tsic.databinding.InflaterMenteeELearningBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.mentee_drawer_menu.learning.details.MenteeElearningDetailsActivity
import com.tsic.util.INTENT_ELEARNING
import org.jetbrains.anko.browse
import org.jetbrains.anko.startActivity


class MenteeElearningListAdapter(listElearning: List<LearningResponseItem?>) :
    BaseRecyclerAdapter<LearningResponseItem?>(listElearning) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {
        val binding = DataBindingUtil.inflate<InflaterMenteeELearningBinding>(
            LayoutInflater.from(parent?.context),
            R.layout.inflater_mentee_e_learning,
            parent,
            false
        )
        return JobViewHolder(binding)
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) =
        (holder as JobViewHolder).bind(list[position])

    inner class JobViewHolder(val binding: InflaterMenteeELearningBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: LearningResponseItem?) {


            val plainText: String = Html.fromHtml(item?.description).toString()

            val imageUrl = when (item?.type) {
                "image" -> E_Learning_BaseFile + item.fileUrl
                "video" -> E_Learning_BaseFile + item.fileUrl//"http://icons.iconarchive.com/icons/iconsmind/outline/256/Video-5-icon.png"
                else -> "https://cdn.iconscout.com/icon/premium/png-256-thumb/url-1775615-1511423.png"
            }


            var obj = item?.run {
                LearningResponseItem(
                    learningId,
                    articleLink,
                    type,
                    imageUrl,
                    titleName,
                    plainText,
                    url,
                    isActive,
                    addedBy,
                    createdDate


                )
            }


            binding.model = obj

            binding.detailId?.setOnClickListener {

                if (item != null) {
                    if (item.type == "pdf") {
                        "${E_Learning_BaseFile}${item.fileUrl}".let { it1 -> it?.context?.browse(it1) }
                    } else {
                        it?.context?.startActivity<MenteeElearningDetailsActivity>(INTENT_ELEARNING to obj)
                    }
                }
            }
        }
    }

}