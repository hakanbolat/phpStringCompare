# PHP String Percent Compare

A PHP class for comparing two strings and calculating their similarity percentage.

## Features

- Calculates similarity percentage between two strings
- Multiple text preprocessing options:
  - Remove HTML tags
  - Remove punctuation
  - Remove extra spaces
  - Remove unnecessary words
  - Remove non-alphanumeric characters
  - Convert language (transliteration)
  - Word substitution

## Requirements

- PHP 7.4 or higher

## Installation

Clone the repository:

```bash
git clone https://github.com/yourusername/phpStringCompare.git
```

## Usage

```php
<?php

require_once 'path/to/StringPercentCompare.php';

use TextComparison\StringPercentCompare;

// Basic usage
$str1 = "This is a sample text";
$str2 = "This is sample text";
$comparator = new StringPercentCompare($str1, $str2);
$similarity = $comparator->getSimilarityPercentage();
echo "Similarity: {$similarity}%\n";

// Advanced usage with options
$options = [
    'remove_html_tags' => true,
    'remove_extra_spaces' => true,
    'remove_punctuation' => true,
    'debug' => false,
    // Additional options:
    // 'punctuation_symbols' => ['!', '?', '.'],
    // 'unnecessary_words' => ['the', 'a', 'an'],
    // 'convert_language' => true,
    // 'non_alphanumeric' => true,
    // 'convert_word' => true,
];

$str1 = "<p>This is a sample text with HTML!</p>";
$str2 = "This is sample text with html.";
$comparator = new StringPercentCompare($str1, $str2, $options);
$similarity = $comparator->getSimilarityPercentage();
echo "Similarity: {$similarity}%\n";
```

## Configuration Options

| Option | Type | Description |
|--------|------|-------------|
| `debug` | boolean | Enable debug output |
| `remove_html_tags` | boolean | Remove HTML tags from strings |
| `remove_extra_spaces` | boolean | Replace multiple spaces with a single space |
| `remove_punctuation` | boolean | Remove punctuation symbols |
| `punctuation_symbols` | array | Custom array of punctuation symbols to remove |
| `unnecessary_words` | array | Words to remove from comparison |
| `convert_language` | boolean | Transliterate non-ASCII characters |
| `non_alphanumeric` | boolean | Remove non-alphanumeric characters |
| `convert_word` | boolean | Apply word substitutions |

## How It Works

The class compares two strings by:

1. Preprocessing both strings based on the provided options
2. Counting words in each string
3. Building a regex pattern from the second string's words
4. Finding matches in the first string
5. Calculating a similarity percentage based on the number of matches

## License

MIT License
