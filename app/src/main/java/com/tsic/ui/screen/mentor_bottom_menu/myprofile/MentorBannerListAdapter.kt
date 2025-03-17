package com.tsic.ui.screen.mentor_bottom_menu.myprofile

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentor_api.AffiliateSystemMessaging
import com.tsic.databinding.InflaterMentorBannerListBinding
import com.tsic.ui.base.BaseRecyclerAdapter

class MentorBannerListAdapter(
    private val bannerList: List<AffiliateSystemMessaging?>,
) : BaseRecyclerAdapter<AffiliateSystemMessaging?>(bannerList) {


    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {

        return ListItemHolder(
            DataBindingUtil.inflate(
                LayoutInflater.from(parent?.context),
                R.layout.inflater_mentor_banner_list,
                parent,
                false
            )
        )
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) {
        (holder as ListItemHolder).bind(bannerList[position])
    }

    inner class ListItemHolder(val binding: InflaterMentorBannerListBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: AffiliateSystemMessaging?) {
            binding.model = item
        }

    }

}