<?php

namespace Emincmg\ConvoLite\Traits;

use Illuminate\Support\Facades\Storage;

trait AttachesFiles
{
    /**
     * Attaches files to a message. Accepts an array of UploadedFile model.
     * @param array $files
     * @return void
     */
    public function attachFiles(array $files): void
    {
        $fileData = [];

        foreach ($files as $file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('message-files', $filename, 'public');

            $fileInfo = [
                'filename' => $filename,
                'full_path' => Storage::url($filePath)
            ];

            $fileData[] = $fileInfo;
        }
        $this->files= $fileData;
    }
}
