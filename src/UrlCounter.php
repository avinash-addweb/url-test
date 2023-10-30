<?php

namespace Threecolts\Phptest;

class UrlCounter
{
    /**
     * This function counts how many unique normalized valid URLs were passed to the function
     *
     * Accepts a list of URLs
     *
     * Example:
     *
     * input: ['https://example.com']
     * output: 1
     *
     * Notes:
     *  - assume none of the URLs have authentication information (username, password).
     *
     * Normalized URL:
     *  - process in which a URL is modified and standardized: https://en.wikipedia.org/wiki/URL_normalization
     *
     *    For example.
     *    These 2 urls are the same:
     *    input: ["https://example.com", "https://example.com/"]
     *    output: 1
     *
     *    These 2 are not the same:
     *    input: ["https://example.com", "http://example.com"]
     *    output 2
     *
     *    These 2 are the same:
     *    input: ["https://example.com?", "https://example.com"]
     *    output: 1
     *
     *    These 2 are the same:
     *    input: ["https://example.com?a=1&b=2", "https://example.com?b=2&a=1"]
     *    output: 1
     */

    /* @var $urls : string[] */
    public function countUniqueUrls(?array $urls)
    {
        $normalizedUrls = [];

        foreach($urls as $url) {
            // Parse the URL into its components
            $url = parse_url($url);
            
            // Invalid URL
            if ($url === false) {
                continue;
            }
    
            // Step 1: Change Scheme and host name to lower case
            $scheme = '';
            if(isset($url['scheme'])) {
                $scheme = strtolower($url['scheme']);
            }
    
            $host = '';
            if(isset($url['host'])) {
                $host = strtolower($url['host']);
            }
    
    
            // Step 2: Remove Default ports 80 and 443 from URL
            $port = ($scheme === 'http' ? 80 : 443);
            if(isset($url['port'])) {
                $port = $url['port'];
            }
    
            if(($scheme == 'http' && $port == 80) || ($scheme == 'https' && $port == 443)) {
                $port = '';
            } else {
                $port = ':' . $port;
            }
    
    
            // Step 3: Preserve Path
            $path = '/';
            if(isset($url['path'])) {
                $path = $url['path'];
            }
    
    
            // Step 4: Preserve Query String if available and Order the same
            $query = '';
            if(isset($url['query']) && $url['query'] != '') {
    
                parse_str($url['query'], $queryString);
    
                // Sort Param by key
                ksort($queryString);
    
                // Rebuild Query String Param
                $url['query'] = http_build_query($queryString);
    
                $query = '?' . $url['query'];
            }
    
            // Step 5: Preserve Fragment Part if available
            $fragment = '';
            if(isset($url['fragment'])) {
                $fragment = '#'.$url['fragment'];
            }
    
    
            // Reconstruct the normalized URL
            $normalized_url = $scheme . '://' . $host . $port . $path . $query . $fragment;
    
            if(!in_array($normalized_url, $normalizedUrls)) {
                $normalizedUrls[] = $normalized_url;
            }
    
        }
    
        return count($normalizedUrls);
    }

    /**
     * This function counts how many unique normalized valid URLs were passed to the function per top level domain
     *
     * A top level domain is a domain in the form of example.com. Assume all top level domains end in .com
     * subdomain.example.com is not a top level domain.
     *
     * Accepts a list of URLs
     *
     * Example:
     *
     * input: ["https://example.com"]
     * output: ["example.com" => 1]
     *
     * input: ["https://example.com", "https://subdomain.example.com"]
     * output: ["example.com" => 2]
     *
     */
    /* @var $urls : string[] */
    public function countUniqueUrlsPerTopLevelDomain(?array $urls)
    {
        $normalizedUrls = [];
        $uniqueTopLevelDomain = [];
    
        foreach($urls as $url) {
            // Parse the URL into its components
            $url = parse_url($url);
    
            // Invalid URL
            if ($url === false) {
                continue;
            }
    
            // Step 1: Change Scheme and host name to lower case
            $scheme = '';
            if(isset($url['scheme'])) {
                $scheme = strtolower($url['scheme']);
            }
    
            $host = '';
            if(isset($url['host'])) {
                $host = strtolower($url['host']);
            }
    
    
            // Step 2: Remove Default ports 80 and 443 from URL
            $port = ($scheme === 'http' ? 80 : 443);
            if(isset($url['port'])) {
                $port = $url['port'];
            }
    
            if(($scheme == 'http' && $port == 80) || ($scheme == 'https' && $port == 443)) {
                $port = '';
            } else {
                $port = ':' . $port;
            }
    
    
            // Step 3: Preserve Path
            $path = '/';
            if(isset($url['path'])) {
                $path = $url['path'];
            }
    
    
            // Step 4: Preserve Query String if available and Order the same
            $query = '';
            if(isset($url['query']) && $url['query'] != '') {
    
                parse_str($url['query'], $queryString);
    
                // Sort Param by key
                ksort($queryString);
    
                // Rebuild Query String Param
                $url['query'] = http_build_query($queryString);
    
                $query = '?' . $url['query'];
            }
    
            // Step 5: Preserve Fragment Part if available
            $fragment = '';
            if(isset($url['fragment'])) {
                $fragment = '#'.$url['fragment'];
            }
    
    
            // Reconstruct the normalized URL
            $normalized_url = $scheme . '://' . $host . $port . $path . $query . $fragment;
    
            if(!in_array($normalized_url, $normalizedUrls)) {
                $normalizedUrls[] = $normalized_url;
    
                $hostParts = explode('.', $host);
    
                if(count($hostParts) > 2) {
                    $mainDomain = array_slice($hostParts, -2,2);
                    $topLevelDomain = implode('.', $mainDomain);
                } else {
                    $topLevelDomain = $host;
                }
    
                if(!isset($uniqueTopLevelDomain[$topLevelDomain])) {
                    $uniqueTopLevelDomain[$topLevelDomain] = 0;
                }
    
                $uniqueTopLevelDomain[$topLevelDomain] = $uniqueTopLevelDomain[$topLevelDomain] + 1;
    
            }
    
        }
    
        return $uniqueTopLevelDomain;
    }
}