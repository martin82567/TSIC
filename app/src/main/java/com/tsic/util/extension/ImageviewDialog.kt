package com.tsic.util.extension

/**
 * @author Kaiser Perwez
 */

import android.content.DialogInterface
import android.view.View
import org.jetbrains.anko.AnkoContext
import org.jetbrains.anko.alert

private const val ACTIVITY_PADDING = 16

class ImageviewDialog(ui: AnkoContext<View>, url: String) {

    var dialog: DialogInterface


    init {
        with(ui) {
            dialog = alert {
                /*
                                customView {
                                    relativeLayout {
                                        gravity=Gravity.CENTER
                                        imageView() {
                                            loadUrl(url)
                                        }.lparams {
                                            width= 100
                                            height= 100
                                        }
                                    }
                                }
                */
            }.show()
        }
    }
}
