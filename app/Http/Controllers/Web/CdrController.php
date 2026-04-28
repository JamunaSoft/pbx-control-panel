<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cdr;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CdrController extends Controller
{
    /**
     * Display a listing of CDRs with filtering.
     */
    public function index(Request $request)
    {
        $query = Cdr::query();

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('start', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('start', '<=', $request->end_date);
        }

        // Filter by caller/callee
        if ($request->has('caller')) {
            $query->where('src', 'like', '%' . $request->caller . '%');
        }

        if ($request->has('callee')) {
            $query->where('dst', 'like', '%' . $request->callee . '%');
        }

        // Filter by disposition
        if ($request->has('disposition')) {
            $query->where('disposition', $request->disposition);
        }

        // Filter by duration
        if ($request->has('min_duration')) {
            $query->where('duration', '>=', $request->min_duration);
        }

        if ($request->has('max_duration')) {
            $query->where('duration', '<=', $request->max_duration);
        }

        $cdrs = $query->orderBy('start', 'desc')->paginate(50);

        return view('cdr.index', compact('cdrs'));
    }

    /**
     * Export CDRs to CSV with current filters.
     */
    public function export(Request $request)
    {
        $query = Cdr::query();

        // Apply same filters as index
        if ($request->has('start_date')) {
            $query->whereDate('start', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('start', '<=', $request->end_date);
        }

        if ($request->has('caller')) {
            $query->where('src', 'like', '%' . $request->caller . '%');
        }

        if ($request->has('callee')) {
            $query->where('dst', 'like', '%' . $request->callee . '%');
        }

        if ($request->has('disposition')) {
            $query->where('disposition', $request->disposition);
        }

        if ($request->has('min_duration')) {
            $query->where('duration', '>=', $request->min_duration);
        }

        if ($request->has('max_duration')) {
            $query->where('duration', '<=', $request->max_duration);
        }

        $cdrs = $query->orderBy('start', 'desc')->get();

        $filename = 'cdr_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return new StreamedResponse(function() use ($cdrs) {
            $handle = fopen('php://output', 'w');

            // CSV headers
            fputcsv($handle, [
                'Date/Time',
                'Caller',
                'Callee',
                'Duration',
                'Billable Duration',
                'Disposition',
                'Context',
                'Channel',
                'Destination Channel',
                'Unique ID'
            ]);

            // CSV data
            foreach ($cdrs as $cdr) {
                fputcsv($handle, [
                    $cdr->start ? $cdr->start->format('Y-m-d H:i:s') : '',
                    $cdr->src,
                    $cdr->dst,
                    $cdr->duration,
                    $cdr->billsec,
                    $cdr->disposition,
                    $cdr->dcontext,
                    $cdr->channel,
                    $cdr->dstchannel,
                    $cdr->uniqueid,
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
