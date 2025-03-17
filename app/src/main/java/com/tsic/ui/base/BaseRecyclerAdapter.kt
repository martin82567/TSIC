package com.tsic.ui.base

/**
 * @author Kaiser Perwez
 */
 

import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView

//if your derived class needs removal or updation of list items,pass mutable list to adapter at derived class.
//For this base class,keep the list to be immutable to prevent accidental updation

abstract class BaseRecyclerAdapter<out T>(val list: List<T>) : RecyclerView.Adapter<RecyclerView.ViewHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): RecyclerView.ViewHolder {
        return onCreateViewHolderBase(parent, viewType)
    }

    abstract fun onCreateViewHolderBase(parent: ViewGroup?, viewType: Int): RecyclerView.ViewHolder

    override fun onBindViewHolder(holder: RecyclerView.ViewHolder, position: Int) {
        onBindViewHolderBase(holder, position)
    }

    abstract fun onBindViewHolderBase(holder: RecyclerView.ViewHolder?, position: Int)

    override fun getItemCount(): Int {
        return if (list.isNotEmpty()) list.size else 0
    }

    fun getdata(): List<T> {
        return list
    }

}
