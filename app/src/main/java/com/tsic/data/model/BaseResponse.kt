package com.tsic.data.model

/**
 * @author Kaiser Perwez
 */

data class BaseResponse<out T>(
    val status: Boolean,
    val message: String?,
    val data: T?,
    val error: Throwable?
)
