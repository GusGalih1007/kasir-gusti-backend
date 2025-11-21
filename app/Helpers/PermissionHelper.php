<?php

namespace App\Helpers;

use App\Models\PageRoleAction;
use App\Models\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PermissionHelper
{
    public static function hasPermission($action, $pageName = null)
    {   
        if (Auth::user()->role_id == 1) {
            return true;
        }

        // Jika pageName tidak diberikan, cari dari route name
        if (!$pageName) {
            $routeName = request()->route()->getName();
            $pageName = self::getPageNameFromRoute($routeName);
        }

        if (!$pageName) {
            return false;
        }

        $page = Page::where('page_name', $pageName)->first();
        if (!$page) {
            return false;
        }

        return PageRoleAction::where('role_id', Auth::user()->role_id)
            ->where('page_code', $page->page_code)
            ->where('action', ucfirst($action))
            ->exists();
    }

    /**
     * Get page name from route name dynamically from database
     */
    public static function getPageNameFromRoute($routeName)
    {
        return Cache::remember("route_to_page_mapping:{$routeName}", 3600, function () use ($routeName) {
            $allPages = Page::all();
            $routeBase = self::getRouteBase($routeName);
            
            // Strategy 1: Exact match dengan page name
            $pageName = self::findByExactMatch($routeBase, $allPages);
            if ($pageName) return $pageName;

            // Strategy 2: Keyword matching
            $pageName = self::findByKeywordMatching($routeBase, $allPages);
            if ($pageName) return $pageName;

            // Strategy 3: Pattern matching
            $pageName = self::findByPatternMatching($routeBase, $allPages);
            if ($pageName) return $pageName;

            return null;
        });
    }

    /**
     * Strategy 1: Exact match
     */
    private static function findByExactMatch($routeBase, $allPages)
    {
        foreach ($allPages as $page) {
            $pageNameLower = strtolower($page->page_name);
            $routeBaseLower = strtolower($routeBase);
            
            if ($pageNameLower === $routeBaseLower) {
                return $page->page_name;
            }
        }
        return null;
    }

    /**
     * Strategy 2: Keyword matching
     */
    private static function findByKeywordMatching($routeBase, $allPages)
    {
        $routeKeywords = self::extractKeywords($routeBase);
        
        $bestMatch = null;
        $bestScore = 0;

        foreach ($allPages as $page) {
            $pageKeywords = self::extractKeywords($page->page_name);
            $score = self::calculateMatchScore($routeKeywords, $pageKeywords);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $page->page_name;
            }
        }

        return $bestScore >= 2 ? $bestMatch : null;
    }

    /**
     * Strategy 3: Pattern matching dengan common words
     */
    private static function findByPatternMatching($routeBase, $allPages)
    {
        $commonMappings = [
            'index' => ['list', 'index', 'view'],
            'create' => ['create', 'add', 'new'],
            'edit' => ['edit', 'update', 'modify'],
            'destroy' => ['delete', 'remove', 'destroy']
        ];

        $routeBaseClean = self::removeCommonWords($routeBase);

        foreach ($allPages as $page) {
            $pageNameClean = self::removeCommonWords($page->page_name);
            
            // Check if route base contains page name or vice versa
            if (str_contains(strtolower($routeBaseClean), strtolower($pageNameClean)) || 
                str_contains(strtolower($pageNameClean), strtolower($routeBaseClean))) {
                return $page->page_name;
            }

            // Check word similarity
            similar_text(strtolower($routeBaseClean), strtolower($pageNameClean), $percent);
            if ($percent > 60) {
                return $page->page_name;
            }
        }

        return null;
    }

    /**
     * Extract base route name
     */
    private static function getRouteBase($routeName)
    {
        return preg_replace('/\.(index|create|store|show|edit|update|destroy)$/', '', $routeName);
    }

    /**
     * Extract keywords from string
     */
    private static function extractKeywords($string)
    {
        $cleanString = strtolower(preg_replace('/[^a-zA-Z0-9]/', ' ', $string));
        $words = array_filter(explode(' ', $cleanString), function ($word) {
            return strlen($word) > 2 && !in_array($word, ['the', 'and', 'for', 'with', 'page', 'management']);
        });
        return array_values(array_unique($words));
    }

    /**
     * Calculate match score
     */
    private static function calculateMatchScore($routeKeywords, $pageKeywords)
    {
        $score = 0;
        foreach ($routeKeywords as $routeKeyword) {
            foreach ($pageKeywords as $pageKeyword) {
                if ($routeKeyword === $pageKeyword) {
                    $score += 3;
                } elseif (str_contains($pageKeyword, $routeKeyword) || str_contains($routeKeyword, $pageKeyword)) {
                    $score += 2;
                }
            }
        }
        return $score;
    }

    /**
     * Remove common words from string
     */
    private static function removeCommonWords($string)
    {
        $commonWords = ['page', 'management', 'system', 'admin', 'user', 'list', 'view'];
        $words = explode(' ', strtolower($string));
        $filteredWords = array_filter($words, function ($word) use ($commonWords) {
            return !in_array($word, $commonWords) && strlen($word) > 2;
        });
        return implode(' ', $filteredWords);
    }

    /**
     * Get all pages for dropdown atau keperluan lain
     */
    public static function getAllPages()
    {
        return Cache::remember('all_pages_list', 3600, function () {
            return Page::all()->pluck('page_name', 'page_code')->toArray();
        });
    }

    /**
     * Clear cache untuk mapping (bisa dipanggil ketika ada update page)
     */
    public static function clearCache()
    {
        Cache::flush();
    }
}