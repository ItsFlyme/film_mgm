<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    // 1. Tampilan Layar Utama / TV Monitor (Untuk Pengunjung di Ruang Tunggu)
    public function index()
    {
        $currentQueue = Queue::where('status', 'called')->latest()->first();
        $waitingQueues = Queue::where('status', 'waiting')->oldest()->take(5)->get();

        return view('queue.index', compact('currentQueue', 'waitingQueues'));
    }

    public function monitorData(): JsonResponse
    {
        return response()->json([
            'currentQueue' => Queue::where('status', 'called')->latest()->first(),
            'waitingQueues' => Queue::where('status', 'waiting')->oldest()->take(5)->get(),
        ]);
    }

    // 2. Tampilan Dashboard Petugas / Admin Operator
    public function admin()
    {
        // OTOMATIS: Ubah sisa antrean hari sebelumnya yang masih 'waiting' / 'called' menjadi 'completed'
        Queue::whereDate('created_at', '<', today())
            ->whereIn('status', ['waiting', 'called'])
            ->update(['status' => 'completed']);

        $currentQueue = Queue::where('status', 'called')->latest()->first();
        $queues = Queue::whereDate('created_at', today())->oldest()->get();

        return view('queue.admin', compact('currentQueue', 'queues'));
    }

    // 3. Tampilan Kiosk / Tempat Pengambilan Tiket Mandiri
    public function kiosk()
    {
        return view('queue.kiosk');
    }

    // 4. Proses Simpan Tiket Baru dari Kiosk
    public function store(Request $request)
    {
        $request->validate([
            'visitor_name' => 'required|string|max:255',
            'movie_title' => 'required|string',
            'session_time' => 'required|string',
        ]);

        $movieTitle = $request->movie_title;
        $sessionTime = $request->session_time;

        // Cek apakah sesi untuk film tersebut pada hari ini sudah ada yang booking
        $existingSession = Queue::whereDate('created_at', today())
            ->where('movie_title', $movieTitle)
            ->where('session_time', $sessionTime)
            ->whereIn('status', ['waiting', 'called', 'completed'])
            ->exists();

        if ($existingSession) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Maaf, sesi pemutaran "' . $movieTitle . ' (' . $sessionTime . ')" sudah ke-booking/terisi untuk hari ini!');
        }

        // AMBIL NOMOR URUT TERAKHIR SECARA GLOBAL (Menyambung terus meskipun berganti hari)
        // Contoh: Jika hari sebelumnya terakhir A-009, maka hari ini dimulai dari A-010
        $latestQueue = Queue::latest()->first();

        $number = 1;
        if ($latestQueue && $latestQueue->queue_number) {
            $extractNumber = intval(str_replace('A-', '', $latestQueue->queue_number));
            $number = $extractNumber + 1;
        }

        $queueNumber = 'A-' . sprintf('%03d', $number);

        Queue::create([
            'queue_number' => $queueNumber,
            'visitor_name' => $request->visitor_name ?? 'Pengunjung Museum',
            'movie_title' => $movieTitle,
            'session_time' => $sessionTime,
            'status' => 'waiting'
        ]);

        return redirect()->route('queue.kiosk')->with('success', $queueNumber);
    }

    // 5. Proses Panggil Antrean Berikutnya oleh Admin
    public function callNext()
    {
        // Ubah status antrean yang sedang 'called' menjadi 'completed'
        Queue::where('status', 'called')->update(['status' => 'completed']);

        // Ambil antrean berikutnya dengan status 'waiting'
        $nextQueue = Queue::where('status', 'waiting')->oldest()->first();

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
        $currentQueue = Queue::where('status', 'called')->latest()->first();

        // Hapus ->take(5) agar mengambil SEMUA antrean yang berstatus waiting
        $waitingQueues = Queue::where('status', 'waiting')->get();

        $queues = Queue::whereDate('created_at', today())->oldest()->get();

        return response()->json([
            'currentQueue' => $currentQueue,
            'waitingQueues' => $waitingQueues,
            'queues' => $queues
        ]);
    }

    public function destroy($id)
    {
        $queue = Queue::findOrFail($id);
        $queue->delete();

        // Jika diakses via AJAX/JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Antrean berhasil dihapus.'
            ]);
        }

        return redirect()->route('queue.admin')->with('success', 'Antrean berhasil dihapus.');
    }

    public function destroyAll()
    {
        Queue::truncate(); // Atau bisa menggunakan Queue::query()->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Semua antrean berhasil dikosongkan.'
            ]);
        }

        return redirect()->route('queue.admin')->with('success', 'Semua antrean berhasil dikosongkan.');
    }

    public function history(Request $request)
    {
        $currentQueue = Queue::where('status', 'called')->latest()->first();

        // Ambil semua data riwayat dari awal, urutkan dari yang terbaru
        $query = Queue::latest();

        // Opsional: Filter berdasarkan tanggal jika admin memilih filter tanggal
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $queues = $query->get();

        return view('queue.admin', compact('currentQueue', 'queues'));
    }
}
