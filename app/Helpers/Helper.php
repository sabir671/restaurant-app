<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

function getFullName($user)
{
    return ucwords($user->first_name . ' ' . $user->last_name);
}

function getImage($image, $isAvatar = false, $withBaseurl = false)
{
    $errorImage = $isAvatar ? url('/backend/no_avatar.png') : url('/backend/no_image.png');
    return !empty($image) ? ($withBaseurl ? url('/storage/' . $image) : Storage::url($image)) : $errorImage;
}

function saveResizeImage($file, $directory, $width = null, $height = null, $type = 'Jpeg')
{
    if (!Storage::exists($directory)) {
        Storage::makeDirectory($directory);
    }
    $is_preview = strpos($directory, 'previews') !== false;
    $filename = Str::random() . time() . '.' . $type;
    $path = "$directory/$filename";

    $imageManager = new ImageManager(new Driver());

    $img = $imageManager->read($file);
    if ($width) {
        $img->resizeDown(width: $width);
    }
    if ($height) {
        $img->resizeDown(height: $height);
    }
    if ($width && $width == $is_preview) {
        $img = $img->blur(60);
    }
    $resource = $img->{'to' . ucfirst($type)}($is_preview ? 40 : 85);
    Storage::disk('public')->put($path, $resource, 'public');

    return $path;
}


function saveDocument($file, $directory, $fileName = null)
{
    if (!Storage::exists($directory)) {
        Storage::makeDirectory("$directory");
    }
    $filename = $fileName ? $fileName : Str::random() . time() . '.' . $file->getClientOriginalExtension();
    Storage::disk('public')->putFileAs($directory, $file, $filename);
    $path = $directory . '/' . $filename;
    return $path;
}

/**
 * @param $file
 * get files
 */
function getFiles($file_name)
{
    $file = empty($file_name) ? '' : url('/storage/' . $file_name);
    return empty($file) ? '' : $file;
}

function saveAnyFile($file, $directory, $fileName) {
    $file_type = $file->getMimeType();
    if(str_starts_with($file_type, 'image/')){
        $path = saveResizeImage($file, $directory);
    } else {
        $path = saveDocument($file, $directory, $fileName);
    }
    return $path;
}

function statusClasses($status)
{
    $class = '';
    switch ($status) {
        case 'active':
        case 'approved':
        case 'accepted':
        case 'completed':
            $class = 'success';
            break;
        case 'inactive':
        case 'rejected':
        case 'cancelled':
            $class = 'danger';
            break;
        case 'pending':
            $class = 'warning';
            break;
    }
    return $class;
}

function deleteFile($path)
{
    if (!empty($path) && file_exists('app/' . $path)) {
        unlink(storage_path('app/' . $path));
    }

    $storage_path = 'storage/' . $path;
    $public_path = public_path($storage_path);
    if (!empty($path) && file_exists($public_path)) {
        unlink($public_path);
    }
}

function addEllipsis($text, $max = 30)
{
    return strlen($text) > 30 ? mb_substr($text, 0, $max, "UTF-8") . "..." : $text;
}

function isValue($value)
{
    if ($value !== 'undefined' && $value !== null && !empty($value)) {
        return $value;
    } else {
        return 'N/A';
    }
}

function formatString($key, $reverse = false) {
    if ($reverse) {
        return str_replace([' ', "'"],'_', strtolower($key));
    } else {
        return str_replace(['_','-'],' ', strtolower($key));
    }
}

function getAssignedPermissionsCount($role, $group)
{
    return $role->permissions()->where('group', $group)->count();
}
