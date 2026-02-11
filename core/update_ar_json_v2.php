<?php
$arJsonPath = '/Users/omerheathrow/Downloads/codecanyon-KActWBoM-translab-transport-ticket-booking-system/Files/core/resources/lang/ar.json';
$arJson = json_decode(file_get_contents($arJsonPath), true);

$newKeys = [
    "App Booking" => "حجز من التطبيق",
    "Booking Time" => "وقت الحجز",
    "Ticket ID" => "رقم الحجز",
    "Booked By" => "طريقة الحجز",
    "Your B2C Commission Rate" => "نسبة عمولتك من التطبيق (B2C)",
    "(Custom rate for your company)" => "(نسبة خاصة بشركتك)",
    "(Platform standard rate)" => "(نسبة المنصة القياسية)",
    "Request Rate Review" => "طلب مراجعة النسبة",
    "Performance Summary" => "ملخص الأداء",
    "Total Gross Volume" => "إجمالي حجم المبيعات",
    "Estimated Net Revenue" => "صافي الإيرادات المتوقع",
    "Export to CSV" => "تصدير إلى CSV",
    "Journey Date" => "تاريخ الرحلة",
    "Passenger" => "الراكب",
    "Gross Amount" => "المبلغ الإجمالي",
    "Commission" => "العمولة",
    "Net Credit" => "صافي الرصيد",
    "No B2C sales found" => "لم يتم العثور على مبيعات للتطبيق",
    "Route Name" => "اسم المسار",
    "Total Revenue" => "إجمالي الإيرادات",
    "Trip Title" => "اسم الرحلة",
    "Brand Name" => "الماركة",
    "Model No." => "رقم الموديل",
    "Registration No." => "رقم اللوحة",
    "Nick Name" => "الاسم المستعار",
    "Engine Number" => "رقم المحرك",
    "Chassis Number" => "رقم الشاسيه",
    "Owner Name" => "اسم المالك",
    "Owner Phone Number" => "رقم هاتف المالك",
    "Edit Vehicle" => "تعديل بيانات الحافلة",
    "Add New Vehicle" => "إضافة حافلة جديدة",
    "Clear" => "مسح",
    "Cancel" => "إلغاء",
    "Apply" => "تطبيق",
    "Start Date - End Date" => "تاريخ البداية - تاريخ النهاية",
    "Select Date Range" => "اختر الفترة الزمنية",
    "Route & Trip Performance" => "أداء المسارات والرحلات",
    "Trip Performance" => "أداء الرحلات",
    "Route Performance" => "أداء المسارات",
    "Search..." => "بحث...",
    "Other" => "أخرى"
];

foreach ($newKeys as $key => $value) {
    if (!isset($arJson[$key])) {
        $arJson[$key] = $value;
    }
}

// Sort the array by keys to keep it organized
ksort($arJson);

file_get_contents($arJsonPath); // Ensure file exists
file_put_contents($arJsonPath, json_encode($arJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "ar.json updated and sorted successfully.\n";
