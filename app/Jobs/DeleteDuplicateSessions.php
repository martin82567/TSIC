<?php

namespace App\Jobs;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteDuplicateSessions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Get all duplicate sessions
            $sessions = DB::table('session')
                ->select('id', 'name', 'schedule_date', 'time_duration', 'type', 'session_method_location_id', 'mentee_id', 'mentor_id', 'no_show', DB::raw('COUNT(*) as duplicate_count'))
                ->where('created_date', '>=', '2024-08-01')
                ->groupBy('name', 'schedule_date', 'time_duration', 'type', 'session_method_location_id', 'mentee_id', 'mentor_id', 'no_show')
                ->having('duplicate_count', '>', 1)
                ->get();

            $idsToDelete = [];
            foreach ($sessions as $session) {
                // Get all duplicate session IDs except one
                $duplicateSessions = DB::table('session')
                    ->where('name', $session->name)
                    ->where('schedule_date', $session->schedule_date)
                    ->where('time_duration', $session->time_duration)
                    ->where('type', $session->type)
                    ->where('session_method_location_id', $session->session_method_location_id)
                    ->where('mentee_id', $session->mentee_id)
                    ->where('mentor_id', $session->mentor_id)
                    ->where('no_show', $session->no_show)
                    ->where('created_date', '>=', '2024-08-01')
                    ->pluck('id')
                    ->toArray();

                // Keep the first ID (original) and remove the rest
                $sessionsToDelete = array_slice($duplicateSessions, 1);
                $idsToDelete = array_merge($idsToDelete, $sessionsToDelete);
            }

// Delete all duplicate session IDs collected
            DB::table('session')->whereIn('id', $idsToDelete)->delete();

        } catch (GuzzleException $e) {
            Log::error('Error deleting duplicate sessions: ' . $e->getMessage());
        }
    }
}
