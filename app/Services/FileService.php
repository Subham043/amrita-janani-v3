<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FileService
{

    public function save_file(string $key, string $path): string|null
    {
        if(request()->hasFile($key)){
            $uuid = str()->uuid();
            $file = $uuid.'-'.request()[$key]->hashName();

            request()[$key]->storeAs($path,$file, 'private');
            return $file;
        }
        return null;
    }

    public function save_private_image(string $key, string $path): string|null
    {
        if(request()->hasFile($key)){
            $uuid = str()->uuid();
            $file = $uuid.'-'.request()[$key]->hashName();

            // create image manager with desired driver
            $manager = new ImageManager(new Driver());

            // read image from file system
            $image = $manager->read(request()->file($key)->getRealPath());

            // resize image proportionally to 300px width
            $image->scale(width: 100);

            $image->save(storage_path('app/private/'.$path).'/'.'compressed-'.$file);

            request()[$key]->storeAs($path,$file, 'private');
            return $file;
        }
        return null;
    }
    
    public function save_public_image(string $key, string $path): string|null
    {
        if(request()->hasFile($key)){
            $uuid = str()->uuid();
            $file = $uuid.'-'.request()[$key]->hashName();

            // create image manager with desired driver
            $manager = new ImageManager(new Driver());

            // read image from file system
            $image = $manager->read(request()->file($key)->getRealPath());

            // resize image proportionally to 300px width
            $image->scale(width: 100);
            $image->save(storage_path('app/public/'.$path).'/'.'compressed-'.$file);

            request()[$key]->storeAs($path,$file, 'public');
            return $file;
        }
        return null;
    }

    public function mp3_file_duration(string $name): string|null
    {
        try {
            //code...
            $mp3file = new Mp3File(storage_path('app/public/upload/audios/'.$name));
            $duration2 = $mp3file->getDuration();//(slower) for VBR (or CBR)
            return Mp3File::formatTime($duration2);
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public function document_page_number(string $name): string|null
    {
        $pdftext = file_get_contents(storage_path('app/private/upload/documents/'.$name));

        return preg_match_all("/\/Page\W/", $pdftext,$dummy);
    }

    public function remove_file(string $file_name, string $path): void
    {
        if($file_name!=null && file_exists(storage_path($path).$file_name)){
            unlink(storage_path($path.$file_name));
        }
    }

}