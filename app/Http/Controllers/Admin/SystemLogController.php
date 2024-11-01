<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SystemLogResource;
use App\Models\SystemLog;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SystemLogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:settings'));
    }

    /**
     * Get paginated system logs
     */
    public function paginate(): AnonymousResourceCollection
    {
        $records = paginate(SystemLog::latest()->oldest('seen_at'));

        return SystemLogResource::collection($records);
    }

    /**
     * Mark logs as seen
     */
    public function markAsSeen(SystemLog $log)
    {
        $log->markAsSeen();
    }

    /**
     * Mark all logs as seen
     *
     * @return void
     */
    public function markAllAsSeen()
    {
        SystemLog::unseen()->update(['seen_at' => now()]);
    }
}
