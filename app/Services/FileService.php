<?php

namespace App\Services;

class FileService
{

    public function save_file(String $file_key_name, String $path): string|null
    {
        if(request()->hasFile($file_key_name) && request()->file($file_key_name)->isValid()){
            $image = request()[$file_key_name]->hashName();
            request()[$file_key_name]->storeAs($path,$image);
            return $image;
        }

        return null;
    }

    public function delete_file(string $path): void
    {
        if(file_exists(storage_path($path))){
            unlink(storage_path($path));
        }
    }

}