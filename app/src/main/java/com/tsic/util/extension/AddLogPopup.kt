package com.tsic.util.extension
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
class AddLogPopup (ui: AnkoContext<View>){
    var dialog: DialogInterface
    lateinit var cancelButton: TextView
    lateinit var okButton: TextView

    init {
        with(ui) {
            dialog = alert {

                customView {
                    verticalLayout {
                        padding = dip(ACTIVITY_PADDING)

                        textView("Would you like to save Logs") {
                            textSize = 18f
                        }.lparams {
                            bottomMargin = dip(ACTIVITY_PADDING)
                        }

                        linearLayout {
                            topPadding = dip(24)
                            orientation = LinearLayout.HORIZONTAL
                            horizontalGravity = Gravity.END

                            cancelButton = textView("No") {
                                textSize = 16f
                            }.lparams {
                                marginEnd = dip(ACTIVITY_PADDING)
                            }

                            okButton = textView("Yes") {
                                textSize = 16f
                            }
                        }
                    }
                }
            }.show()
        }
    }

}
