<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class UserNotification extends Notification
{
    private $title;

    private $description;

    private $image;

    private $param = [];

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($title = '', $description = '', $image = '', $param = [])
    {
        $param['click_action'] = 'FLUTTER_NOTIFICATION_CLICK';
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->param = $param;
    }

    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(notification: new FcmNotification(
            title: $this->title,
            body: $this->description,
            image: $this->image
        )))
            ->data($this->param)
            ->custom([
            'android' => [
                'notification' => [
                    'color' => '#0A0A0A',
                ],
                'fcm_options' => [
                    'analytics_label' => 'analytics',
                ],
            ],
            'apns' => [
                'fcm_options' => [
                    'analytics_label' => 'analytics',
                ],
            ],
        ]);
    }
}
