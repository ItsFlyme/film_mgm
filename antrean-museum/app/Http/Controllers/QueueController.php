<?php

namespace App\Http\Controllers;

use App\Exports\QueueHistoryWorkbook;
use App\Models\MuseumAd;
use App\Models\Queue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class QueueController extends Controller
{
    public function index()
    {
        $currentQueue = Queue::where('status', 'called')->latest()->first();
        $waitingQueues = Queue::where('status', 'waiting')->oldest()->get();

        return view('queue.index', compact('currentQueue', 'waitingQueues'));
    }

    public function monitorData(): JsonResponse
    {
        return response()->json([
            'currentQueue' => Queue::where('status', 'called')->latest()->first(),
            'waitingQueues' => Queue::where('status', 'waiting')->oldest()->get(),
        ]);
    }

    public function adsData(): JsonResponse
    {
        $ads = MuseumAd::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (MuseumAd $ad): array => [
                'id' => $ad->id,
                'title' => $ad->title,
                'description' => $ad->description,
                'image_url' => asset('storage/'.$ad->image_path),
                'duration_seconds' => $ad->duration_seconds,
            ]);

        return response()->json(['ads' => $ads]);
    }

    public function admin()
    {
        [$todayStart, $todayEnd] = $this->todayRange();

        Queue::where('created_at', '<', $todayStart)
            ->whereIn('status', ['waiting', 'called'])
            ->update(['status' => 'completed']);

        $currentQueue = Queue::whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('status', 'called')
            ->latest()
            ->first();
        $queues = Queue::whereBetween('created_at', [$todayStart, $todayEnd])->oldest()->get();
        $stats = $this->statistics();
        $ads = MuseumAd::orderBy('sort_order')->orderBy('id')->get();

        return view('queue.admin', compact('currentQueue', 'queues', 'stats', 'ads'));
    }

    public function kiosk()
    {
        return view('queue.kiosk');
    }

    public function storeAd(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
            'image' => 'required|image|mimes:jpg,jpeg,jpe,jfif,png,webp|max:2048',
            'duration_seconds' => 'required|integer|min:3|max:60',
            'sort_order' => 'nullable|integer|min:0|max:10000',
        ]);

        $imagePath = $request->file('image')->storePublicly('museum-ads', 'public');
        if ($imagePath === false) {
            throw new RuntimeException('Gambar iklan gagal disimpan.');
        }

        try {
            MuseumAd::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'image_path' => $imagePath,
                'duration_seconds' => (int) $validated['duration_seconds'],
                'sort_order' => isset($validated['sort_order'])
                    ? (int) $validated['sort_order']
                    : ((int) MuseumAd::max('sort_order') + 1),
                'is_active' => $request->boolean('is_active'),
            ]);
        } catch (Throwable $exception) {
            if (! Storage::disk('public')->delete($imagePath)) {
                throw new RuntimeException('Iklan gagal dibuat dan file gambarnya tidak dapat dibersihkan.', 0, $exception);
            }

            throw $exception;
        }

        return redirect()->to(route('queue.admin').'#carousel-management')
            ->with('success', 'Iklan carousel berhasil ditambahkan.');
    }

    public function toggleAd(MuseumAd $ad)
    {
        $ad->is_active = ! $ad->is_active;
        $ad->save();

        return redirect()->to(route('queue.admin').'#carousel-management')
            ->with('success', 'Status iklan carousel berhasil diperbarui.');
    }

    public function destroyAd(MuseumAd $ad)
    {
        $imagePath = $ad->image_path;
        $ad->delete();

        if (! Storage::disk('public')->delete($imagePath)) {
            throw new RuntimeException('Iklan terhapus, tetapi file gambarnya gagal dihapus.');
        }

        return redirect()->to(route('queue.admin').'#carousel-management')
            ->with('success', 'Iklan carousel berhasil dihapus.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'visitor_type' => 'required|in:individual,group',
            'visitor_name' => 'required|string|max:255',
            'institution_name' => 'required_if:visitor_type,group|nullable|string|max:255',
            'movie_title' => 'required|string|max:255',
            'session_time' => 'required|string|max:50',
        ]);

        if ($validated['visitor_type'] === 'group') {
            $institutionName = trim($validated['institution_name']);
            [$todayStart, $todayEnd] = $this->todayRange();
            $alreadyRegistered = Queue::whereBetween('created_at', [$todayStart, $todayEnd])
                ->where('visitor_type', 'group')
                ->where('session_time', $validated['session_time'])
                ->whereRaw('LOWER(institution_name) = ?', [mb_strtolower($institutionName)])
                ->exists();

            if ($alreadyRegistered) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Instansi ini sudah terdaftar pada sesi tersebut hari ini.');
            }

            $validated['institution_name'] = $institutionName;
        } else {
            $validated['institution_name'] = null;
        }

        $latestQueue = Queue::latest()->first();
        $number = 1;
        if ($latestQueue && $latestQueue->queue_number) {
            $number = intval(str_replace('A-', '', $latestQueue->queue_number)) + 1;
        }

        $queueNumber = 'A-'.sprintf('%03d', $number);

        Queue::create([
            'queue_number' => $queueNumber,
            'visitor_name' => $validated['visitor_name'],
            'visitor_type' => $validated['visitor_type'],
            'institution_name' => $validated['institution_name'],
            'movie_title' => $validated['movie_title'],
            'session_time' => $validated['session_time'],
            'status' => 'waiting',
        ]);

        return redirect()->route('queue.kiosk')->with('success', $queueNumber);
    }

    public function callNext()
    {
        [$todayStart, $todayEnd] = $this->todayRange();

        Queue::whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('status', 'called')
            ->update(['status' => 'completed']);

        $nextQueue = Queue::whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('status', 'waiting')
            ->oldest()
            ->first();

        if ($nextQueue) {
            $nextQueue->update(['status' => 'called']);

            return response()->json([
                'success' => true,
                'data' => $nextQueue,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Antrean sudah habis!',
        ]);
    }

    public function getData()
    {
        [$todayStart, $todayEnd] = $this->todayRange();

        return response()->json([
            'currentQueue' => Queue::whereBetween('created_at', [$todayStart, $todayEnd])
                ->where('status', 'called')
                ->latest()
                ->first(),
            'waitingQueues' => Queue::whereBetween('created_at', [$todayStart, $todayEnd])
                ->where('status', 'waiting')
                ->oldest()
                ->get(),
            'queues' => Queue::whereBetween('created_at', [$todayStart, $todayEnd])->oldest()->get(),
            'stats' => $this->statistics(),
        ]);
    }

    public function destroy($id)
    {
        $queue = Queue::findOrFail($id);
        $queue->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Antrean berhasil dihapus.',
            ]);
        }

        return redirect()->route('queue.admin')->with('success', 'Antrean berhasil dihapus.');
    }

    public function destroyAll()
    {
        [$todayStart, $todayEnd] = $this->todayRange();
        Queue::whereBetween('created_at', [$todayStart, $todayEnd])->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Semua antrean hari ini berhasil dihapus.',
            ]);
        }

        return redirect()->route('queue.admin')->with('success', 'Semua antrean hari ini berhasil dihapus.');
    }

    public function history(Request $request)
    {
        $request->validate(['date' => 'sometimes|date_format:Y-m-d']);
        $historyQuery = $this->historyQuery();
        if ($request->filled('date')) {
            $date = Carbon::createFromFormat('Y-m-d', $request->input('date'), 'Asia/Jakarta');
            $historyQuery->whereBetween('created_at', [
                $date->copy()->startOfDay()->utc(),
                $date->copy()->endOfDay()->utc(),
            ]);
        }

        $queues = $historyQuery->latest()->get();
        [$todayStart, $todayEnd] = $this->todayRange();
        $currentQueue = Queue::whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('status', 'called')
            ->latest()
            ->first();
        $stats = $this->statistics();
        $ads = MuseumAd::orderBy('sort_order')->orderBy('id')->get();

        return view('queue.admin', compact('currentQueue', 'queues', 'stats', 'ads'));
    }

    public function exportHistory()
    {
        $queues = $this->historyQuery()->latest()->get();
        $path = QueueHistoryWorkbook::create($queues);

        return response()->download(
            $path,
            'riwayat-antrean-'.Carbon::now('Asia/Jakarta')->format('Y-m-d').'.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }

    private function historyQuery(): Builder
    {
        $localNow = Carbon::now('Asia/Jakarta');

        return Queue::whereBetween('created_at', [
            $localNow->copy()->subDays(29)->startOfDay()->utc(),
            $localNow->copy()->utc(),
        ]);
    }

    private function statistics(): array
    {
        [$todayStart, $todayEnd] = $this->todayRange();
        [$monthStart, $monthEnd] = $this->monthRange();

        return [
            'today' => $this->countsBetween($todayStart, $todayEnd),
            'month' => $this->countsBetween($monthStart, $monthEnd),
        ];
    }

    private function todayRange(): array
    {
        $today = Carbon::now('Asia/Jakarta');

        return [
            $today->copy()->startOfDay()->utc(),
            $today->copy()->endOfDay()->utc(),
        ];
    }

    private function monthRange(): array
    {
        $month = Carbon::now('Asia/Jakarta');

        return [
            $month->copy()->startOfMonth()->utc(),
            $month->copy()->endOfMonth()->utc(),
        ];
    }

    private function countsBetween($start, $end): array
    {
        $counts = Queue::whereBetween('created_at', [$start, $end])
            ->selectRaw('visitor_type, COUNT(*) as total')
            ->groupBy('visitor_type')
            ->pluck('total', 'visitor_type');

        $individuals = (int) $counts->get('individual', 0);
        $groups = (int) $counts->get('group', 0);

        return [
            'total' => $individuals + $groups,
            'individual' => $individuals,
            'group' => $groups,
        ];
    }
}
