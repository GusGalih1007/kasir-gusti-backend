<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\PageRoleAction;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class CheckPermmission
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $action)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login.form');
        }

        // Allow if user is super admin (role_id = 1)
        if (Auth::user()->role_id == 1) {
            return $next($request);
        }

        // Get the current route name
        $routeName = $request->route()->getName();

        // Allow routes without names (fallback)
        if (!$routeName) {
            return $next($request);
        }

        // Skip permission check for auth routes
        if ($this->isAuthRoute($routeName)) {
            return $next($request);
        }

        // Get page code from route name
        $pageCode = $this->getPageCodeFromRoute($routeName);

        if (!$pageCode) {
            abort(403, 'No permission configuration found for this route.');
        }

        // Check if user has permission for this page and action
        $hasPermission = PageRoleAction::where('role_id', Auth::user()->role_id)
            ->where('page_code', $pageCode)
            ->where('action', ucfirst($action))
            ->exists();

        if (!$hasPermission) {
            abort(403, "You do not have '{$action}' permission for '{$pageCode}'.");
        }

        return $next($request);
    }

    /**
     * Check if route is an authentication route (should be public)
     */
    private function isAuthRoute($routeName)
    {
        $authRoutes = [
            'login.form',
            'login.post',
            'logout.user',
            'password.request',
            'password.email',
            'password.reset',
            // Add other auth routes as needed
        ];

        return in_array($routeName, $authRoutes);
    }

    /**
     * Extract page code from route name using multiple strategies
     */
    private function getPageCodeFromRoute($routeName)
    {
        return Cache::remember("permission_route:{$routeName}", 3600, function () use ($routeName) {

            // Strategy 1: Direct mapping for common routes
            $directMappings = $this->getDirectRouteMappings();
            if (isset($directMappings[$routeName])) {
                return $directMappings[$routeName];
            }

            // Strategy 2: Extract from route name patterns
            $pageCode = $this->extractPageCodeFromPattern($routeName);
            if ($pageCode && $this->pageExists($pageCode)) {
                return $pageCode;
            }

            // Strategy 3: Try to find similar page in database
            $similarPageCode = $this->findSimilarPageCode($routeName);
            if ($similarPageCode) {
                return $similarPageCode;
            }

            return null;
        });
    }

    /**
     * Direct route to page code mappings for common cases
     */
    private function getDirectRouteMappings()
    {
        return [
            // Dashboard
            'dashboard' => 'DASHBOARD',

            // Special routes
            'role-permission.update-permission' => 'ROLE_PERMISSION',

            // Product variant routes
            'product-variant.index' => 'PRODUCT_VARIANT',
            'product-variant.create' => 'PRODUCT_VARIANT',
            'product-variant.store' => 'PRODUCT_VARIANT',
            'product-variant.show' => 'PRODUCT_VARIANT',
            'product-variant.edit' => 'PRODUCT_VARIANT',
            'product-variant.update' => 'PRODUCT_VARIANT',
            'product-variant.destroy' => 'PRODUCT_VARIANT',
        ];
    }

    /**
     * Extract page code from route name using patterns
     */
    private function extractPageCodeFromPattern($routeName)
    {
        // Remove CRUD operations from route name
        $baseRoute = preg_replace('/\.(index|create|store|show|edit|update|destroy)$/', '', $routeName);

        // Convert to UPPER_SNAKE_CASE
        $pageCode = strtoupper(preg_replace('/[-\.]/', '_', $baseRoute));

        return $pageCode;
    }

    /**
     * Find similar page code in database
     */
    private function findSimilarPageCode($routeName)
    {
        $allPages = Page::all();

        // Clean route name for comparison
        $cleanRoute = preg_replace('/\.(index|create|store|show|edit|update|destroy)$/', '', $routeName);
        $searchTerm = strtoupper(str_replace(['-', '_', '.'], '', $cleanRoute));

        foreach ($allPages as $page) {
            $cleanPageCode = strtoupper(str_replace(['-', '_'], '', $page->page_code));

            // Exact match after cleaning
            if ($cleanPageCode === $searchTerm) {
                return $page->page_code;
            }

            // Contains match
            if (str_contains($cleanPageCode, $searchTerm) || str_contains($searchTerm, $cleanPageCode)) {
                return $page->page_code;
            }
        }

        return null;
    }

    /**
     * Check if page exists in database
     */
    private function pageExists($pageCode)
    {
        return Cache::remember("page_exists:{$pageCode}", 3600, function () use ($pageCode) {
            return Page::where('page_code', $pageCode)->exists();
        });
    }
}
