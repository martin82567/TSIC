package com.tsic.ui.screen.pdf_viewer

import android.content.res.Configuration
import android.os.Bundle
import android.util.Log
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.appcompat.app.AppCompatActivity
import androidx.databinding.DataBindingUtil
import com.tsic.R
import com.tsic.databinding.ActivityPdfViewerBinding
import com.tsic.util.extension.setStatusBarColor
import org.jetbrains.anko.configuration
import org.jetbrains.anko.indeterminateProgressDialog

class PdfViewerActivity : AppCompatActivity() {
    private val binding by lazy {
        DataBindingUtil.setContentView<ActivityPdfViewerBinding>(
            this,
            R.layout.activity_pdf_viewer
        )
    }
    val progressDialog by lazy {
        indeterminateProgressDialog("Loading...").apply {
            setCancelable(false)
        }
    }
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding?.rootLayout?.setBackgroundResource(
            when (configuration.uiMode and Configuration.UI_MODE_NIGHT_MASK) {
                Configuration.UI_MODE_NIGHT_NO ->
                    R.drawable.bg_all_white
                Configuration.UI_MODE_NIGHT_YES ->
                    R.drawable.bg3
                else -> R.drawable.bg_all_white
            }
        )
        setStatusBarColor(R.color.colorStatusTranslucentGreen)
        setSupportActionBar(binding.toolbar)
        supportActionBar?.apply {
            title = intent?.getStringExtra("title")
            setDisplayHomeAsUpEnabled(true)
            setDisplayShowHomeEnabled(true)
        }
        binding?.apply {
            progressDialog.show()
            webView.webViewClient = WebViewClient()
            webView.settings.setSupportZoom(true)
            webView.settings.javaScriptEnabled = true
            intent?.getStringExtra("url")?.let {
                Log.d("TAG", "url: $it")
                webView.loadUrl(it)
                //webView.loadData(it, "text/html", "UTF-8")
//                webView.loadDataWithBaseURL(it, "text/html", "UTF-8",null,null)
            }
            webView.webViewClient = object :WebViewClient(){
                override fun onPageFinished(view: WebView?, url: String?) {
                    super.onPageFinished(view, url)
                    progressDialog.dismiss()
                }
            }
        //            WebViewClient() {
//                public boolean shouldOverrideUrlLoading(WebView view, String url) {
//                    Log.i(TAG, "Processing webview url click...");
//                    view.loadUrl(url);
//                    return true;
//                }
//
//                public void onPageFinished(WebView view, String url) {
//                    Log.i(TAG, "Finished loading URL: " + url);
//                    if (progressBar.isShowing()) {
//                        progressBar.dismiss();
//                    }
//                }

            }

    }
    override fun onSupportNavigateUp(): Boolean {
        onBackPressed()
        return true
    }
}