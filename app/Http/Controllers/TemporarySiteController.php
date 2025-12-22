<?php

namespace App\Http\Controllers;

use App\Models\TemporarySite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TemporarySiteController extends Controller
{
    /**
     * Display a listing of temporary sites.
     */
    public function index(Request $request)
    {
        $query = TemporarySite::query();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filters
        if ($request->filled('atoll')) {
            $query->filterAtoll($request->atoll);
        }

        if ($request->filled('coverage')) {
            $query->filterCoverage($request->coverage);
        }

        if ($request->filled('status')) {
            $query->filterStatus($request->status);
        }

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query->filterDateRange($request->start_date, $request->end_date);
        }

        // Sorting
        $sortBy = $request->get('sort', 'temp_site_id');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $temporarySites = $query->paginate(20)->withQueryString();

        // Get unique values for filter dropdowns
        $atolls = TemporarySite::select('atoll_code')->distinct()->orderBy('atoll_code')->pluck('atoll_code');
        $coverages = TemporarySite::select('coverage')->distinct()->orderBy('coverage')->pluck('coverage');
        $statuses = ['Temporary', 'Resolved', 'Remove from list', 'Monitoring'];

        return view('temporary-sites.index', compact('temporarySites', 'atolls', 'coverages', 'statuses'));
    }

    /**
     * Show the form for creating a new temporary site.
     */
    public function create()
    {
        return view('temporary-sites.create');
    }

    /**
     * Store a newly created temporary site in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'temp_site_id' => ['required', 'string', 'max:255', 'unique:temporary_sites,temp_site_id'],
            'atoll_code' => ['required', 'string', 'max:255'],
            'site_name' => ['required', 'string', 'max:255'],
            'coverage' => ['required', 'string', 'max:255'],
            'added_date' => ['required', 'date'],
            'transmission_or_backhaul' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'status' => ['required', 'in:Temporary,Resolved,Remove from list,Monitoring'],
            'review_date' => ['nullable', 'date'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        TemporarySite::create($validated);

        return redirect()->route('temporary-sites.index')
            ->with('success', 'Temporary site created successfully.');
    }

    /**
     * Display the specified temporary site.
     */
    public function show(TemporarySite $temporarySite)
    {
        $temporarySite->load(['creator', 'updater', 'audits.user']);

        return view('temporary-sites.show', compact('temporarySite'));
    }

    /**
     * Show the form for editing the specified temporary site.
     */
    public function edit(TemporarySite $temporarySite)
    {
        return view('temporary-sites.edit', compact('temporarySite'));
    }

    /**
     * Update the specified temporary site in storage.
     */
    public function update(Request $request, TemporarySite $temporarySite)
    {
        $validated = $request->validate([
            'temp_site_id' => ['required', 'string', 'max:255', 'unique:temporary_sites,temp_site_id,' . $temporarySite->id],
            'atoll_code' => ['required', 'string', 'max:255'],
            'site_name' => ['required', 'string', 'max:255'],
            'coverage' => ['required', 'string', 'max:255'],
            'added_date' => ['required', 'date'],
            'transmission_or_backhaul' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'status' => ['required', 'in:Temporary,Resolved,Remove from list,Monitoring'],
            'review_date' => ['nullable', 'date'],
        ]);

        $validated['updated_by'] = Auth::id();

        $temporarySite->update($validated);

        return redirect()->route('temporary-sites.index')
            ->with('success', 'Temporary site updated successfully.');
    }

    /**
     * Remove the specified temporary site from storage.
     */
    public function destroy(TemporarySite $temporarySite)
    {
        $temporarySite->delete();

        return redirect()->route('temporary-sites.index')
            ->with('success', 'Temporary site deleted successfully.');
    }

    /**
     * Remove multiple temporary sites from storage.
     */
    public function destroyBulk(Request $request)
    {
        $validated = $request->validate([
            'site_ids' => ['required', 'array'],
            'site_ids.*' => ['required', 'integer', 'exists:temporary_sites,id'],
        ]);

        $count = TemporarySite::whereIn('id', $validated['site_ids'])->delete();

        return redirect()->route('temporary-sites.index')
            ->with('success', "Successfully deleted {$count} temporary site(s).");
    }

    /**
     * Show the import form.
     */
    public function importForm()
    {
        return view('temporary-sites.import');
    }

    /**
     * Process the import.
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_type' => ['required', 'in:file,text'],
            'csv_file' => ['required_if:import_type,file', 'file', 'mimes:csv,txt'],
            'csv_text' => ['required_if:import_type,text', 'string'],
        ]);

        $data = [];

        if ($request->import_type === 'file') {
            $file = $request->file('csv_file');
            $content = file_get_contents($file->getRealPath());
            $rows = array_map('str_getcsv', explode("\n", $content));
        } else {
            $rows = array_map('str_getcsv', explode("\n", $request->csv_text));
        }

        // Remove empty rows
        $rows = array_filter($rows, function($row) {
            return !empty(array_filter($row));
        });

        if (empty($rows)) {
            return back()->with('error', 'No data found in the import.');
        }

        // Check if first row is header
        $firstRow = $rows[0];
        $hasHeader = false;

        if (isset($firstRow[0]) && (
            stripos($firstRow[0], 'temp') !== false ||
            stripos($firstRow[0], 'id') !== false ||
            stripos($firstRow[0], 'TS') === false
        )) {
            $hasHeader = true;
            array_shift($rows); // Remove header row
        }

        $imported = 0;
        $updated = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            try {
                // Clean the row data
                $row = array_map('trim', $row);

                // Skip if not enough columns
                if (count($row) < 7) {
                    continue;
                }

                // Parse the date (support both MM/DD/YYYY and DD/MM/YYYY)
                $dateString = $row[4];
                $addedDate = $this->parseDate($dateString);

                $data = [
                    'temp_site_id' => $row[0],
                    'atoll_code' => $row[1],
                    'site_name' => $row[2],
                    'coverage' => $row[3],
                    'added_date' => $addedDate,
                    'transmission_or_backhaul' => $row[5],
                    'remarks' => $row[6] ?? null,
                    'status' => 'Temporary',
                    'updated_by' => Auth::id(),
                ];

                // Check if exists
                $existingSite = TemporarySite::where('temp_site_id', $data['temp_site_id'])->first();

                if ($existingSite) {
                    $existingSite->update($data);
                    $existingSite->logImport();
                    $updated++;
                } else {
                    $data['created_by'] = Auth::id();
                    $site = TemporarySite::create($data);
                    $site->logImport();
                    $imported++;
                }
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        $message = "Import completed: {$imported} new site(s) created, {$updated} site(s) updated.";

        if (!empty($errors)) {
            return redirect()->route('temporary-sites.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
        }

        return redirect()->route('temporary-sites.index')
            ->with('success', $message);
    }

    /**
     * Parse date from various formats.
     */
    private function parseDate($dateString)
    {
        // Try to parse DD-MM-YYYY or DD/MM/YYYY
        if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/', $dateString, $matches)) {
            $day = $matches[1];
            $month = $matches[2];
            $year = $matches[3];

            // If day > 12, it's definitely DD-MM-YYYY
            if ($day > 12) {
                return Carbon::createFromFormat('d-m-Y', "{$day}-{$month}-{$year}")->format('Y-m-d');
            }

            // If month > 12, it's definitely MM-DD-YYYY
            if ($month > 12) {
                return Carbon::createFromFormat('m-d-Y', "{$day}-{$month}-{$year}")->format('Y-m-d');
            }

            // Assume DD-MM-YYYY by default
            return Carbon::createFromFormat('d-m-Y', "{$day}-{$month}-{$year}")->format('Y-m-d');
        }

        // Fallback to Carbon parse
        return Carbon::parse($dateString)->format('Y-m-d');
    }

    /**
     * Toggle technology status (online/offline) for a temporary site.
     */
    public function toggleTechStatus(Request $request, TemporarySite $temporarySite)
    {
        $request->validate([
            'tech' => 'required|in:2g,3g,4g',
            'is_online' => 'required|boolean',
        ]);

        $tech = $request->input('tech');
        $isOnline = $request->input('is_online');

        // Map tech to database field
        $fieldMap = [
            '2g' => 'is_2g_online',
            '3g' => 'is_3g_online',
            '4g' => 'is_4g_online',
        ];

        $field = $fieldMap[$tech];

        // Update the status
        $temporarySite->update([
            $field => $isOnline,
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => ucfirst($tech) . ' status updated successfully',
            'is_online' => $isOnline,
        ]);
    }
}
