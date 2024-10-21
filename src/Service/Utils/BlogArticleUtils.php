<?php 

namespace App\Service\Utils;


class BlogArticleUtils 
{
    public function getTopRepeatedWords($textContent = "", $bannedWords = []) {
        // Normalize text to lowercase and split into words
        $textToSplittedArray = preg_split('/\s+/', strtolower($textContent));
        $bannedWordsLowerCase = array_map('strtolower', $bannedWords);
    
        // Return empty array if no words are found
        if (count($textToSplittedArray) === 0) {
            return [];
        }
    
        // Initialize an associative array to count word occurrences
        $mappingKeywordsDict = [];
    
        foreach ($textToSplittedArray as $currentElem) {
            
            // Deleting last elemenet with rtrim method if it finishes with a dot
            $rtrimString = $currentElem;
            if (substr($rtrimString, -1) === '.') {
                $rtrimString = rtrim($rtrimString, '.');
            }
            
            // Skip banned words
            if (!in_array($rtrimString, $bannedWordsLowerCase)) {
                // Count occurrences
                if (isset($mappingKeywordsDict[$rtrimString])) {
                    $mappingKeywordsDict[$rtrimString] += 1;
                } else {
                    $mappingKeywordsDict[$rtrimString] = 1;
                }
            }
        }
    
        // Convert associative array to a numeric array of [word, count] pairs
        $keywordsArray = [];
        foreach ($mappingKeywordsDict as $word => $count) {
            $keywordsArray[] = [$word, $count];
        }
    
        // Sort the array by count in descending order
        usort($keywordsArray, function ($a, $b) {
            return $b[1] <=> $a[1]; // Sort by count (second element)
        });
    
        // Get the top three repeated words
        $topThreeWords = array_map(function ($item) {
            return $item[0];
        }, array_slice($keywordsArray, 0, 3));
    
        return $topThreeWords;
    }
}