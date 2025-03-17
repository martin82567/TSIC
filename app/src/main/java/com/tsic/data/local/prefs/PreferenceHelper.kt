package com.tsic.data.local.prefs

import android.content.Context
import android.content.SharedPreferences

object PreferenceHelper {

    private var sharedPreferences: SharedPreferences? = null

    fun getSharedPrefs(context: Context): SharedPreferences? {
        if (sharedPreferences == null) {
            sharedPreferences = context.getSharedPreferences(PREFS_FILENAME_DEFAULT, Context.MODE_PRIVATE)
        }
        return sharedPreferences
    }

    fun customPrefs(context: Context, name: String): SharedPreferences? {
        if (sharedPreferences == null) {
            sharedPreferences = context.getSharedPreferences(PREFS_FILENAME_DEFAULT, Context.MODE_PRIVATE)
        }
        return sharedPreferences
    }

    inline fun SharedPreferences.edit(operation: (SharedPreferences.Editor) -> Unit) {
        val editor = this.edit()
        operation(editor)
        editor.apply()
    }

    fun setData(key: String, value: Any?) {
        sharedPreferences?.set(key, value)
    }

    operator fun SharedPreferences.set(key: String, value: Any?) {
        when (value) {
            is String? -> edit { it.putString(key, value) }
            is Int -> edit { it.putInt(key, value) }
            is Boolean -> edit { it.putBoolean(key, value) }
            is Float -> edit { it.putFloat(key, value) }
            is Double -> edit { it.putFloat(key, value.toFloat()) }
            is Long -> edit { it.putLong(key, value) }
            else -> throw UnsupportedOperationException("Not yet implemented")
        }
    }

    /**
     * finds value on given key.
     * [T] is the type of value
     * @param defaultValue optional default value - will take null for strings, false for bool and -1 for numeric values if [defaultValue] is not specified
     */

    inline operator fun <reified T : Any> SharedPreferences.get(key: String, defaultValue: T? = null): T? {
        return when (T::class) {
            String::class -> getString(key, defaultValue as? String) as T?
            Int::class -> getInt(key, defaultValue as? Int ?: -1) as T?
            Boolean::class -> getBoolean(key, defaultValue as? Boolean ?: false) as T?
            Float::class -> getFloat(key, defaultValue as? Float ?: -1f) as T?
            Long::class -> getLong(key, defaultValue as? Long ?: -1) as T?
            else -> throw UnsupportedOperationException("Not yet implemented")
        }
    }

    fun clearSharedPreferences(sharedPreferences: SharedPreferences?) {
        sharedPreferences?.edit {
            it.clear()
        }
    }
}
