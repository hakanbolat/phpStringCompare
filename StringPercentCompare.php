<?php

class StringPercentCompare
{
    private $_string1 = '';
    private $_string2 = '';
    private $_words1_count;
    private $_words2_count;
    private $_percent = null;
    private $_debug = false;

    private $_remove_extra_spaces = false;
    private $_remove_punctuation = false;
    private $_remove_html_tags = false;
    private $_remove_unnecessary = false;
    private $_remove_non_alphanumeric = false;
    private $_convert_language = false;
    private $_convert_word = false;

    private $_punctuation_symbols = array('.', ',', '/', '-', '$', '*', ':', ';', '!', '?', '|', '\\', '_', '<', '>', '#', '~', '"', '\'', '^', '(', ')', '=', '+');
    private $_unnecessary_words = array('akilli telefon', 'tasinabilir bilgisayar', 'notebook', 'cep telefonu');
    private $_non_alphanumeric_reg = '~[^a-zA-Z0-9.]~';

    private $_convert_word1 = array('rose gold', 'gold', 'silver', 'space grey', 'space gray', 'jet black', 'jetblack', 'mate black', 'black', 'uzay grisi', 'ultra hd', 'full hd', 'wi-fi', '"', '4 gb', '8 gb', '16 gb', '32 gb', '64 gb', '128 gb', '256 gb', 'gaming');
    private $_convert_word2 = array('roze altın', 'altin', 'gumus', 'uzay gri', 'uzay gri', 'simsiyah', 'simsiyah', 'matsiyah', 'siyah', 'uzay gri', 'uhd', 'fhd', 'wifi', 'inc', '4gb', '8gb', '16gb', '32gb', '64gb', '128gb', '256gb', 'oyuncu');

    public function __construct($str1, $str2, $params = array())
    {
        $this->setParameters($params);
        $this->initializeStrings($str1, $str2);

        if ($this->_debug) {
            $this->printDebug($this->_string1);
            $this->printDebug($this->_string2);
        }
    }

    private function setParameters($params)
    {
        $this->_debug = !empty($params['debug']);
        $this->_remove_html_tags = !empty($params['remove_html_tags']);
        $this->_remove_extra_spaces = !empty($params['remove_extra_spaces']);
        $this->_remove_punctuation = !empty($params['remove_punctuation']);
        $this->_punctuation_symbols = !empty($params['punctuation_symbols']) ? $params['punctuation_symbols'] : $this->_punctuation_symbols;
        $this->_remove_unnecessary = !empty($params['unnecessary_words']) ? $params['unnecessary_words'] : $this->_remove_unnecessary;
        $this->_convert_language = !empty($params['convert_language']);
        $this->_remove_non_alphanumeric = !empty($params['non_alphanumeric']);
        $this->_convert_word = !empty($params['convert_word']);
    }

    private function initializeStrings($str1, $str2)
    {
        $str1 = strtolower($str1);
        $str2 = strtolower($str2);

        if ($this->_remove_html_tags) {
            $str1 = strip_tags($str1);
            $str2 = strip_tags($str2);
        }
        if ($this->_remove_punctuation && count($this->_punctuation_symbols)) {
            $str1 = str_replace($this->_punctuation_symbols, '', $str1);
            $str2 = str_replace($this->_punctuation_symbols, '', $str2);
        }
        if ($this->_remove_unnecessary && count($this->_unnecessary_words)) {
            $str1 = str_replace($this->_unnecessary_words, '', $str1);
            $str2 = str_replace($this->_unnecessary_words, '', $str2);
        }
        if ($this->_convert_language) {
            $str1 = iconv('utf-8', 'ascii//TRANSLIT', $str1);
            $str2 = iconv('utf-8', 'ascii//TRANSLIT', $str2);
        }
        if ($this->_convert_word) {
            $str1 = str_replace($this->_convert_word1, $this->_convert_word2, $str1);
            $str2 = str_replace($this->_convert_word1, $this->_convert_word2, $str2);
        }
        if ($this->_remove_non_alphanumeric) {
            $str1 = preg_replace($this->_non_alphanumeric_reg, ' ', $str1);
            $str2 = preg_replace($this->_non_alphanumeric_reg, ' ', $str2);
        }
        if ($this->_remove_extra_spaces) {
            $str1 = preg_replace('#\s+#u', ' ', $str1);
            $str2 = preg_replace('#\s+#u', ' ', $str2);
        }

        $this->_string1 = trim($str1);
        $this->_string2 = trim($str2);

        $this->_words1_count = $this->getWordCount($str1);
        $this->_words2_count = $this->getWordCount($str2);
    }

    private function getWordCount($str)
    {
        return count(array_values(array_filter(explode(' ', $str), function ($value) {
            return $value !== '';
        })));
    }

    public function process()
    {
        if (!is_null($this->_percent)) {
            return false;
        }

        $str1 = $this->_string1;
        $str2 = $this->_string2;
        $str2 = explode(' ', $str2);
        array_multisort(array_map('strlen', $str2), $str2);
        $str2 = array_reverse($str2);
        $str2 = array_values(array_filter($str2, function ($value) {
            return $value !== '';
        }));

        // Regex to try match
        $regex = '~(\\b';

        // Add each word to regex
        for ($i = 0; $i < $this->_words2_count; $i++) {
            $regex .= $str2[$i] . ' ' . ($i != ($this->_words2_count - 1) ? '|\\b' : '');
            //$regex .= $str2[$i] . ($i != ($this->_words2_count - 1) ? '|' : '');
        }

        // Finish regex, case insensitive
        $regex .= ')~i';
        //birinci stringin son kelimesi regex içerisinde varsa boşluk karakterini sil
        if (is_numeric(strpos($regex, ('|' . substr($str1, (strrpos($str1, ' ', -1) + 1)))))) {
            $searchString = '|' . substr($str1, (strrpos($str1, ' ', -1) + 1)) . ' ';
            $replaceString = '|' . substr($str1, (strrpos($str1, ' ', -1) + 1));
            $regex = str_replace($searchString, $replaceString, $regex);
        }
        if ($this->_debug)
            $this->printDebug($regex);
        $wordsFound = preg_replace($regex, '- ', $str1 . ' ');
        if ($this->_debug)
            $this->printDebug($wordsFound);
        $wordsFound = preg_replace('[- ]', '*', $wordsFound);
        if ($this->_debug)
            $this->printDebug($wordsFound);
        $wordsFound = preg_replace('~[^*]~', '', $wordsFound);
        if ($this->_debug)
            $this->printDebug($wordsFound);
        $wordsFoundCount = strlen($wordsFound);

        $percent = ($wordsFoundCount) / ($this->_words1_count) * 100;
        if ($this->_words1_count != $this->_words2_count && (int) $percent == 100) {
            $percent = $percent - 5;
        }

        $this->_percent = number_format($percent, 2, '.', '');
        return $this;
    }

    public function getSimilarityPercentage()
    {
        $this->process();
        return (float) $this->_percent;
    }

    public function printDebug($data)
    {
        if (is_array($data) || is_object($data)) {
            echo json_encode($data) . PHP_EOL;
        } else {
            echo $data . PHP_EOL;
        }
    }
}

?>
