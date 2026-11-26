<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffAccountController extends Controller
{
    public function index(): View
    {
        return view('queue.accounts', [
            'accounts' => User::query()->orderBy('username')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'alpha_dash', 'min:3', 'max:50', 'unique:users,username'],
            'role' => ['required', Rule::in(['admin', 'super_admin'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['username'],
            'email' => $validated['username'].'@staff.local',
            'username' => $validated['username'],
            'role' => $validated['role'],
            'password' => $validated['password'],
        ]);

        return redirect()->route('queue.accounts.index')->with('success', 'Akun petugas berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'username' => [
                'required', 'string', 'alpha_dash', 'min:3', 'max:50',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['admin', 'super_admin'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($user->role === 'super_admin'
            && $validated['role'] !== 'super_admin'
            && ($user->is($request->user()) || User::where('role', 'super_admin')->count() <= 1)) {
            return back()->withErrors([
                'role' => 'Role akun aktif tidak dapat diturunkan dan minimal harus ada satu akun super admin.',
            ]);
        }

        $user->username = $validated['username'];
        $user->name = $validated['username'];
        $user->role = $validated['role'];

        if ($request->filled('password')) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()->route('queue.accounts.index')->with('success', 'Akun petugas berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors([
                'account' => 'Anda tidak dapat menghapus akun yang sedang digunakan.',
            ]);
        }

        if ($user->role === 'super_admin' && User::where('role', 'super_admin')->count() <= 1) {
            return back()->withErrors([
                'account' => 'Minimal harus ada satu akun super admin.',
            ]);
        }

        $user->delete();

        return redirect()->route('queue.accounts.index')->with('success', 'Akun petugas berhasil dihapus.');
    }
}
