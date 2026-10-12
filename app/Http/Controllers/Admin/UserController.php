<?php


declare(strict_types=1);


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;


class UserController extends Controller
{
    protected AuditService $auditService;


    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }


    /**
     * Display list of users
     */
    public function index(Request $request): View
    {
        $query = User::query();


        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }


        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }


        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }


        $users = $query->orderBy('created_at', 'desc')->paginate(20);


        return view('admin.users.index', compact('users'));
    }


    /**
     * Show create user form
     */
    public function create(): View
    {
        $roles = ['customer', 'mechanic', 'sales', 'cashier', 'inventory', 'manager', 'super-admin'];
        return view('admin.users.create', compact('roles'));
    }


    /**
     * Store new user
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['customer', 'mechanic', 'sales', 'cashier', 'inventory', 'manager', 'super-admin'])],
            'is_active' => 'boolean',
        ]);


        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $validated['is_active'] ?? true,
            'email_verified_at' => now(),
        ]);


        $this->auditService->logCreated('user', $user->id, $user->toArray());


        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dibuat');
    }


    /**
     * Display user details
     */
    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }


    /**
     * Show edit user form
     */
    public function edit(User $user): View
    {
        $roles = ['customer', 'mechanic', 'sales', 'cashier', 'inventory', 'manager', 'super-admin'];
        return view('admin.users.edit', compact('user', 'roles'));
    }


    /**
     * Update user
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(['customer', 'mechanic', 'sales', 'cashier', 'inventory', 'manager', 'super-admin'])],
            'is_active' => 'boolean',
        ]);


        $oldValues = $user->toArray();


        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'is_active' => $validated['is_active'] ?? true,
        ];


        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }


        $user->update($updateData);


        $this->auditService->logUpdated('user', $user->id, $oldValues, $user->toArray());


        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil diupdate');
    }


    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        $oldStatus = $user->is_active;
        $user->update(['is_active' => !$oldStatus]);


        $this->auditService->log(
            action: $user->is_active ? 'activated' : 'deactivated',
            modelType: 'user',
            modelId: $user->id,
            oldValues: ['is_active' => $oldStatus],
            newValues: ['is_active' => $user->is_active],
            description: "User {$user->name} " . ($user->is_active ? 'activated' : 'deactivated')
        );


        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Status user berhasil diubah');
    }


    /**
     * Delete user
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->auditService->logDeleted('user', $user->id, $user->toArray());


        $user->delete();


        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus');
    }
}



