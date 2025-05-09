<?php
function validate_title($title) {
    $word_count = str_word_count($title);
    return $word_count >= 3 && $word_count <= 15;
}

function validate_content($content) {
    $word_count = str_word_count(strip_tags($content));
    return $word_count >= 20 && $word_count <= 200;
}

function validate_image($image) {
    $allowed_types = ['image/jpeg', 'image/png'];
    $max_size = 2 * 1024 * 1024; // 2MB
    return in_array($image['type'], $allowed_types) && $image['size'] <= $max_size;
}
?>
