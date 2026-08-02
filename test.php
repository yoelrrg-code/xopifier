<?php
//https://drive.usercontent.google.com/download?id=1EsXhAw9aYIImJAaZE-t5WTo31FlOMvHK,https://drive.usercontent.google.com/download?id=111NtMrWWivhFD6wfFz0Z5c6bkhYlpKuA
$image_url = 'https://drive.usercontent.google.com/download?id=111NtMrWWivhFD6wfFz0Z5c6bkhYlpKuA';
$image_content = file_get_contents($image_url);

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_buffer($finfo, $image_content);
finfo_close($finfo);

switch($mime_type) {
    case 'image/jpeg':
        $extension = 'jpg';
        break;
    case 'image/png':
        $extension = 'png';
        break;
    case 'image/gif':
        $extension = 'gif';
        break;
    // otros tipos si es necesario
    default:
        $extension = 'unknown';
}

echo "Tipo archivo: " . $extension;

// Get the filename and extension
// $path_info = pathinfo($image_url);
// $file_name = $path_info['basename']; // e.g., image.jpg
// $file_extension = $path_info['extension']; // e.g., jpg
// Create a temporary file

$temp_file = tempnam(sys_get_temp_dir(), 'img_');

// var_dump($image_content);
?>