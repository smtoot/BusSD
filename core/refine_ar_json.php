<?php
$arJsonPath = '/Users/omerheathrow/Downloads/codecanyon-KActWBoM-translab-transport-ticket-booking-system/Files/core/resources/lang/ar.json';
$arJson = json_decode(file_get_contents($arJsonPath), true);

$newKeys = [
    "Registration Number" => "رقم اللوحة",
    "Model Number" => "رقم الموديل",
    "Are you sure to enable this vehicle?" => "هل أنت متأكد من تفعيل هذه الحافلة؟",
    "Are you sure to disable this vehicle?" => "هل أنت متأكد من تعطيل هذه الحافلة؟",
    "No search result found." => "لم يتم العثور على نتائج بحث.",
    "No owner data yet." => "لا توجد بيانات لملاك الحافلات بعد.",
];

foreach ($newKeys as $key => $value) {
    if (!isset($arJson[$key])) {
        $arJson[$key] = $value;
    }
}

ksort($arJson);

file_put_contents($arJsonPath, json_encode($arJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "ar.json refined successfully.\n";
