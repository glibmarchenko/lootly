<?php

namespace App\Services\Amazon;


use Illuminate\Support\Facades\Storage;

class UploadFile
{
    protected $s3;

    public function __construct()
    {
        $this->s3 = Storage::disk('s3');
    }

    public function upload($merchantObj, $file, $action_id)
    {
        try{
            $image = file_get_contents($file);
        }
        catch(ErrorException $e){
            return null;
        }

        $imageFileName = $this->generateImageName($merchantObj, $action_id);

        $url = 'https://s3.us-east-1.amazonaws.com/lootly-custom-icons/' . $imageFileName;
        $storagePath = $this->s3->put($imageFileName, $image, 'public');

        return $url;
    }

    public function generateImageName($merchantObj, $action_id)
    {
        if ($action_id) {
            return $merchantObj->id . '_' . $action_id . '_' . time() .'_'. rand().'.jpg';
        } else {
            return $merchantObj->id . '_' . time() .'_'. rand().'.jpg';
        }
    }

    public function delete($fileName)
    {
        if($this->s3->exists($fileName)) {
         return $this->s3->delete($fileName);
        }

    }

}
