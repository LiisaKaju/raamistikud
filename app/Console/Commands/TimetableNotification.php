<?php

namespace App\Console\Commands;

use App\Mail\Timetable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;
use Carbon\Carbon;

class TimetableNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:timetable-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Homework test mode: use current week instead of next week.
        $startDate = now()->startOfWeek();
        $endDate = now()->endOfWeek();

        $response = Http::get('https://tahveltp.edu.ee/hois_back/timetableevents/timetableSearch', [
            'from' => $startDate->toIsoString(),
            'thru' => $endDate->toIsoString(),
            'lang' => 'ET',
            'page' => 0,
            'schoolId' => 38,
            'size' => 50,
            'studentGroups' => '39248890-7051-4182-9a9a-8a82259b862b',
        ]);

        if ($response->failed()) {
            $this->error('Request failed: '.$response->status());

            return self::FAILURE;
        }

        Carbon::setLocale('et');

        $data = $response->json();
        $events = collect(data_get($data, 'content', []))
            ->filter(fn ($event) => filled(data_get($event, 'date')))
            ->sortBy(fn ($event) => Carbon::parse(data_get($event, 'date'))->format('Y-m-d').' '.data_get($event, 'timeStart', '00:00'));

        $groupedEvents = $events->groupBy(function ($event) {
            return Carbon::parse(data_get($event, 'date'))->translatedFormat('l');
        });

        if ($groupedEvents->isEmpty()) {
            $this->warn('Selle nädala jaoks tunnid puuduvad.');
            $this->info('Events fetched: 0');

            return self::SUCCESS;
        }

        foreach ($groupedEvents as $dayName => $dayEvents) {
            $dayDate = Carbon::parse(data_get($dayEvents->first(), 'date'))->translatedFormat('d F Y');

            $this->newLine();
            $this->info($dayName.' ('.$dayDate.')');

            foreach ($dayEvents as $lesson) {
                $this->line(sprintf(
                    '- %s-%s | %s | ruum: %s',
                    data_get($lesson, 'timeStart', '--:--'),
                    data_get($lesson, 'timeEnd', '--:--'),
                    data_get($lesson, 'nameEt', 'Nimetu tund'),
                    data_get($lesson, 'rooms.0.roomCode', '-')
                ));
            }
        }

        $this->newLine();
        $this->info('Events fetched: '.$events->count());

        $recipient = env('MAIL_TO_ADDRESS', config('mail.from.address'));

        if (! $recipient) {
            $this->error('Email recipient is missing. Set MAIL_TO_ADDRESS in .env.');

            return self::FAILURE;
        }

        Mail::to($recipient)->send(new Timetable($groupedEvents, $startDate, $endDate));
        $this->info('Timetable email sent to: '.$recipient);

        return self::SUCCESS;
    }
}

