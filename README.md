# phpStringCompare
Compares two strings and outputs the similarities  as percentage with PHP

Usage:

```php
  require_once 'StringPercentCompare.php';
  $string1 = 'Asus ROG GL553VD-DM066 i7-7700HQ 2.80GHz 8GB 128GB SSD+1TB 15.6" FHD 4GB GTX 1050 FreeDOS Gaming Notebook';
  $string2 = 'ASUS GL553VD-DM065T i7-7700HQ/ 8 GB DDR4/1TB 5400RPM-128G M.2 SSD/4 GB NVIDIA GeForce GTX 1050/W10/GAMING NOTEBOOK';
  $percent = new StringPercentCompare(
        $string1,
        $string2,
        array('remove_html_tags'=>true, 'remove_extra_spaces'=>true,
            'remove_punctuation'=>true, 'punctuation_symbols'=>Array('(',')'),
            'convert_language'=>true, 'non_alphanumeric'=>true,
            'unnecessary_words'=>true, 'convert_word'=>true,'debug'=>true
        ));
  echo $percent->getSimilarityPercentage();
```
