<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProfileDisliked extends Notification {
    use Queueable;

    protected $disliker;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($disliker) {
        $this->disliker = $disliker;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        //! Fetch the first image of the disliker
        $profilePicture = $this->disliker->photoGalleries()->where('status', 'active')->first()->image ?? null;

        return [
            'message'               => 'Someone disliked your profile',
            'disliker_id'           => $this->disliker->id,
            'disliker_name'         => $this->disliker->name,
            'liker_profile_picture' => $profilePicture,
        ];
    }
}
