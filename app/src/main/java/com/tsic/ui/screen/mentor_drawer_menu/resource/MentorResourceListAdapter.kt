package com.tsic.ui.screen.mentor_drawer_menu.resource


import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentor_api.ELearning
import com.tsic.data.remote.api.E_Learning_BaseFile
import com.tsic.databinding.InflaterMentorResourceBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.mentor_drawer_menu.resource.details.MentorResourceDetailsActivity
import com.tsic.util.INTENT_ELEARNING
import org.jetbrains.anko.browse
import org.jetbrains.anko.startActivity


class MentorResourceListAdapter(listElearning: List<ELearning?>) :
    BaseRecyclerAdapter<ELearning?>(listElearning) {

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {
        val binding = DataBindingUtil.inflate<InflaterMentorResourceBinding>(
            LayoutInflater.from(parent?.context),
            R.layout.inflater_mentor_resource,
            parent,
            false
        )
        return JobViewHolder(binding)
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) =
        (holder as JobViewHolder).bind(list[position])

    inner class JobViewHolder(val binding: InflaterMentorResourceBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: ELearning?) {

            val imageUrl = when (item?.type) {
                "image" -> E_Learning_BaseFile + item.file
                "video" -> E_Learning_BaseFile + item.file//"http://icons.iconarchive.com/icons/iconsmind/outline/256/Video-5-icon.png"
                else -> "https://cdn.iconscout.com/icon/premium/png-256-thumb/url-1775615-1511423.png"
            }


            var obj = item?.run {
                ELearning(
                    affiliate_id,
                    description,
                    imageUrl,
                    id,
                    name,
                    type,
                    url
                )
            }


            binding.model = obj
            binding.detailId.setOnClickListener {
                if (item != null) {
                    if (item.type == "pdf") {
                        "${E_Learning_BaseFile}${item.file}".let { it1 -> it?.context?.browse(it1) }
                    } else {
                        it?.context?.startActivity<MentorResourceDetailsActivity>(
                            INTENT_ELEARNING to obj
                        )
                    }
                }

            }
        }
    }

}