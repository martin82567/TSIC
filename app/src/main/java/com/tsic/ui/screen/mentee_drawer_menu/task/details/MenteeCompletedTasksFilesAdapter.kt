package com.tsic.ui.screen.mentee_drawer_menu.task.details


import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.tsic.R
import com.tsic.data.model.mentee_api.Uploadedfile
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.data.remote.api.TASK_IMAGE_URL
import com.tsic.databinding.InflaterAllImageviewBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.util_screens.FullscreenImageActivity
import com.tsic.util.INTENT_KEY_TITLE
import com.tsic.util.INTENT_KEY_URL
import org.jetbrains.anko.startActivity

class MenteeCompletedTasksFilesAdapter(
    val mediaList: MutableList<Uploadedfile?>,
    val activity: MenteeCompletedTaskDetailsActivity
) :
    BaseRecyclerAdapter<Uploadedfile?>(mediaList) {

    private val apiService by lazy {
        MenteeApiService.create()
    }

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {
        val binding = DataBindingUtil.inflate<InflaterAllImageviewBinding>(
            LayoutInflater.from(parent?.context),
            R.layout.inflater_all_imageview,
            parent,
            false
        )
        return MenteeTaskFilesHolder(binding)
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) =
        (holder as MenteeTaskFilesHolder).bind(mediaList[position])


    inner class MenteeTaskFilesHolder(val binding: InflaterAllImageviewBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(item: Uploadedfile?) {
            binding.url = "$TASK_IMAGE_URL${item?.fileName}"
            binding.imageView?.setOnClickListener {
                activity.startActivity<FullscreenImageActivity>(

                    INTENT_KEY_TITLE to "TASK FILES",
                    INTENT_KEY_URL to "$TASK_IMAGE_URL${item?.fileName}"
                )
            }
        }
    }


}
