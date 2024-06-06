<?php

namespace Emincmg\ConvoLite\Traits\Message;

use Emincmg\ConvoLite\Models\Attachment;
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
        foreach ($files as $file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('message-files', $filename, 'public');
            $attachment = new Attachment();
            $attachment->name = $filename;
            $attachment->full_path = env('APP_URL') . '/storage/message-files/' . $filePath;
            $attachment->storage_path = Storage::url($filePath);
            $attachment->public_path = asset($attachment->storage_path);
            $attachment->conversation_id = $this->conversation_id;
            $attachment->user_id = $this->user_id;
            $this->attachments()->save($attachment);
        }
    }
}
