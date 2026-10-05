<?php

namespace App\Http\Requests;

use App\Enums\MembershipStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('member'));
    }

    public function rules(): array
    {
        $memberId = $this->route('member')?->id;

        return [
            'member_number'     => ['required', 'string', 'max:20', "unique:members,member_number,{$memberId}"],
            'full_name'         => ['required', 'string', 'max:100'],
            'address'           => ['required', 'string', 'max:500'],
            'phone'             => ['nullable', 'string', 'max:20'],
            'membership_status' => ['required', new Enum(MembershipStatus::class)],
            'notes'             => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'member_number.unique' => 'Nomor anggota sudah digunakan oleh anggota lain.',
            'full_name.required'   => 'Nama lengkap wajib diisi.',
            'address.required'     => 'Alamat wajib diisi.',
        ];
    }
}
