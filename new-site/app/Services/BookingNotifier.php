<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Queue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Уведомления о брони: email по шаблонам очереди (queues.notification_*),
 * SMS и WhatsApp — пока заглушки в лог (драйверы подключим отдельно,
 * см. docs/БИЗНЕС_ЛОГИКА.md §1: SMS через провайдера, WPP через textmebot).
 *
 * Плейсхолдеры шаблонов сохранены из старого Queue::parseNotification:
 * %TIME% %DATE% %DAY% %DATE_LONG% %OFFICE% %CARMAKE% %CARMODEL%
 * %PURPOSE% %PURPOSE_LONG% %CANCELID% %URL%
 */
class BookingNotifier
{
    private const WEEK_DAYS = [
        1 => 'pirmdien', 2 => 'otrdien', 3 => 'trešdien', 4 => 'ceturtdien',
        5 => 'piektdien', 6 => 'sestdien', 7 => 'svētdien',
    ];

    public function bookingCreated(Booking $booking, Queue $queue, ?string $time): void
    {
        $this->sendEmail(
            $booking,
            $queue,
            $time,
            $queue->notification_email,
            $queue->notification_subject ?: 'Jūsu pieraksts R1 riepu servisā'
        );

        $sms = $this->parse($queue->notification_schedule_sms, $booking, $queue, $time);
        if ($sms !== null && $booking->phone_number) {
            // TODO: реальный SMS-драйвер (бывш. SmsSender::sendSchedule)
            Log::info('SMS [booking created]', ['to' => $booking->phone_number, 'text' => $sms]);
        }

        // TODO: WhatsApp в филиал о сегодняшней записи (бывш. textmebot)
    }

    public function bookingCancelled(Booking $booking, Queue $queue, ?string $time): void
    {
        $this->sendEmail(
            $booking,
            $queue,
            $time,
            $queue->notification_cancel_email,
            'Tava rezervācija R1 riepu servisā ATCELTA'
        );

        $sms = $this->parse($queue->notification_schedule_cancel_sms, $booking, $queue, $time);
        if ($sms !== null && $booking->phone_number) {
            // TODO: реальный SMS-драйвер
            Log::info('SMS [booking cancelled]', ['to' => $booking->phone_number, 'text' => $sms]);
        }
    }

    private function sendEmail(Booking $booking, Queue $queue, ?string $time, ?string $template, string $subject): void
    {
        if (! $booking->email) {
            return;
        }

        $html = $this->parse($template, $booking, $queue, $time);
        if ($html === null) {
            return;
        }

        try {
            Mail::html($html, function ($message) use ($booking, $subject) {
                $message->to($booking->email)->subject($subject);
                if ($bcc = config('mail.booking_bcc')) {
                    $message->bcc($bcc);
                }
            });
        } catch (\Throwable $e) {
            Log::warning('Booking email failed', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
        }
    }

    private function parse(?string $template, Booking $booking, Queue $queue, ?string $time): ?string
    {
        if ($template === null || trim($template) === '') {
            return null;
        }

        $date = $booking->slot->date;
        $dayOfWeek = self::WEEK_DAYS[(int) $date->format('N')];
        [$purpose, $purposeLong] = $this->purposeForService($booking->service_id);

        return strtr($template, [
            '%TIME%' => (string) $time,
            '%DATE%' => $date->format('d.m.Y'),
            '%DAY%' => ucfirst($dayOfWeek),
            '%DATE_LONG%' => $dayOfWeek.', '.$date->format('d.m.Y'),
            '%OFFICE%' => $queue->office->title,
            '%CARMAKE%' => (string) $booking->car_brand,
            '%CARMODEL%' => (string) $booking->car_model,
            '%PURPOSE%' => $purpose,
            '%PURPOSE_LONG%' => $purposeLong,
            '%CANCELID%' => (string) $booking->cancel_code,
            '%URL%' => (string) config('app.schedule_url', config('app.url')),
        ]);
    }

    /** @return array{string, string} Тексты по услуге — как в старом Queue::parseNotification. */
    private function purposeForService(?int $serviceId): array
    {
        return match ($serviceId) {
            1 => ['riepu nomaiņa', 'Jūs vēlaties samainīt riepas vai riteņus, kuri Jums būs līdzi'],
            2 => ['riepu nomaiņa', 'Jūs vēlaties samainīt riepas vai riteņus, kuri glabājas pie mums'],
            3 => ['riepu nomaiņa', 'Jūs vēlaties samainīt riepas vai riteņus, kurus vēlaties pie mums nopirkt'],
            4 => ['riepas remonts', 'Jūs vēlaties saremontēt riepu'],
            6 => ['kondicioniera uzpilde', 'Jūs vēlaties uzpildīt kondicionieri'],
            8 => ['riepu nomaiņa', ''],
            9 => ['riepu nomaiņa', 'Jūs vēlaties samainīt riepas vai riteņus motociklam, kurus vēlaties pie mums nopirkt'],
            default => ['', ''],
        };
    }
}
