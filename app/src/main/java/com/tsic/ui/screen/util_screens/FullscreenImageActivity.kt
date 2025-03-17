package com.tsic.ui.screen.util_screens

import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityFullscreenImageBinding
import com.tsic.util.INTENT_KEY_TITLE
import com.tsic.util.INTENT_KEY_URL
import com.tsic.util.extension.setStatusBarColor

class FullscreenImageActivity : AppCompatActivity() {
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityFullscreenImageBinding>(
            this,
            R.layout.activity_fullscreen_image
        )
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        initUiAndListeners()
    }

    private fun initUiAndListeners() {
        val filename = intent.getStringExtra(INTENT_KEY_TITLE)
        val fileUrl = intent.getStringExtra(INTENT_KEY_URL)

        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            title = filename
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }
        setStatusBarColor(R.color.colorStatusTranslucentGreen)
        binding?.url = fileUrl
    }

    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
    }
}
