package com.tsic.util.extension

import android.content.DialogInterface
import android.view.Gravity
import android.view.View
import android.widget.LinearLayout
import android.widget.TextView
import com.google.android.material.textfield.TextInputEditText
import org.jetbrains.anko.*
import org.jetbrains.anko.design.textInputEditText
import org.jetbrains.anko.design.textInputLayout

private const val ACTIVITY_PADDING = 16

class AddNoteDialog(ui: AnkoContext<View>) {

    var dialog: DialogInterface
    lateinit var noteText: TextInputEditText
    lateinit var cancelButton: TextView
    lateinit var okButton: TextView

    init {
        with(ui) {
            dialog = alert {

                customView {
                    verticalLayout {
                        padding = dip(ACTIVITY_PADDING)

                        textView("Note") {
                            textSize = 18f
                        }.lparams {
                            bottomMargin = dip(ACTIVITY_PADDING)
                        }

                        textInputLayout {
                            hint = "Enter note"
                            noteText = textInputEditText {
                                textSize = 16f
                                maxLines = 5
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