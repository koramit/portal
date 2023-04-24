<?php

namespace App\Notifications\Messages;

class LINENotifyMessage
{
    private array $messages;

    public function text(string $text): static
    {
        $this->messages['message'] = $text;

        return $this;
    }

    public function sticker(int $packageId, int $stickerId): static
    {
        $this->messages['stickerPackageId'] = $packageId;
        $this->messages['stickerId'] = $stickerId;

        return $this;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
