<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Email ke mahasiswa setiap status pendaftaran berubah */
class ApplicationStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public Application $application) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $app = $this->application->loadMissing('program.campus');

        $mail = (new MailMessage)
            ->subject('[CELS] Status pendaftaran: '.$app->statusLabel())
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Status pendaftaran Anda pada program **'.$app->program->title.'** ('.$app->program->campus->name.') kini: **'.$app->statusLabel().'**.');

        if ($app->admin_note) {
            $mail->line('Catatan dari admin kampus: '.$app->admin_note);
        }

        return $mail
            ->action('Lihat Pendaftaran', route('mahasiswa.applications.show', $app))
            ->line('Terima kasih telah menggunakan CELS.');
    }
}
