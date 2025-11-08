<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AdminUser;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * Check if admin has required role.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $adminId = session('admin_id');

        if (!$adminId) {
            return redirect()->route('admin.login');
        }

        $admin = AdminUser::find($adminId);

        if (!$admin) {
            return redirect()->route('admin.login');
        }

        // Convert role names to role IDs
        $roleIds = $this->convertRolesToIds($roles);

        // Check if admin has one of the required roles
        if (!in_array($admin->role, $roleIds)) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }

    /**
     * Convert role names to IDs.
     */
    protected function convertRolesToIds(array $roles): array
    {
        $roleMap = [
            'super_admin' => AdminUser::ROLE_SUPER_ADMIN,
            'document_admin' => AdminUser::ROLE_DOCUMENT_ADMIN,
            'legal_admin' => AdminUser::ROLE_LEGAL_ADMIN,
        ];

        return array_map(fn($role) => $roleMap[$role] ?? null, $roles);
    }
}
