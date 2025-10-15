<?php

namespace App\Filament\Resources\AbsenceRequests\Pages;

use App\Filament\Resources\AbsenceRequests\AbsenceRequestResource;
use App\Filament\Resources\AbsenceRequests\Schemas\AbsenceRequestForm;
use App\Models\AbsenceApproval;
use App\Models\AbsenceDay;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ListAbsenceRequests extends ListRecords
{
    protected static string $resource = AbsenceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('create_one_day')
                ->label('Nghỉ 1 ngày')
                ->schema(fn () => AbsenceRequestForm::configure(app(Schema::class), 'create_one_day')->getComponents())
                ->mutateDataUsing(function (array $data): array {
                    $fromDate = Carbon::parse($data['from_date']);

                    $data['to_date'] = $data['from_date'];
                    $data['user_id'] = Auth::id();
                    $data['created_by'] = Auth::id();
                    $data['status'] = 'pending';

                    // Nếu là Thứ 7 => total_day = 0.5
                    if ($fromDate->dayOfWeek === Carbon::SATURDAY) {
                        $data['total_day'] = 0.5;
                    } else {
                        // Còn lại: full day hay half day theo part_of_day
                        $data['total_day'] = $data['part_of_day'] === 'day' ? 1 : 0.5;
                    }

                    return $data;
                })
                ->using(function (array $data, string $model): Model {
                    unset($data['date']);
                    unset($data['part_of_day']);

                    return $model::create($data);
                })
                ->after(function ($record, array $data) {
                    AbsenceDay::create([
                        'status' => 'pending',
                        'absence_id' => $record->id,
                        'date' => $record->from_date,
                        'part_of_day' => $data['part_of_day'],
                        'leave_type' => 'none',
                    ]);
                    AbsenceApproval::create([
                        'approval_id' => null,
                        'absence_id' => $record->id,
                        'level' => 1,
                        'status' => 'pending',
                        'note' => null,
                    ]);
                }),

            CreateAction::make('create_multi_day')
                ->label('Nghỉ nhiều ngày')
                ->schema(fn () => AbsenceRequestForm::configure(app(Schema::class), 'create_multi_day')->getComponents())
                ->mutateDataUsing(function (array $data): array {
                    $from = Carbon::parse($data['from_date']); // từ form
                    $to = Carbon::parse($data['to_date']);   // từ form

                    // tạo period
                    $period = CarbonPeriod::create($from, $to);

                    // lọc ra ngày không phải Chủ nhật
                    $workingDays = collect($period)
                        ->filter(fn ($date) => $date->dayOfWeek !== Carbon::SUNDAY);

                    // đếm tổng ngày (không bao gồm Chủ nhật)
                    $totalDays = $workingDays->count();

                    // nếu có ít nhất 1 ngày Thứ 7 thì +0.5
                    if ($workingDays->contains(fn ($date) => $date->dayOfWeek === Carbon::SATURDAY)) {
                        $totalDays += 0.5;
                    }

                    $data['user_id'] = Auth::id();
                    $data['created_by'] = Auth::id();
                    $data['status'] = 'pending';
                    $data['total_day'] = $totalDays;

                    return $data;

                })
                ->using(function (array $data, string $model): Model {
                    return $model::create($data);
                })
                ->after(function ($record, array $data) {
                    $from = Carbon::parse($record->from_date);
                    $to = Carbon::parse($record->to_date);

                    $period = CarbonPeriod::create($from, $to);

                    AbsenceDay::where('absence_id', $record->id)->forceDelete();

                    $user = $record->user; // giả sử model AbsenceRequest có quan hệ user()
                    $role = $user->role ?? null; // hoặc $user->position, tùy cột bạn đặt

                    $days = collect();

                    foreach ($period as $date) {
                        // bỏ qua Chủ nhật
                        if ($date->dayOfWeek === Carbon::SUNDAY) {
                            continue;
                        }

                        // 🧩 Logic phần buổi nghỉ
                        if ($date->dayOfWeek === Carbon::SATURDAY) {
                            if ($role === 'staff') {
                                $partOfDay = 'morning';
                            } elseif (in_array($role, ['driver', 'assistant'])) {
                                $partOfDay = 'day';
                            } else {
                                // role khác (nếu có) thì tuỳ bạn muốn xử lý thế nào
                                $partOfDay = 'day';
                            }
                        } else {
                            $partOfDay = 'day';
                        }

                        $day = AbsenceDay::create([
                            'status' => 'pending',
                            'absence_id' => $record->id,
                            'date' => $date->format('Y-m-d'),
                            'part_of_day' => $partOfDay,
                            'leave_type' => 'none',
                        ]);

                        $days->push($day);
                    }
                    $dayCount = $days->count();
                    $levels = $dayCount > 5 ? [1, 2, 3] : [1, 2];

                    foreach ($levels as $level) {
                        AbsenceApproval::create([
                            'approval_id' => null,
                            'absence_id' => $record->id,
                            'level' => $level,
                            'status' => 'pending',
                            'note' => null,
                        ]);
                    }
                }),
        ];
    }
}
