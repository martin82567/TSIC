package com.tsic.util.extension

/**
 * @author Kaiser Perwez
 */
 
import android.content.DialogInterface
import android.view.Gravity
import android.view.View
import android.view.ViewManager
import android.widget.LinearLayout
import android.widget.TextView
import com.google.android.material.textfield.TextInputEditText
import com.google.android.material.textfield.TextInputLayout
import org.jetbrains.anko.*
import org.jetbrains.anko.custom.ankoView
import org.jetbrains.anko.design.textInputEditText
import org.jetbrains.anko.design.textInputLayout

private const val ACTIVITY_PADDING = 16

class ForgetPasswordDialog(ui: AnkoContext<View>) {

    var dialog: DialogInterface
    lateinit var emailText: TextInputEditText
    lateinit var cancelButton: TextView
    lateinit var okButton: TextView

    init {
        with(ui) {
            dialog = alert {

                customView {
                    verticalLayout {
                        padding = dip(ACTIVITY_PADDING)

                        textView("Forgot Password") {
                            textSize = 18f
                        }.lparams {
                            bottomMargin = dip(ACTIVITY_PADDING)
                        }
/*
//new 0.10.6 has some issues with the 'lparams' of textview, but not with of linearlayout
                        linearLayout {
                            textView("Forgot Password") {
                                textSize = 18f
                            }.lparams {
                                bottomMargin = dip(ACTIVITY_PADDING)
                            }
                        }
                        */
                        textInputLayout {
                            hint = "Enter email address"
                            emailText = textInputEditText {
                                textSize = 16f
                            }
                        }

                        linearLayout {
                            topPadding = dip(24)
                            orientation = LinearLayout.HORIZONTAL
                            horizontalGravity = Gravity.END

                            cancelButton = textView("Cancel") {
                                textSize = 16f
                            }.lparams {
                                marginEnd = dip(ACTIVITY_PADDING)
                            }

                            okButton = textView("Send") {
                                textSize = 16f
                            }


                        }
                    }
                }
            }.show()
        }
    }
}

inline fun ViewManager.textInputEditText(theme: Int = 0, init: TextInputEditText.() -> Unit) =
    ankoView({ TextInputEditText(it) }, theme, init)

inline fun ViewManager.textInputLayout(theme: Int = 0, init: TextInputLayout.() -> Unit) =
    ankoView({ TextInputLayout(it) }, theme, init)
