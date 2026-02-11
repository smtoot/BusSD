<?php
$json_path = '/Users/omerheathrow/Downloads/codecanyon-KActWBoM-translab-transport-ticket-booking-system/Files/core/resources/lang/ar.json';
$json_data = json_decode(file_get_contents($json_path), true);
ksort($json_data);
file_put_contents($json_path, json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Deduplicated and prettified ar.json\n";
