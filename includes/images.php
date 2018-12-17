<?php
define("IMAGE_DIRECTORY", ROOT_DIR . "resources/images/");
define("IMAGE_THUMBNAIL_DIRECTORY", ROOT_DIR . "resources/images/thumbnail/");
define("IMAGE_WATERMARK_DIRECTORY", ROOT_DIR . "resources/images/watermark/");
define("WATERMARK_PATH", IMAGE_DIRECTORY . 'watermark.png');
define("WATERMARK_URL", BASE_URL . "/images/original/watermark.png");


function createWaterMarked($original_file_path, $destination)
{
    $watermark_file = WATERMARK_PATH;
    $watermark = imagecreatefrompng($watermark_file);
    list($src_width, $src_height) = getimagesize($watermark_file);
    list($dis_width, $dis_height) = getimagesize($original_file_path);

    $image_jpeg = imagecreatefromjpeg($original_file_path);

    imagecopy($image_jpeg, $watermark, ($dis_width / 2) - ($src_width / 2), ($dis_height) - ($src_height), 0, 0, $src_width, $src_height);
    if (!file_exists(dirname($destination)))
        mkdir(dirname($destination), 0777, true);
    imagejpeg($image_jpeg, $destination);
}

function createThumbnail($original_path, $des_path)
{
    list($width, $height) = getimagesize($original_path);

    $d_width = 300;
    $d_height = round($height / ($width / $d_width));

    $dst_image = imagecreatetruecolor($d_width, $d_height);
    $src_image = imagecreatefromjpeg($original_path);
    imagecopyresized($dst_image, $src_image, 0, 0, 0, 0, $d_width, $d_height, $width, $height);
    if (!file_exists(dirname($des_path)))
        mkdir(dirname($des_path), 0777, true);
    imagejpeg($dst_image, $des_path);
}

function get_path($file_name)
{
    return IMAGE_DIRECTORY . $file_name;
}

function get_original_url($image_id)
{
    return BASE_URL . "/images/original/{$image_id}.jpg";
}

function get_thumbnail_path($file_name)
{
    return IMAGE_DIRECTORY . "thumbnail/" . $file_name;
}

function get_thumbnail_url($image_id)
{
    return BASE_URL . "/thumbnail/{$image_id}.jpg";
}

function get_watermark_path($file_name)
{
    return IMAGE_DIRECTORY . "watermark/" . $file_name;
}

function get_watermark_url($image_id)
{
    return BASE_URL . "/{$image_id}.jpg";
}

function clear_cash($image_id)
{
    $file_name = $image_id . ".jpg";
    if (file_exists(get_watermark_path($file_name)))
        unlink(get_watermark_path($file_name));
    if (file_exists(get_thumbnail_path($file_name)))
        unlink(get_thumbnail_path($file_name));
}

function delete_image($image_id)
{
    unlink(get_path($image_id . '.jpg'));
    clear_cash($image_id);
}