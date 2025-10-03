<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;

class Login extends BaseLogin
{
    /**
     * Custom field hiển thị trong form login
     */
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('Số điện thoại') // 👈 bạn đổi tên nhãn theo ý
            ->required()
            ->autofocus();
    }

    /**
     * Custom lại cách lấy credentials để login
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['login'],  // 👈 map tới cột "phone" trong bảng users
            'password' => $data['password'],
        ];
    }
}
