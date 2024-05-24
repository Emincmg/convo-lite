<?php

namespace Emincmg\ConvoLite\Traits\Message;

use Illuminate\Support\Facades\Storage;

trait AttachesFiles
{
    /**
     * Attach files to the application.
     *
     * @param array $files An array of files to attach.
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
