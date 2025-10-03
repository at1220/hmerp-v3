<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enum\Company;
use App\Enum\CompanyPayment;
use App\Enum\Department;
use App\Enum\DrivingLicences;
use App\Enum\Gender;
use App\Enum\Level;
use App\Enum\MaritalStatus;
use App\Enum\Position;
use App\Enum\TypeContact;
use App\Enum\UserStatus;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Họ và tên')
                    ->required(),
                TextInput::make('email')
                    ->label('Sđt đăng nhập')
                    ->visibleOn(Operation::Edit)

                    ->disabled() // ⛔ Không cho chỉnh sửa
                    ->dehydrated(false),
                TextInput::make('email')
                    ->label('Sđt đăng nhập')
                    ->tel()
                    ->visibleOn(Operation::Create)
                    ->required(),
                TextInput::make('first_password')
                    ->label('Mật khẩu ban đầu')
                    ->password()
                    ->revealable()
                    ->disabled() // ⛔ Không cho chỉnh sửa
                    ->dehydrated(false), // ❌ Không gửi lên server khi submit
                Select::make('status')
                    ->options(UserStatus::class)
                    ->default('active')
                    ->required(),
                Section::make('Thông tin nhân viên')
                    ->relationship('staff')
                    ->schema([
                        TextInput::make('phone')
                            ->label('Sđt')
                            ->tel()
                            ->visibleOn(Operation::Edit)
                            ->required(),
                        Select::make('gender')
                            ->label('Giới tính')
                            ->options(Gender::class)
                            ->required(),
                        TextInput::make('identity_number')
                            ->label('Số CCCD/CMND')
                            ->required()
                            ->rules([
                                'digits:12', // ✅ Phải đúng 12 chữ số
                            ])->validationMessages([
                                'digits' => 'Số CCCD/CMND phải có đúng 12 chữ số.',
                            ]),
                        DatePicker::make('date_insurance')
                            ->label('Ngày cấp CCCD/CMND')
                            ->displayFormat('d/m/Y')
                            ->format('Y-m-d')
                            ->required(),
                        TextInput::make('issuance_place')
                            ->label('Nơi cấp CCCD/CMND')
                            ->required(),
                        DatePicker::make('birthday')
                            ->label('Ngày sinh')
                            ->displayFormat('d/m/Y')
                            ->format('Y-m-d')
                            ->required(),
                        TextInput::make('birthplace')
                            ->label('Nơi sinh')
                            ->required(),
                        TextInput::make('native_land')
                            ->label('Quê quán')
                            ->required(),
                        TextInput::make('household')
                            ->label('Hộ khẩu thường trú')
                            ->required(),
                        TextInput::make('staying')
                            ->label('Tạm trú'),
                        Select::make('marital_status')
                            ->label('Tình trạng hôn nhân')
                            ->options(MaritalStatus::class)
                            ->required(),
                        TextInput::make('nationality')
                            ->label('Quốc tịch'),
                        TextInput::make('folk')
                            ->label('Dân tộc'),
                        TextInput::make('religion')
                            ->label('Tôn giáo'),
                        Select::make('level')
                            ->label('Trình độ học vấn')
                            ->options(Level::class)
                            ->default('highschool')
                            ->required(),
                        Select::make('type_contact')
                            ->label('Loại hợp đồng')
                            ->options(TypeContact::class)
                            ->default('fulltime')
                            ->required(),
                        Select::make('position')
                            ->label('Chức vụ')
                            ->options(Position::class)
                            ->default('staff')
                            ->required(),
                        TextInput::make('paid_leave')
                            ->label('Số ngày nghỉ phép trong năm')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        TextInput::make('insurance_salary_month')
                            ->label('Mức đóng BHXH')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Select::make('company')
                            ->label('Công ty')
                            ->options(Company::class)
                            ->required(),
                        Select::make('payment_account')
                            ->label('Tk cty thanh toán')
                            ->options(CompanyPayment::class)
                            ->required(),
                        Select::make('personnel_management')
                            ->label('Quản lý nhân sự')
                            ->options(User::pluck('name', 'id'))

                            ->multiple(),
                        DatePicker::make('date_in')
                            ->label('Ngày vào công ty')
                            ->displayFormat('d/m/Y')
                            ->format('Y-m-d')
                            ->required(),
                        DatePicker::make('date_out')
                            ->label('Ngày nghỉ việc')
                            ->displayFormat('d/m/Y')
                            ->format('Y-m-d'),
                        Select::make('driving_licences')
                            ->label('Bằng lái xe')
                            ->options(DrivingLicences::class),
                        Select::make('outside_truck')
                            ->label('Xe ngoài')
                            ->options([
                                false => 'Không',
                                true => 'Có',

                            ])
                            ->required(),
                        Select::make('has_evaluate')
                            ->label('Đánh giá tháng')
                            ->options([
                                false => 'Không',
                                true => 'Có',

                            ])
                            ->required(),
                        TextInput::make('personal_deduction')
                            ->label('Số người phụ thuộc giảm trừ gia cảnh')
                            ->numeric()
                            ->required(),
                        Select::make('department')
                            ->label('Phòng ban')
                            ->options(Department::class)
                            ->required(),
                    ])->columns(2)->columnSpanFull(),

            ]);
    }
}
