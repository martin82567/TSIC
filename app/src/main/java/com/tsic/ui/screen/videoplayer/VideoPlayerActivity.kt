package com.tsic.ui.screen.videoplayer

import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import com.tsic.R
import com.tsic.util.extension.playVideo
import kotlinx.android.synthetic.main.activity_video_player.*

class VideoPlayerActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_video_player)
    }

    override fun onResume() {
        super.onResume()
        val file = intent.getStringExtra("file")
        playVideo(
            video_player,
            file ?: ""
        )
    }

    override fun onPause() {
        super.onPause()
        video_player?.player?.stop()
        video_player?.player?.release()
    }

}