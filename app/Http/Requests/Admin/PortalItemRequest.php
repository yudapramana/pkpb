<?php

namespace App\Http\Requests\Admin;

class PortalItemRequest extends AdminFormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'title' => trim((string) $this->input('title')),
            'image_url' => $this->filled('image_url') ? trim($this->input('image_url')) : null,
            'destination_url' => $this->filled('destination_url') ? trim($this->input('destination_url')) : null,
            'sort_order' => $this->filled('sort_order') ? $this->input('sort_order') : 0,
            'is_modal' => $this->boolean('is_modal'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp,gif', 'max:2048'],
            'image_url' => ['nullable', 'string', 'max:2000', 'regex:#^(https?://\S+|/(?!/)\S+)$#'],
            'remove_image' => ['nullable', 'boolean'],
            'destination_url' => ['nullable', 'url', 'max:2000'],
            'is_modal' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Judul',
            'image' => 'Gambar ikon',
            'image_url' => 'URL gambar',
            'destination_url' => 'URL tujuan',
            'sort_order' => 'Urutan',
        ];
    }

    public function messages()
    {
        return array_merge(parent::messages(), [
            'url' => ':attribute harus berupa URL lengkap (diawali http:// atau https://).',
            'image_url.regex' => 'URL gambar harus diawali http://, https://, atau / (path lokal, mis. /images/portal/ikon.svg).',
        ]);
    }

    /** Data kolom tabel (tanpa field unggahan). */
    public function itemData()
    {
        return $this->safe()->only([
            'title', 'image_url', 'destination_url', 'is_modal', 'sort_order', 'is_active',
        ]);
    }
}
