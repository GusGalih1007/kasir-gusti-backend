<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\PageRoleAction;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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

        // DEBUG: Log the route and user info
        Log::info("Permission Check", [
            'route' => $routeName,
            'action' => $action,
            'user_id' => Auth::id(),
            'role_id' => Auth::user()->role_id
        ]);

        // Get page code from route name
        $pageCode = $this->getPageCodeFromRoute($routeName);

        // DEBUG: Log the page code found
        Log::info("Page Code Found", [
            'route' => $routeName,
            'page_code' => $pageCode
        ]);

        if (!$pageCode) {
            Log::error("No page code found for route", ['route' => $routeName]);
            abort(403, 'No permission configuration found for this route.');
        }

        // Check if user has permission for this page and action
        $hasPermission = $this->checkUserPermission($pageCode, $action);

        // DEBUG: Log the permission check result
        Log::info("Permission Check Result", [
            'route' => $routeName,
            'page_code' => $pageCode,
            'action' => $action,
            'role_id' => Auth::user()->role_id,
            'has_permission' => $hasPermission
        ]);

        if (!$hasPermission) {
            Log::warning("Permission denied", [
                'user_id' => Auth::id(),
                'role_id' => Auth::user()->role_id,
                'page_code' => $pageCode,
                'action' => $action
            ]);
            abort(403, "You do not have '{$action}' permission for this page.");
        }

        return $next($request);
    }

    /**
     * Check if user has permission for the given page and action
     * Only check page_role_actions table for user permissions
     */
    private function checkUserPermission($pageCode, $action)
    {
        // Check if there's a permission record in page_role_actions table for this role
        return PageRoleAction::where('role_id', Auth::user()->role_id)
            ->where('page_code', $pageCode)
            ->where('action', ucfirst($action))
            ->exists();
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
        ];

        return in_array($routeName, $authRoutes);
    }

    /**
     * Extract page code from route name using multiple strategies
     */
    private function getPageCodeFromRoute($routeName)
    {
        return Cache::remember("permission_route:{$routeName}", 3600, function () use ($routeName) {
            $allPages = Page::all();

            // Strategy 1: Try exact match with page name
            $pageCode = $this->findByPageNameMatch($routeName, $allPages);
            if ($pageCode)
                return $pageCode;

            // Strategy 2: Try keyword matching
            $pageCode = $this->findByKeywordMatching($routeName, $allPages);
            if ($pageCode)
                return $pageCode;

            // Strategy 3: Try route pattern matching
            $pageCode = $this->findByRoutePattern($routeName, $allPages);
            if ($pageCode)
                return $pageCode;

            return null;
        });
    }

    /**
     * Strategy 1: Match route name with page name
     */
    private function findByPageNameMatch($routeName, $allPages)
    {
        $routeKeywords = $this->extractKeywordsFromRoute($routeName);

        foreach ($allPages as $page) {
            $pageKeywords = $this->extractKeywordsFromString($page->page_name);

            // Check if all route keywords exist in page name
            $matches = array_intersect($routeKeywords, $pageKeywords);
            if (count($matches) === count($routeKeywords)) {
                return $page->page_code;
            }

            // Check if page name contains the main route keyword
            $mainKeyword = $routeKeywords[0] ?? '';
            if ($mainKeyword && stripos($page->page_name, $mainKeyword) !== false) {
                return $page->page_code;
            }
        }

        return null;
    }

    /**
     * Strategy 2: Keyword-based matching
     */
    private function findByKeywordMatching($routeName, $allPages)
    {
        $routeBase = $this->getRouteBase($routeName);
        $routeKeywords = $this->extractKeywordsFromString($routeBase);

        $bestMatch = null;
        $bestScore = 0;

        foreach ($allPages as $page) {
            $pageKeywords = $this->extractKeywordsFromString($page->page_name);

            $score = $this->calculateMatchScore($routeKeywords, $pageKeywords);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $page->page_code;
            }
        }

        // Only return if we have a reasonably good match
        return $bestScore >= 2 ? $bestMatch : null;
    }

    /**
     * Strategy 3: Route pattern matching for common patterns
     */
    private function findByRoutePattern($routeName, $allPages)
    {
        $routeBase = $this->getRouteBase($routeName);

        // Common pattern mappings
        $patternMappings = [
            'dashboard' => ['dashboard', 'home', 'main'],
            'category' => ['category', 'categories', 'classification'],
            'brand' => ['brand', 'brands', 'manufacturer'],
            'supplier' => ['supplier', 'suppliers', 'vendor'],
            'customer' => ['customer', 'customers', 'client'],
            'membership' => ['membership', 'members', 'subscription'],
            'role-permission' => ['role', 'permission', 'access'],
            'user' => ['user', 'users', 'account'],
            'product' => ['product', 'products', 'item'],
            'product-variant' => ['variant', 'variation', 'option'],
            'page' => ['page', 'pages', 'menu'],
            'transaction' => ['transaction', 'sales', 'order'],
        ];

        foreach ($patternMappings as $routePattern => $keywords) {
            if ($routeBase === $routePattern) {
                foreach ($allPages as $page) {
                    $pageLower = strtolower($page->page_name);
                    foreach ($keywords as $keyword) {
                        if (str_contains($pageLower, $keyword)) {
                            return $page->page_code;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Extract base route name (remove CRUD operations)
     */
    private function getRouteBase($routeName)
    {
        return preg_replace('/\.(index|create|store|show|edit|update|destroy)$/', '', $routeName);
    }

    /**
     * Extract keywords from a string
     */
    private function extractKeywordsFromString($string)
    {
        // Convert to lowercase and replace special characters with spaces
        $cleanString = strtolower(preg_replace('/[^a-zA-Z0-9]/', ' ', $string));

        // Split into words and remove common words
        $words = array_filter(explode(' ', $cleanString), function ($word) {
            return strlen($word) > 2 && !in_array($word, ['the', 'and', 'for', 'with', 'page', 'management']);
        });

        return array_values(array_unique($words));
    }

    /**
     * Extract keywords from route name
     */
    private function extractKeywordsFromRoute($routeName)
    {
        $routeBase = $this->getRouteBase($routeName);
        return $this->extractKeywordsFromString($routeBase);
    }

    /**
     * Calculate match score between route keywords and page keywords
     */
    private function calculateMatchScore($routeKeywords, $pageKeywords)
    {
        $score = 0;

        foreach ($routeKeywords as $routeKeyword) {
            foreach ($pageKeywords as $pageKeyword) {
                if ($routeKeyword === $pageKeyword) {
                    $score += 3; // Exact match
                } elseif (str_contains($pageKeyword, $routeKeyword) || str_contains($routeKeyword, $pageKeyword)) {
                    $score += 2; // Partial match
                } elseif (levenshtein($routeKeyword, $pageKeyword) <= 2) {
                    $score += 1; // Similar match
                }
            }
        }

        return $score;
    }
}