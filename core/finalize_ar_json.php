<?php
$arJsonPath = '/Users/omerheathrow/Downloads/codecanyon-KActWBoM-translab-transport-ticket-booking-system/Files/core/resources/lang/ar.json';
$arJson = json_decode(file_get_contents($arJsonPath), true);

$finalKeys = [
    "Date Of Journey" => "تاريخ الرحلة",
    "Ticket ID" => "رقم الحجز",
    "Booked By" => "طريقة الحجز",
    "Booking Time" => "وقت الحجز",
    "Gross Amount" => "المبلغ الإجمالي",
    "Commission" => "العمولة",
    "Net Credit" => "صافي الرصيد",
    "App Booking" => "حجز من التطبيق",
    "Your B2C Commission Rate" => "نسبة عمولتك من التطبيق (B2C)",
    "(Custom rate for your company)" => "(نسبة خاصة بشركتك)",
    "(Platform standard rate)" => "(نسبة المنصة القياسية)",
    "Request Rate Review" => "طلب مراجعة النسبة",
    "Performance Summary" => "ملخص الأداء",
    "Total Gross Volume" => "إجمالي حجم المبيعات",
    "Estimated Net Revenue" => "صافي الإيرادات المتوقع",
    "Export to CSV" => "تصدير إلى CSV",
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
    "Search..." => "بحث...",
    "Route Performance" => "أداء المسارات",
    "Trip Performance" => "أداء الرحلات",
    "Route & Trip Performance" => "أداء المسارات والرحلات",
    "B2C (App)" => "حجز التطبيق (B2C)",
    "Counter" => "حجز المكتب (الفواتير)",
    "No search result found." => "لم يتم العثور على نتائج بحث.",
    "Are you sure to enable this vehicle?" => "هل أنت متأكد من تفعيل هذه الحافلة؟",
    "Are you sure to disable this vehicle?" => "هل أنت متأكد من تعطيل هذه الحافلة؟"
];

foreach ($finalKeys as $key => $value) {
    $arJson[$key] = $value;
}

ksort($arJson);

file_put_contents($arJsonPath, json_encode($arJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "ar.json finalized successfully.\n";
