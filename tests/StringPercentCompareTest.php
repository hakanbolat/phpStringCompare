<?php

namespace TextComparison\Tests;

use PHPUnit\Framework\TestCase;
use TextComparison\StringPercentCompare;

class StringPercentCompareTest extends TestCase
{
    public function testBasicComparison()
    {
        $str1 = "This is a sample text";
        $str2 = "This is sample text";
        $comparator = new StringPercentCompare($str1, $str2);
        $similarity = $comparator->getSimilarityPercentage();
        
        $this->assertGreaterThan(75, $similarity);
        $this->assertLessThan(100, $similarity);
    }
    
    public function testIdenticalStrings()
    {
        $str = "This is an identical string";
        $comparator = new StringPercentCompare($str, $str);
        $similarity = $comparator->getSimilarityPercentage();
        
        $this->assertEquals(100, $similarity);
    }
    
    public function testHtmlRemoval()
    {
        $str1 = "<p>This is a sample text with <b>HTML</b> tags!</p>";
        $str2 = "This is sample text with html tags.";
        $options = [
            'remove_html_tags' => true,
            'remove_punctuation' => true,
        ];
        $comparator = new StringPercentCompare($str1, $str2, $options);
        $similarity = $comparator->getSimilarityPercentage();
        
        $this->assertGreaterThan(80, $similarity);
    }
    
    public function testCompletelyDifferentStrings()
    {
        $str1 = "This is the first string with unique words";
        $str2 = "Completely different text with no common elements";
        $comparator = new StringPercentCompare($str1, $str2);
        $similarity = $comparator->getSimilarityPercentage();
        
        $this->assertLessThan(30, $similarity);
    }
    
    public function testTechnicalProductDescriptions()
    {
        $str1 = "Asus ROG GL553VD-DM066 i7-7700HQ 2.80GHz 8GB 128GB SSD+1TB 15.6\" FHD 4GB GTX 1050 FreeDOS Gaming Notebook";
        $str2 = "ASUS GL553VD-DM065T i7-7700HQ/ 8 GB DDR4/1TB 5400RPM-128G M.2 SSD/4 GB NVIDIA GeForce GTX 1050/W10/GAMING NOTEBOOK";
        $options = [
            'remove_html_tags' => true,
            'remove_extra_spaces' => true,
            'remove_punctuation' => true,
            'convert_language' => true,
            'non_alphanumeric' => true,
            'unnecessary_words' => true,
            'convert_word' => true
        ];
        $comparator = new StringPercentCompare($str1, $str2, $options);
        $similarity = $comparator->getSimilarityPercentage();
        
        $this->assertGreaterThan(50, $similarity);
    }
    
    public function testWordConversion()
    {
        $str1 = "Gold iPhone 12 Pro Max 128GB";
        $str2 = "iPhone 12 Pro Max Altın 128GB";
        $options = [
            'convert_word' => true
        ];
        $comparator = new StringPercentCompare($str1, $str2, $options);
        $similarity = $comparator->getSimilarityPercentage();
        
        $this->assertGreaterThan(80, $similarity);
    }
} 