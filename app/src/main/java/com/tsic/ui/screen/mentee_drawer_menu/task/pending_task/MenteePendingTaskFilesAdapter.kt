package com.tsic.ui.screen.mentee_drawer_menu.task.pending_task


import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.bumptech.glide.request.RequestOptions
import com.tsic.R
import com.tsic.data.local.prefs.KEY_AUTH_TOKEN
import com.tsic.data.local.prefs.PreferenceHelper
import com.tsic.data.local.prefs.USER_PREF
import com.tsic.data.model.mentee_api.Uploadedfile
import com.tsic.data.remote.api.MenteeApiService
import com.tsic.data.remote.api.TASK_IMAGE_URL
import com.tsic.databinding.InflaterMenteeImagesBinding
import com.tsic.ui.base.BaseRecyclerAdapter
import com.tsic.ui.screen.util_screens.FullscreenImageActivity
import com.tsic.util.INTENT_KEY_TITLE
import com.tsic.util.INTENT_KEY_URL
import com.tsic.util.extension.isDeviceOnline
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.schedulers.Schedulers
import org.jetbrains.anko.*

class MenteePendingTaskFilesAdapter(
    val mediaList: MutableList<Uploadedfile?>,
    val activity: MenteePendingTaskDetailsActivity
) :
    BaseRecyclerAdapter<Uploadedfile?>(mediaList) {

    private val apiService by lazy {
        MenteeApiService.create()
    }

    override fun onCreateViewHolderBase(
        parent: ViewGroup?,
        viewType: Int
    ): RecyclerView.ViewHolder {
        val binding = DataBindingUtil.inflate<InflaterMenteeImagesBinding>(
            LayoutInflater.from(parent?.context),
            R.layout.inflater_mentee_images,
            parent,
            false
        )
        return MenteeTaskFilesHolder(binding)
    }

    override fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int) =
        (holder as MenteeTaskFilesHolder).bind(position)

    private fun removeMedia(view: View, position: Int) {
        val userPrefs by lazy {
            PreferenceHelper.customPrefs(view.context, USER_PREF)
        }

        if (!view.context.isDeviceOnline()) {
            view.context?.toast("No internet connection.")
            return
        }

        val dialog = view.context.indeterminateProgressDialog("Deleting data...").apply {
            setCancelable(false)
        }

        apiService.deleteMedia(
            userPrefs?.getString(KEY_AUTH_TOKEN, ""),
            mediaList.get(position)?.id.toString()
        ).subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread())
            .doOnSubscribe { dialog.show() }
            .doAfterTerminate { dialog.dismiss() }
            .subscribe(
                { result ->
                    activity.apply {
                        showToast(result?.message)
                        binding?.vm?.getTaskDetails(activity.taskId)
                    }
                },
                { error ->
                    activity.showToast(error?.message)
                    dialog.dismiss()
                }
            )
    }

    inner class MenteeTaskFilesHolder(val binding: InflaterMenteeImagesBinding) :
        RecyclerView.ViewHolder(binding.root) {
        fun bind(position: Int) {

            mediaList[position]?.apply {
                if (id == 0) {
                    Glide.with(activity)
                        .load(R.drawable.ic_add_files)
                        .apply(RequestOptions.circleCropTransform())
                        .into(binding.ivTipImage)
                    binding.ivCancel?.visibility = View.GONE
                    binding.ivTipImage?.setOnClickListener {
                        activity.selectImages()
                    }
                } else {
                    binding.model = Uploadedfile(
                        "$TASK_IMAGE_URL$fileName",
                        addedBy,
                        createdDate,
                        goaltaskId,
                        id
                    )

                    binding.ivTipImage?.setOnClickListener {
                        activity.startActivity<FullscreenImageActivity>(

                            INTENT_KEY_TITLE to "TASK FILES",
                            INTENT_KEY_URL to "$TASK_IMAGE_URL$fileName"
                        )
                    }
                }
            }

            binding.ivCancel.setOnClickListener {
                activity.alert("Sure to delete?") {
                    yesButton { dialog ->
                        if (mediaList[position]?.id == 0) {
                            mediaList.removeAt(position)
                            notifyDataSetChanged()
                        } else {
                            removeMedia(it, position)
                        }
                    }
                    noButton { }
                }.show()

            }
        }
    }
}
