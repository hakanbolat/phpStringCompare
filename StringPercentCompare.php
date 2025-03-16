<?php

namespace TextComparison;

/**
 * StringPercentCompare - A class to compare two strings and calculate similarity percentage
 */
class StringPercentCompare
{
    private string $string1 = '';
    private string $string2 = '';
    private int $wordsCount1;
    private int $wordsCount2;
    private ?float $percent = null;
    private bool $debug = false;

    // Text processing options
    private bool $removeExtraSpaces = false;
    private bool $removePunctuation = false;
    private bool $removeHtmlTags = false;
    private bool $removeUnnecessary = false;
    private bool $removeNonAlphanumeric = false;
    private bool $convertLanguage = false;
    private bool $convertWord = false;

    // Configuration arrays
    private array $punctuationSymbols = ['.', ',', '/', '-', '$', '*', ':', ';', '!', '?', '|', '\\', '_', '<', '>', '#', '~', '"', '\'', '^', '(', ')', '=', '+'];
    private array $unnecessaryWords = ['akilli telefon', 'tasinabilir bilgisayar', 'notebook', 'cep telefonu'];
    private string $nonAlphanumericRegex = '~[^a-zA-Z0-9.]~';

    private array $convertWordFrom = ['rose gold', 'gold', 'silver', 'space grey', 'space gray', 'jet black', 'jetblack', 'mate black', 'black', 'uzay grisi', 'ultra hd', 'full hd', 'wi-fi', '"', '4 gb', '8 gb', '16 gb', '32 gb', '64 gb', '128 gb', '256 gb', 'gaming'];
    private array $convertWordTo = ['roze altın', 'altin', 'gumus', 'uzay gri', 'uzay gri', 'simsiyah', 'simsiyah', 'matsiyah', 'siyah', 'uzay gri', 'uhd', 'fhd', 'wifi', 'inc', '4gb', '8gb', '16gb', '32gb', '64gb', '128gb', '256gb', 'oyuncu'];

    /**
     * Constructor
     *
     * @param string $str1 First string to compare
     * @param string $str2 Second string to compare
     * @param array $params Configuration parameters
     */
    public function __construct(string $str1, string $str2, array $params = [])
    {
        $this->setParameters($params);
        $this->initializeStrings($str1, $str2);

        if ($this->debug) {
            $this->printDebug($this->string1);
            $this->printDebug($this->string2);
        }
    }

    /**
     * Set configuration parameters
     *
     * @param array $params Configuration parameters
     * @return void
     */
    private function setParameters(array $params): void
    {
        $this->debug = !empty($params['debug']);
        $this->removeHtmlTags = !empty($params['remove_html_tags']);
        $this->removeExtraSpaces = !empty($params['remove_extra_spaces']);
        $this->removePunctuation = !empty($params['remove_punctuation']);
        
        if (!empty($params['punctuation_symbols'])) {
            $this->punctuationSymbols = $params['punctuation_symbols'];
        }
        
        if (!empty($params['unnecessary_words'])) {
            $this->removeUnnecessary = true;
            $this->unnecessaryWords = $params['unnecessary_words'];
        } else {
            $this->removeUnnecessary = !empty($params['remove_unnecessary']);
        }
        
        $this->convertLanguage = !empty($params['convert_language']);
        $this->removeNonAlphanumeric = !empty($params['non_alphanumeric']);
        $this->convertWord = !empty($params['convert_word']);
    }

    /**
     * Initialize and preprocess the input strings
     *
     * @param string $str1 First string
     * @param string $str2 Second string
     * @return void
     */
    private function initializeStrings(string $str1, string $str2): void
    {
        $str1 = strtolower($str1);
        $str2 = strtolower($str2);

        if ($this->removeHtmlTags) {
            $str1 = strip_tags($str1);
            $str2 = strip_tags($str2);
        }
        
        if ($this->removePunctuation && !empty($this->punctuationSymbols)) {
            $str1 = str_replace($this->punctuationSymbols, '', $str1);
            $str2 = str_replace($this->punctuationSymbols, '', $str2);
        }
        
        if ($this->removeUnnecessary && !empty($this->unnecessaryWords)) {
            $str1 = str_replace($this->unnecessaryWords, '', $str1);
            $str2 = str_replace($this->unnecessaryWords, '', $str2);
        }
        
        if ($this->convertLanguage) {
            $str1 = iconv('utf-8', 'ascii//TRANSLIT', $str1);
            $str2 = iconv('utf-8', 'ascii//TRANSLIT', $str2);
        }
        
        if ($this->convertWord) {
            $str1 = str_replace($this->convertWordFrom, $this->convertWordTo, $str1);
            $str2 = str_replace($this->convertWordFrom, $this->convertWordTo, $str2);
        }
        
        if ($this->removeNonAlphanumeric) {
            $str1 = preg_replace($this->nonAlphanumericRegex, ' ', $str1);
            $str2 = preg_replace($this->nonAlphanumericRegex, ' ', $str2);
        }
        
        if ($this->removeExtraSpaces) {
            $str1 = preg_replace('#\s+#u', ' ', $str1);
            $str2 = preg_replace('#\s+#u', ' ', $str2);
        }

        $this->string1 = trim($str1);
        $this->string2 = trim($str2);

        $this->wordsCount1 = $this->getWordCount($str1);
        $this->wordsCount2 = $this->getWordCount($str2);
    }

