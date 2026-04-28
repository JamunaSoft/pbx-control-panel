<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cdr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CdrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cdr::query();

        // Date range filtering
        if ($request->has('start_date')) {
            $query->where('start', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('start', '<=', $request->end_date . ' 23:59:59');
        }

        // Source/Destination filtering
        if ($request->has('src')) {
            $query->where('src', 'like', "%{$request->src}%");
        }

        if ($request->has('dst')) {
            $query->where('dst', 'like', "%{$request->dst}%");
        }

        // Disposition filtering
        if ($request->has('disposition')) {
            $query->where('disposition', $request->disposition);
        }

        // Duration filtering
        if ($request->has('min_duration')) {
            $query->where('duration', '>=', $request->min_duration);
        }

        if ($request->has('max_duration')) {
            $query->where('duration', '<=', $request->max_duration);
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'start');
        $sortDir = $request->get('sort_dir', 'desc');

        if (in_array($sortBy, ['start', 'duration', 'billsec', 'disposition'])) {
            $query->orderBy($sortBy, $sortDir);
        }

        $cdrs = $query->paginate(50);

        return response()->json($cdrs);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cdr $cdr)
    {
        return response()->json($cdr->load(['callerExtension', 'calleeExtension']));
    }

    /**
     * Get CDR statistics
     */
    public function stats(Request $request)
    {
        $query = Cdr::query();

        // Apply date filters
        if ($request->has('start_date')) {
            $query->where('start', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('start', '<=', $request->end_date . ' 23:59:59');
        }

        $stats = [
            'total_calls' => (clone $query)->count(),
            'answered_calls' => (clone $query)->where('disposition', 'ANSWERED')->count(),
            'missed_calls' => (clone $query)->whereIn('disposition', ['NO ANSWER', 'BUSY'])->count(),
            'failed_calls' => (clone $query)->whereNotIn('disposition', ['ANSWERED', 'NO ANSWER', 'BUSY'])->count(),
            'total_duration' => (clone $query)->sum('duration'),
            'average_duration' => (clone $query)->avg('duration'),
            'total_billsec' => (clone $query)->sum('billsec'),
            'average_billsec' => (clone $query)->avg('billsec'),
        ];

        // Hourly breakdown
        $hourlyStats = (clone $query)
            ->selectRaw('HOUR(start) as hour, COUNT(*) as calls, AVG(duration) as avg_duration')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $stats['hourly_breakdown'] = $hourlyStats;

        // Top calling numbers
        $topCallers = (clone $query)
            ->select('src', DB::raw('COUNT(*) as call_count'))
            ->whereNotNull('src')
            ->where('src', '!=', '')
            ->groupBy('src')
            ->orderBy('call_count', 'desc')
            ->limit(10)
            ->get();

        $stats['top_callers'] = $topCallers;

        // Top called numbers
        $topCalled = (clone $query)
            ->select('dst', DB::raw('COUNT(*) as call_count'))
            ->whereNotNull('dst')
            ->where('dst', '!=', '')
            ->groupBy('dst')
            ->orderBy('call_count', 'desc')
            ->limit(10)
            ->get();

        $stats['top_called'] = $topCalled;

        return response()->json($stats);
    }

    /**
     * Export CDR data
     */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:csv,json',
        ]);

        $query = Cdr::whereBetween('start', [
            $validated['start_date'],
            $validated['end_date'] . ' 23:59:59'
        ]);

        $cdrs = $query->orderBy('start')->get();

        if ($validated['format'] === 'csv') {
            $filename = 'cdr_' . $validated['start_date'] . '_to_' . $validated['end_date'] . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($cdrs) {
                $file = fopen('php://output', 'w');

                // CSV headers
                fputcsv($file, [
                    'UniqueID', 'Start', 'Answer', 'End', 'Duration', 'BillSec',
                    'Source', 'Destination', 'Disposition', 'Channel', 'DstChannel'
                ]);

                // CSV data
                foreach ($cdrs as $cdr) {
                    fputcsv($file, [
                        $cdr->uniqueid,
                        $cdr->start,
                        $cdr->answer,
                        $cdr->end,
                        $cdr->duration,
                        $cdr->billsec,
                        $cdr->src,
                        $cdr->dst,
                        $cdr->disposition,
                        $cdr->channel,
                        $cdr->dstchannel,
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return response()->json($cdrs);
    }
}
