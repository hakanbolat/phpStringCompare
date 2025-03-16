<?php

require_once 'StringPercentCompare.php';

use TextComparison\StringPercentCompare;

// Example 1: Basic comparison
$str1 = "This is a sample text";
$str2 = "This is sample text";
$comparator = new StringPercentCompare($str1, $str2);
$similarity = $comparator->getSimilarityPercentage();
echo "Example 1 - Basic comparison:\n";
echo "String 1: \"{$str1}\"\n";
echo "String 2: \"{$str2}\"\n";
echo "Similarity: {$similarity}%\n\n";

// Example 2: HTML content comparison
$str1 = "<p>This is a sample text with <b>HTML</b> tags!</p>";
$str2 = "This is sample text with html tags.";
$options = [
    'remove_html_tags' => true,
    'remove_punctuation' => true,
];
$comparator = new StringPercentCompare($str1, $str2, $options);
$similarity = $comparator->getSimilarityPercentage();
echo "Example 2 - HTML content comparison:\n";
echo "String 1: \"{$str1}\"\n";
echo "String 2: \"{$str2}\"\n";
echo "Similarity: {$similarity}%\n\n";

// Example 3: Technical product descriptions
$str1 = "Asus ROG GL553VD-DM066 i7-7700HQ 2.80GHz 8GB 128GB SSD+1TB 15.6\" FHD 4GB GTX 1050 FreeDOS Gaming Notebook";
$str2 = "ASUS GL553VD-DM065T i7-7700HQ/ 8 GB DDR4/1TB 5400RPM-128G M.2 SSD/4 GB NVIDIA GeForce GTX 1050/W10/GAMING NOTEBOOK";
$options = [
    'remove_html_tags' => true,
    'remove_extra_spaces' => true,
    'remove_punctuation' => true,
    'convert_language' => true,
    'non_alphanumeric' => true,
    'unnecessary_words' => true,
    'convert_word' => true,
    'debug' => false
];
$comparator = new StringPercentCompare($str1, $str2, $options);
$similarity = $comparator->getSimilarityPercentage();
echo "Example 3 - Technical product descriptions:\n";
echo "String 1: \"{$str1}\"\n";
echo "String 2: \"{$str2}\"\n";
echo "Similarity: {$similarity}%\n\n";

// Example 4: Different languages
$str1 = "Merhaba dünya, bu bir test cümlesidir!";
$str2 = "Merhaba dunya, bu test cumlesidir.";
$options = [
    'convert_language' => true,
    'remove_punctuation' => true,
];
$comparator = new StringPercentCompare($str1, $str2, $options);
$similarity = $comparator->getSimilarityPercentage();
echo "Example 4 - Different languages:\n";
echo "String 1: \"{$str1}\"\n";
echo "String 2: \"{$str2}\"\n";
echo "Similarity: {$similarity}%\n\n";

// Example 5: With debug output
$str1 = "Gold iPhone 12 Pro Max 128GB";
$str2 = "iPhone 12 Pro Max Altın 128GB";
$options = [
    'convert_word' => true,
    'debug' => true
];
$comparator = new StringPercentCompare($str1, $str2, $options);
$similarity = $comparator->getSimilarityPercentage();
echo "Example 5 - With debug output:\n";
echo "String 1: \"{$str1}\"\n";
echo "String 2: \"{$str2}\"\n";
echo "Similarity: {$similarity}%\n"; 