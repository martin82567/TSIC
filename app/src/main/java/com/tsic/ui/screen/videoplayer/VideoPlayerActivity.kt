package com.tsic.ui.screen.videoplayer

import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivitySplashBinding
import com.tsic.databinding.ActivityVideoPlayerBinding
import com.tsic.util.extension.playVideo

class VideoPlayerActivity : AppCompatActivity() {

    val binding by lazy {
        DataBindingUtil.setContentView<ActivityVideoPlayerBinding>(
            this,
            R.layout.activity_video_player
        )
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
    }

    override fun onResume() {
        super.onResume()
        val file = intent.getStringExtra("file")
        playVideo(
            binding.videoPlayer,
            file ?: ""
        )
    }

    override fun onPause() {
        super.onPause()
        binding.videoPlayer.player?.stop()
        binding.videoPlayer.player?.release()
    }

}