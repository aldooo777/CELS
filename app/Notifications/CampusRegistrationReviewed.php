<?php

namespace App\Notifications;

use App\Models\CampusRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Email ke admin kampus setelah Admin CELS menyetujui / menolak pendaftaran kampus */
class CampusRegistrationReviewed extends Notification
{
    use Queueable;

    public function __construct(public CampusRegistration $registration) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $reg  = $this->registration->loadMissing('campus');
        $mail = (new MailMessage)->greeting('Halo, '.$reg->pic_name.'!');

        if ($reg->status === 'disetujui') {
            return $mail
                ->subject('[CELS] Pendaftaran kampus disetujui')
                ->line('Pendaftaran '.$reg->campus->name.' di CELS telah **disetujui**.')
                ->line('Kampus Anda kini berada dalam masa uji coba hingga '.$reg->campus->trial_ends_at?->translatedFormat('d F Y').'.')
                ->action('Masuk ke Dashboard', route('login'))
                ->line('Tim CELS akan menghubungi Anda terkait paket langganan.');
        }

        return $mail
            ->subject('[CELS] Pendaftaran kampus ditolak')
            ->line('Mohon maaf, pendaftaran '.$reg->campus->name.' di CELS **ditolak**.')
            ->line('Alasan: '.($reg->admin_note ?: '-'))
            ->line('Silakan hubungi tim CELS bila ada pertanyaan.');
    }
}
