<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Basis form request panel admin: pesan validasi berbahasa Indonesia.
 * Otorisasi: semua user yang login adalah admin (dijaga middleware "auth").
 */
abstract class AdminFormRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() !== null;
    }

    public function messages()
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'integer' => ':attribute harus berupa angka.',
            'boolean' => ':attribute tidak valid.',
            'array' => ':attribute tidak valid.',
            'in' => ':attribute tidak valid.',
            'not_in' => ':attribute tidak valid.',
            'exists' => ':attribute tidak ditemukan.',
            'unique' => ':attribute sudah terdaftar.',
            'digits' => ':attribute harus :digits digit angka.',
            'distinct' => ':attribute tidak boleh dipilih lebih dari sekali.',
            'image' => ':attribute harus berupa gambar.',
            'mimes' => ':attribute harus berformat :values.',
            'max' => [
                'string' => ':attribute maksimal :max karakter.',
                'file' => ':attribute maksimal :max KB.',
                'numeric' => ':attribute maksimal :max.',
                'array' => ':attribute maksimal :max item.',
            ],
            'min' => [
                'string' => ':attribute minimal :min karakter.',
                'file' => ':attribute minimal :min KB.',
                'numeric' => ':attribute minimal :min.',
                'array' => 'Minimal :min :attribute.',
            ],
        ];
    }
}
