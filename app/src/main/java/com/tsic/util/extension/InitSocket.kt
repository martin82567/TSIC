package com.tsic.util.extension

import io.socket.client.IO
import io.socket.client.Socket
import okhttp3.OkHttpClient
import java.security.cert.X509Certificate
import javax.net.ssl.HostnameVerifier
import javax.net.ssl.SSLContext
import javax.net.ssl.X509TrustManager

class InitSocket {
    fun initSocket(url: String): Socket {
        val myHostnameVerifier = HostnameVerifier { hostname, session -> true }

        val trustManager = object : X509TrustManager {
            override fun checkClientTrusted(chain: Array<out X509Certificate>?, authType: String?) {
            }

            override fun checkServerTrusted(chain: Array<out X509Certificate>?, authType: String?) {
            }

            override fun getAcceptedIssuers(): Array<X509Certificate> {
                return emptyArray()
            }

        }
        val trustAllCerts = arrayOf(trustManager)

        val mySSLContext = SSLContext.getInstance("SSL")
        mySSLContext.init(null, trustAllCerts, null)

        val httpBuilder = OkHttpClient.Builder().apply {
            readTimeout(120, java.util.concurrent.TimeUnit.SECONDS)
            connectTimeout(120, java.util.concurrent.TimeUnit.SECONDS)
            writeTimeout(120, java.util.concurrent.TimeUnit.SECONDS)
        }


        val client = httpBuilder
            .hostnameVerifier(myHostnameVerifier)
            .sslSocketFactory(mySSLContext.socketFactory, trustManager)
            .build()

        val opts = IO.Options()
        opts.callFactory = client
        opts.webSocketFactory = client

        return IO.socket(url, opts)
    }
}