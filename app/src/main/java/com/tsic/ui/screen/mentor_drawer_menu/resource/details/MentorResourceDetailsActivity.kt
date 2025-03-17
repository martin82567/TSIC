package com.tsic.ui.screen.mentor_drawer_menu.resource.details

/**
 * @author Kaiser Perwez
 */

//import com.github.tcking.giraffecompressor.GiraffeCompressor
import android.content.res.Configuration
import android.os.Bundle
import android.text.method.LinkMovementMethod
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.data.model.mentor_api.ELearning
import com.tsic.databinding.ActivityMentorResourceDetailsBinding
import com.tsic.ui.screen.videoplayer.VideoPlayerActivity
import com.tsic.util.INTENT_ELEARNING
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.browse
import org.jetbrains.anko.configuration
import org.jetbrains.anko.startActivity
import org.jetbrains.anko.toast

class MentorResourceDetailsActivity : AppCompatActivity() {

    //declarations

    internal val binding by lazy {
        DataBindingUtil.setContentView<ActivityMentorResourceDetailsBinding>(
            this,
            R.layout.activity_mentor_resource_details
        )
    }
    val e_learning_model by lazy { intent.getParcelableExtra(INTENT_ELEARNING) as ELearning? }

    //methods
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()
    }

    private fun initUiAndListeners() {
        when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
            Configuration.UI_MODE_NIGHT_NO -> {
                binding?.rootLayout?.setBackgroundResource(R.drawable.bg_all_white)
            } // Night mode is not active, we're using the light theme
            Configuration.UI_MODE_NIGHT_YES -> {
                binding?.rootLayout?.setBackgroundResource(R.drawable.bg3)
            } // Night mode is active, we're using dark theme
        }
        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            title = "Details"
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }
        setStatusBarColor(R.color.colorStatusTranslucentGreen)
        binding?.apply {
            model = e_learning_model
            checkLearningType()
            //Setting Hyperlink in TextView via code and xml
            binding?.contentLayout?.tVLearningModuleDesc?.movementMethod =
                LinkMovementMethod.getInstance()
        }


    }


    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
    }

    fun showToast(msg: String?) {
        msg?.let { toast(it).show() }
    }

    private fun checkLearningType() {
        when (e_learning_model?.type?.toLowerCase()) {
            "url" -> {

                binding.contentLayout.tVLearningModuleUrl.setOnClickListener {
                    e_learning_model?.url?.let { it1 -> it?.context?.browse(it1) }
                }
                setViewVisibility(View.GONE, View.GONE, View.VISIBLE)
            }
            "video" -> {
                setViewVisibility(View.GONE, View.VISIBLE, View.GONE)
                /*binding?.contentLayout?.videoLearningModule?.let {
                    playVideo(
                        it,
                        e_learning_model?.file ?: ""
                    )
                }*/
                binding?.contentLayout?.videoLearningModule2?.setOnClickListener {
                    this.startActivity<VideoPlayerActivity>("file" to e_learning_model?.file)
                }
            }
            "image" -> {

                setViewVisibility(View.VISIBLE, View.INVISIBLE, View.GONE)
            }
        }
    }

    private fun setViewVisibility(image: Int, video: Int, url: Int) {
        binding.contentLayout.apply {
            iVLearningModuleImage.visibility = image
            videoLearningModule.visibility = image
            videoLearningModule2.visibility = video
            tVLearningModuleUrl.visibility = url

        }
    }
}