    /**
     * Count words in a string
     *
     * @param string $str Input string
     * @return int Number of words
     */
    private function getWordCount(string $str): int
    {
        return count(array_filter(explode(' ', $str), function ($value) {
            return $value !== '';
        }));
    }

    /**
     * Process the comparison between strings
     *
     * @return $this|bool Returns $this on success, false if already processed
     */
    public function process()
    {
        if ($this->percent !== null) {
            return false;
        }

        $str1 = $this->string1;
        $str2Words = explode(' ', $this->string2);
        
        // Sort words by length (longest first)
        array_multisort(array_map('strlen', $str2Words), SORT_DESC, $str2Words);
        
        // Filter out empty values
        $str2Words = array_values(array_filter($str2Words, function ($value) {
            return $value !== '';
        }));

        // Build regex pattern
        $regex = $this->buildRegexPattern($str1, $str2Words);
        
        if ($this->debug) {
            $this->printDebug($regex);
        }
        
        // Find matching words
        $wordsFound = $this->findMatchingWords($str1, $regex);
        $wordsFoundCount = strlen($wordsFound);

        // Calculate percentage
        $percent = ($wordsFoundCount) / ($this->wordsCount1) * 100;
        
        // Adjust percentage if word counts differ but match is 100%
        if ($this->wordsCount1 != $this->wordsCount2 && (int) $percent == 100) {
            $percent -= 5;
        }

        $this->percent = (float) number_format($percent, 2, '.', '');
        return $this;
    }

    /**
     * Build regex pattern for word matching
     *
     * @param string $str1 First string
     * @param array $str2Words Words from second string
     * @return string Regex pattern
     */
    private function buildRegexPattern(string $str1, array $str2Words): string
    {
        // Start regex pattern
        $regex = '~(\\b';

        // Add each word to regex
        for ($i = 0; $i < $this->wordsCount2; $i++) {
            $regex .= $str2Words[$i] . ' ' . ($i != ($this->wordsCount2 - 1) ? '|\\b' : '');
        }

        // Finish regex, case insensitive
        $regex .= ')~i';

        // Handle special case for last word in first string
        $lastWord = substr($str1, (strrpos($str1, ' ', -1) + 1));
        if (strpos($regex, ('|' . $lastWord . ' ')) !== false) {
            $searchString = '|' . $lastWord . ' ';
            $replaceString = '|' . $lastWord;
            $regex = str_replace($searchString, $replaceString, $regex);
        }

        return $regex;
    }

    /**
     * Find matching words using regex
     *
     * @param string $str1 First string
     * @param string $regex Regex pattern
     * @return string String with only matching characters
     */
    private function findMatchingWords(string $str1, string $regex): string
    {
        // Replace matched words with placeholder
        $wordsFound = preg_replace($regex, '- ', $str1 . ' ');
        
        if ($this->debug) {
            $this->printDebug($wordsFound);
        }
        
        // Convert placeholders to asterisks
        $wordsFound = preg_replace('[- ]', '*', $wordsFound);
        
        if ($this->debug) {
            $this->printDebug($wordsFound);
        }
        
        // Keep only asterisks
        $wordsFound = preg_replace('~[^*]~', '', $wordsFound);
        
        if ($this->debug) {
            $this->printDebug($wordsFound);
        }
        
        return $wordsFound;
    }

    /**
     * Get the similarity percentage between strings
     *
     * @return float Similarity percentage
     */
    public function getSimilarityPercentage(): float
    {
        $this->process();
        return $this->percent;
    }

    /**
     * Print debug information
     *
     * @param mixed $data Data to print
     * @return void
     */
    public function printDebug($data): void
    {
        if (is_array($data) || is_object($data)) {
            echo json_encode($data) . PHP_EOL;
        } else {
            echo $data . PHP_EOL;
        }
    }
}

?>
