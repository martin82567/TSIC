package com.tsic.util.extension

/**
 * @author Kaiser Perwez
 */

import android.app.Activity
import android.content.Context
import android.graphics.drawable.Drawable
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.view.WindowManager
import android.widget.ImageView
import androidx.annotation.LayoutRes
import com.tsic.R
import com.tsic.util.GlideApp

/*fun AppCompatActivity?.setToolbar(
    title: String?,
    hideHome: Boolean = false,
    textColor: Int = Color.BLACK,
    drawable: Int = R.drawable.ic_navigate_before_white_24dp
) {
    this?.toolbar?.let {
        it.title = ""
        it.toolbar_title?.apply {
            text = title
            setTextColor(textColor)
        }

        *//*it.home?.let { it ->
            it.visibility = if (hideHome)
                View.GONE
            else
                View.VISIBLE

            it.setOnClickListener {
                startActivity(Intent(this, HomeActivity::class.java))
                finish()
            }
        }*//*

        it.setNavigationIcon(drawable)
        setSupportActionBar(it)
        supportActionBar?.setDisplayHomeAsUpEnabled(true)
    }
}*/

fun getPixelsFromDp(context: Context, dp: Float): Int {
    return Math.round(dp * context.resources.displayMetrics.density)
}

fun Activity?.removeStatusBar(hasFocus: Boolean) {
    if (hasFocus) {
        this?.let {
            window.decorView.systemUiVisibility = (View.SYSTEM_UI_FLAG_LAYOUT_STABLE
                    or View.SYSTEM_UI_FLAG_LAYOUT_HIDE_NAVIGATION
                    or View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN
                    or View.SYSTEM_UI_FLAG_HIDE_NAVIGATION
                    or View.SYSTEM_UI_FLAG_FULLSCREEN
                    or View.SYSTEM_UI_FLAG_IMMERSIVE_STICKY)
        }
    }
}

fun Activity?.setStatusBarColor(color: Int) {
    this?.let {
        val window = window
        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS)
        window.clearFlags(WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS)
        window.statusBarColor = resources.getColor(color)
    }
}

fun ViewGroup.inflate(@LayoutRes layoutRes: Int, attachToRoot: Boolean = false): View {
    return LayoutInflater.from(this.context).inflate(layoutRes, this, attachToRoot)
    //how to use---------->parent.inflate(R.layout.my_layout, true)
}

fun ImageView.loadUrl(imageUrl: String?, placeHolderDrawable: Drawable? = null) {
    if (imageUrl == null) return

    GlideApp.with(this.context).load(imageUrl).apply {
        if (placeHolderDrawable == null)
            placeholder(R.drawable.partial_logo_without_text)
        else
            placeholder(placeHolderDrawable)
    }
        .into(this)
}

