<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;

    public $icon;

    public $actionText;

    public $actionUrl;

    public $openInNewTab;

    public $component;

    public function __construct(
        $message = null,
        $icon = 'info',
        $actionText = 'View',
        $actionUrl = null,
        $openInNewTab = false,
        $component = 'message-notification'
    ) {
        $this->message = $message;
        $this->icon = $icon;
        $this->actionText = $actionText;
        $this->actionUrl = $actionUrl;
        $this->openInNewTab = $openInNewTab;
        $this->component = $component;
    }

    public function message($message): self
    {
        $this->message = $message;

        return $this;
    }

    public function action($actionText, $actionUrl = null, $openInNewTab = false): self
    {
        $this->actionText = $actionText;
        $this->actionUrl = $actionUrl;
        $this->openInNewTab = $openInNewTab;

        return $this;
    }

    public function icon(string $icon): self
    {
        // Icones aceitos success, warning, danger e info
        $this->icon = $icon;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'component' => $this->component,
            'icon' => $this->icon,
            'message' => $this->message,
            'actionText' => $this->actionText,
            'actionUrl' => $this->actionUrl,
            'openInNewTab' => $this->openInNewTab,
        ];
    }

    public function via($notifiable): array
    {
        return ['database'];
    }
}
