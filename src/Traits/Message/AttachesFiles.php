<?php

namespace Emincmg\ConvoLite\Traits\Message;

use Emincmg\ConvoLite\Models\Attachment;
use Emincmg\ConvoLite\Models\Message;
use Illuminate\Support\Facades\Storage;

trait AttachesFiles
{
    /**
     * Attach files to the application.
     *
     * @param array $files An array of files to attach.
     * @param Message|int $message The message instancet the attachment will be attached or its id.
     * @return void
     */
    public function attachFiles(array $files, Message|int $message): void
    {
        if (is_int($message)) {
            $message = Message::findOrFail($message);
        }

        foreach ($files as $file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('message-files', $filename, 'public');
            $attachment = new Attachment();
            $attachment->name = $filename;
            $attachment->full_path = env('APP_URL') . '/storage/message-files/' . $filePath;
            $attachment->storage_path = Storage::url($filePath);
            $attachment->public_path = asset($attachment->storage_path);
            $attachment->conversation_id = $message->conversation_id;
            $attachment->user_id = $message->user_id;
            $message->attachments()->save($attachment);
        }
    }
}
