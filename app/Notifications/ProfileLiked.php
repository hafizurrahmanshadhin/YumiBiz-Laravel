<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProfileLiked extends Notification {
    use Queueable;

    protected $liker;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($liker) {
        $this->liker = $liker;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(object $notifiable): array {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray(object $notifiable): array {
        //! Fetch the first image of the liker
        $profilePicture = $this->liker->photoGalleries()->where('status', 'active')->first()->image ?? null;
        return [
            'message'               => 'Someone liked your profile',
            'liker_id'              => $this->liker->id,
            'liker_name'            => $this->liker->name,
            'liker_profile_picture' => $profilePicture,
        ];
    }
}
