<?php
function get_data() {
    $file = __DIR__ . '/data.json';
    if (!file_exists($file)) {
        // Initialize with empty arrays
        $default = [
            'users' => [],
            'courses' => [],
            'enrollments' => []
        ];
        file_put_contents($file, json_encode($default, JSON_PRETTY_PRINT));
        return $default;
    }
    $json = file_get_contents($file);
    return json_decode($json, true);
}

function save_data($data) {
    $file = __DIR__ . '/data.json';
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}
?>