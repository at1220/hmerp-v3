<?php

if (! function_exists('safeEnumLabel')) {
    function safeEnumLabel(string $class, ?string $value): string
    {
        return $value ? $class::from($value)->getLabel() : '';
    }
}
if (! function_exists('getTimeData')) {
    function getTimeData()
    {
        return [
            '04:00' => '04:00',
            '04:30' => '04:30',
            '05:00' => '05:00',
            '05:30' => '05:30',
            '06:00' => '06:00',
            '06:30' => '06:30',
            '07:00' => '07:00',
            '07:30' => '07:30',
            '08:00' => '08:00',
            '08:30' => '08:30',
            '09:00' => '09:00',
            '09:30' => '09:30',
            '10:00' => '10:00',
            '10:30' => '10:30',
            '11:00' => '11:00',
            '11:30' => '11:30',
            '12:00' => '12:00',
            '12:30' => '12:30',
            '13:00' => '13:00',
            '13:30' => '13:30',
            '14:00' => '14:00',
            '14:30' => '14:30',
            '15:00' => '15:00',
            '15:30' => '15:30',
            '16:00' => '16:00',
            '16:30' => '16:30',
            '17:00' => '17:00',
            '17:30' => '17:30',
            '18:00' => '18:00',
            '18:30' => '18:30',
            '19:00' => '19:00',
            '19:30' => '19:30',
            '20:00' => '20:00',
            '20:30' => '20:30',
            '21:00' => '21:00',
            '21:30' => '21:30',
            '22:00' => '22:00',
            '22:30' => '22:30',
            '23:00' => '23:00',
            '23:30' => '23:30',
        ];
    }
}
function stripVietnamese($str)
{
    $unicode = [
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd' => 'đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
    ];

    foreach ($unicode as $nonAccent => $accents) {
        $str = preg_replace("/($accents)/i", $nonAccent, $str);
    }

    return $str;
}
if (! function_exists('highlightSearch')) {
    function highlightSearch(string $text, ?string $search, string $color = '#22c55e'): string
    {
        if (! $search) {
            return e($text);
        }

        $plainText = stripVietnamese(mb_strtolower($text));
        $plainSearch = stripVietnamese(mb_strtolower($search));

        $pos = mb_stripos($plainText, $plainSearch);

        if ($pos === false) {
            return e($text);
        }

        // Cắt chuỗi gốc theo vị trí match
        $before = mb_substr($text, 0, $pos);
        $match = mb_substr($text, $pos, mb_strlen($search));
        $after = mb_substr($text, $pos + mb_strlen($search));

        return e($before).
            '<span style="color:'.$color.';">'.e($match).'</span>'.
            e($after);
    }
}
function updateOrderForm(callable $set, callable $get): void
{
    // Tính giá cước có VAT
    $price = (float) $get('price'); // Giá cước
    $vat_rate_price = (float) $get('vat_rate_price');
    // tính giá bốc xếp
    $truckload_price = (float) $get('truckload_price'); // Giá cước
    $vat_rate_truckload = (float) $get('vat_rate_truckload');
    // tính goá quay đầu
    $price_back = (float) $get('price_back'); // Giá cước
    $vat_rate_price_back = (float) $get('vat_rate_price_back');
    // % VAT cho giá cước
    $all_price = $price * (1 + ($vat_rate_price / 100));
    $all_truckload = $truckload_price * (1 + ($vat_rate_truckload / 100));
    $all_priceback = $price_back * (1 + ($vat_rate_price_back / 100));
    // Tính tổng cho các dịch vụ đi kèm
    $total_extra_services = 0;
    $all_service_price = 0;
    $services = $get('services') ?? $get('../../services') ?? [];

    foreach ($services as $service) {
        $service_price = (float) $service['price']; // Giá dịch vụ
        $vat_percentage_service = (float) $service['vat_rate']; // % VAT của dịch vụ
        $total_extra_services += $service_price * (1 + ($vat_percentage_service / 100));
        $all_service_price += $service_price;
    }

    // Tổng cước
    $total_price = $price + $truckload_price + $price_back + $all_service_price;
    // Tổng tiền cần thanh toán
    $total_paid = $all_price + $all_truckload + $all_priceback + $total_extra_services;
    $set('total_paid', ($total_paid));
    $set('total_price', ($total_price));
    // 2 trường

    $set('total_amount_service', ($all_service_price));
    $set('vat_amount_service', ($total_extra_services - $all_service_price));

}
