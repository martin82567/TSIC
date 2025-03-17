package com.tsic.ui.common

/**
 * @author Kaiser Perwez
 */

import android.os.Build
import android.text.Html
import android.text.method.ScrollingMovementMethod
import android.view.MotionEvent
import android.widget.ImageView
import android.widget.TextView
import androidx.databinding.BindingAdapter
import com.tsic.R
import com.tsic.util.GlideApp

object BindingUtils {
    /*  @JvmStatic
      @BindingAdapter("numtext")
      fun EditText.numToText(value: Int) {
          this.setText(value.toString())
      }

      @JvmStatic
      @InverseBindingAdapter(attribute = "numtext")
      fun EditText.TextToNum(): Int {
          try {
              return this.text.toString().toInt()
          } catch (ex: Exception) {
              return 1000
          }
      }

      @JvmStatic
      @BindingAdapter("numtextAttrChanged")
      fun EditText.addListener(listener: InverseBindingListener) {
          this.addTextChangedListener(object : TextWatcher {
              override fun afterTextChanged(p0: Editable?) {

              }

              override fun beforeTextChanged(p0: CharSequence?, p1: Int, p2: Int, p3: Int) {
              }

              override fun onTextChanged(p0: CharSequence?, p1: Int, p2: Int, p3: Int) {
                      if (p1!=p2 && (p2>0 || p3>0))
                          listener.onChange()
              }

          })
      }*/


    @JvmStatic
    @BindingAdapter(value = ["image_url"], requireAll = false)
    fun ImageView.setImage(imageUrl: String?) {
        if (imageUrl == null) return

        GlideApp.with(this.context).load(imageUrl)
            .placeholder(if (imageUrl == "camera") R.drawable.ic_camera else R.drawable.loader)
            .into(this)
    }

    @JvmStatic
    @BindingAdapter(value = ["image_url_circular"], requireAll = false)
    fun ImageView.setImageCircular(imageUrlCircular: String?) {
        if (imageUrlCircular?.endsWith("/") == true) return
        GlideApp.with(this.context).load(imageUrlCircular)
            .placeholder(if (imageUrlCircular == "avatar") R.drawable.ic_camera else R.drawable.loader)
            .circleCrop()
            .into(this)
    }

    @JvmStatic
    @BindingAdapter(value = ["image_url_circular_profile"], requireAll = false)
    fun ImageView.setImageCircularProfile(imageUrlCircular: String?) {
        if (imageUrlCircular?.endsWith("/") == true) return
        GlideApp.with(this.context).load(imageUrlCircular)
            .placeholder(R.drawable.ic_avatar_all)
            .circleCrop()
            .into(this)
    }

    @JvmStatic
    @BindingAdapter(value = ["html_text"], requireAll = true)
    fun TextView.loadHtmlOnTextView(text: String?) {
        this.text = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
            Html.fromHtml(text?.trim(), Html.FROM_HTML_MODE_COMPACT)
        } else// ?: "No HTML Content"
        {
            Html.fromHtml(text?.trim()) ?: ""
        }
    }

    @JvmStatic
    @BindingAdapter(value = ["scrolling_method"], requireAll = true)
    fun TextView.setScrollingMethod(enable : Boolean) {
        if (enable){
            this.movementMethod = ScrollingMovementMethod()
            this.setOnTouchListener { v, event ->
                when(event.action){
                    MotionEvent.ACTION_DOWN -> {
                        this.parent.requestDisallowInterceptTouchEvent(true)
                    }
                    MotionEvent.ACTION_UP -> {
                        this.parent.requestDisallowInterceptTouchEvent(false)
                    }
                    else -> true
                }
                false
            }
        }
    }

}
