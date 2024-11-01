<?php
//Controllador de Errores
class VCardMediaException extends \RuntimeException {
    
}

/**
 * Clase que retoca el texto/caracteres
 */
abstract class Transliterator {

    /**
     * Checks whether a string has utf7 characters in it.
     *
     * By bmorel at ssi dot fr
     *
     * @param string $string
     *
     * @return bool
     */
    public static function seemsUtf8($string) {
        $stringLength = strlen($string);
        for ($i = 0; $i < $stringLength; ++$i) {
            if (ord($string[$i]) < 0x80) { // 0bbbbbbb
                continue;
            } elseif ((ord($string[$i]) & 0xE0) == 0xC0) { // 110bbbbb
                $n = 1;
            } elseif ((ord($string[$i]) & 0xF0) == 0xE0) { //1110bbbb
                $n = 2;
            } elseif ((ord($string[$i]) & 0xF8) == 0xF0) { // 11110bbb
                $n = 3;
            } elseif ((ord($string[$i]) & 0xFC) == 0xF8) { // 111110bb
                $n = 4;
            } elseif ((ord($string[$i]) & 0xFE) == 0xFC) { // 1111110b
                $n = 5;
            } else {
                return false; // Does not match any model
            }
            for ($j = 0; $j < $n; ++$j) { // n bytes matching 10bbbbbb follow ?
                if (++$i === $stringLength || ((ord($string[$i]) & 0xC0) !== 0x80)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Replaces accentuated chars (and a few others) with their ASCII base char.
     *
     * @see Transliterator::utf8ToAscii for a full transliteration to ASCII
     *
     * @param string $string String to unaccent
     *
     * @return string Unaccented string
     */
    public static function unaccent($string) {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }
        if (self::seemsUtf8($string)) {
            $chars = array(
                // Decompositions for Latin-1 Supplement
                chr(195) . chr(128) => 'A',
                chr(195) . chr(129) => 'A',
                chr(195) . chr(130) => 'A',
                chr(195) . chr(131) => 'A',
                chr(195) . chr(132) => 'A',
                chr(195) . chr(133) => 'A',
                chr(195) . chr(135) => 'C',
                chr(195) . chr(136) => 'E',
                chr(195) . chr(137) => 'E',
                chr(195) . chr(138) => 'E',
                chr(195) . chr(139) => 'E',
                chr(195) . chr(140) => 'I',
                chr(195) . chr(141) => 'I',
                chr(195) . chr(142) => 'I',
                chr(195) . chr(143) => 'I',
                chr(195) . chr(145) => 'N',
                chr(195) . chr(146) => 'O',
                chr(195) . chr(147) => 'O',
                chr(195) . chr(148) => 'O',
                chr(195) . chr(149) => 'O',
                chr(195) . chr(150) => 'O',
                chr(195) . chr(153) => 'U',
                chr(195) . chr(154) => 'U',
                chr(195) . chr(155) => 'U',
                chr(195) . chr(156) => 'U',
                chr(195) . chr(157) => 'Y',
                chr(195) . chr(159) => 's',
                chr(195) . chr(160) => 'a',
                chr(195) . chr(161) => 'a',
                chr(195) . chr(162) => 'a',
                chr(195) . chr(163) => 'a',
                chr(195) . chr(164) => 'a',
                chr(195) . chr(165) => 'a',
                chr(195) . chr(167) => 'c',
                chr(195) . chr(168) => 'e',
                chr(195) . chr(169) => 'e',
                chr(195) . chr(170) => 'e',
                chr(195) . chr(171) => 'e',
                chr(195) . chr(172) => 'i',
                chr(195) . chr(173) => 'i',
                chr(195) . chr(174) => 'i',
                chr(195) . chr(175) => 'i',
                chr(195) . chr(177) => 'n',
                chr(195) . chr(178) => 'o',
                chr(195) . chr(179) => 'o',
                chr(195) . chr(180) => 'o',
                chr(195) . chr(181) => 'o',
                chr(195) . chr(182) => 'o',
                chr(195) . chr(182) => 'o',
                chr(195) . chr(185) => 'u',
                chr(195) . chr(186) => 'u',
                chr(195) . chr(187) => 'u',
                chr(195) . chr(188) => 'u',
                chr(195) . chr(189) => 'y',
                chr(195) . chr(191) => 'y',
                // Decompositions for Latin Extended-A
                chr(196) . chr(128) => 'A',
                chr(196) . chr(129) => 'a',
                chr(196) . chr(130) => 'A',
                chr(196) . chr(131) => 'a',
                chr(196) . chr(132) => 'A',
                chr(196) . chr(133) => 'a',
                chr(196) . chr(134) => 'C',
                chr(196) . chr(135) => 'c',
                chr(196) . chr(136) => 'C',
                chr(196) . chr(137) => 'c',
                chr(196) . chr(138) => 'C',
                chr(196) . chr(139) => 'c',
                chr(196) . chr(140) => 'C',
                chr(196) . chr(141) => 'c',
                chr(196) . chr(142) => 'D',
                chr(196) . chr(143) => 'd',
                chr(196) . chr(144) => 'D',
                chr(196) . chr(145) => 'd',
                chr(196) . chr(146) => 'E',
                chr(196) . chr(147) => 'e',
                chr(196) . chr(148) => 'E',
                chr(196) . chr(149) => 'e',
                chr(196) . chr(150) => 'E',
                chr(196) . chr(151) => 'e',
                chr(196) . chr(152) => 'E',
                chr(196) . chr(153) => 'e',
                chr(196) . chr(154) => 'E',
                chr(196) . chr(155) => 'e',
                chr(196) . chr(156) => 'G',
                chr(196) . chr(157) => 'g',
                chr(196) . chr(158) => 'G',
                chr(196) . chr(159) => 'g',
                chr(196) . chr(160) => 'G',
                chr(196) . chr(161) => 'g',
                chr(196) . chr(162) => 'G',
                chr(196) . chr(163) => 'g',
                chr(196) . chr(164) => 'H',
                chr(196) . chr(165) => 'h',
                chr(196) . chr(166) => 'H',
                chr(196) . chr(167) => 'h',
                chr(196) . chr(168) => 'I',
                chr(196) . chr(169) => 'i',
                chr(196) . chr(170) => 'I',
                chr(196) . chr(171) => 'i',
                chr(196) . chr(172) => 'I',
                chr(196) . chr(173) => 'i',
                chr(196) . chr(174) => 'I',
                chr(196) . chr(175) => 'i',
                chr(196) . chr(176) => 'I',
                chr(196) . chr(177) => 'i',
                chr(196) . chr(178) => 'IJ',
                chr(196) . chr(179) => 'ij',
                chr(196) . chr(180) => 'J',
                chr(196) . chr(181) => 'j',
                chr(196) . chr(182) => 'K',
                chr(196) . chr(183) => 'k',
                chr(196) . chr(184) => 'k',
                chr(196) . chr(185) => 'L',
                chr(196) . chr(186) => 'l',
                chr(196) . chr(187) => 'L',
                chr(196) . chr(188) => 'l',
                chr(196) . chr(189) => 'L',
                chr(196) . chr(190) => 'l',
                chr(196) . chr(191) => 'L',
                chr(197) . chr(128) => 'l',
                chr(197) . chr(129) => 'L',
                chr(197) . chr(130) => 'l',
                chr(197) . chr(131) => 'N',
                chr(197) . chr(132) => 'n',
                chr(197) . chr(133) => 'N',
                chr(197) . chr(134) => 'n',
                chr(197) . chr(135) => 'N',
                chr(197) . chr(136) => 'n',
                chr(197) . chr(137) => 'N',
                chr(197) . chr(138) => 'n',
                chr(197) . chr(139) => 'N',
                chr(197) . chr(140) => 'O',
                chr(197) . chr(141) => 'o',
                chr(197) . chr(142) => 'O',
                chr(197) . chr(143) => 'o',
                chr(197) . chr(144) => 'O',
                chr(197) . chr(145) => 'o',
                chr(197) . chr(146) => 'OE',
                chr(197) . chr(147) => 'oe',
                chr(197) . chr(148) => 'R',
                chr(197) . chr(149) => 'r',
                chr(197) . chr(150) => 'R',
                chr(197) . chr(151) => 'r',
                chr(197) . chr(152) => 'R',
                chr(197) . chr(153) => 'r',
                chr(197) . chr(154) => 'S',
                chr(197) . chr(155) => 's',
                chr(197) . chr(156) => 'S',
                chr(197) . chr(157) => 's',
                chr(197) . chr(158) => 'S',
                chr(197) . chr(159) => 's',
                chr(197) . chr(160) => 'S',
                chr(197) . chr(161) => 's',
                chr(197) . chr(162) => 'T',
                chr(197) . chr(163) => 't',
                chr(197) . chr(164) => 'T',
                chr(197) . chr(165) => 't',
                chr(197) . chr(166) => 'T',
                chr(197) . chr(167) => 't',
                chr(197) . chr(168) => 'U',
                chr(197) . chr(169) => 'u',
                chr(197) . chr(170) => 'U',
                chr(197) . chr(171) => 'u',
                chr(197) . chr(172) => 'U',
                chr(197) . chr(173) => 'u',
                chr(197) . chr(174) => 'U',
                chr(197) . chr(175) => 'u',
                chr(197) . chr(176) => 'U',
                chr(197) . chr(177) => 'u',
                chr(197) . chr(178) => 'U',
                chr(197) . chr(179) => 'u',
                chr(197) . chr(180) => 'W',
                chr(197) . chr(181) => 'w',
                chr(197) . chr(182) => 'Y',
                chr(197) . chr(183) => 'y',
                chr(197) . chr(184) => 'Y',
                chr(197) . chr(185) => 'Z',
                chr(197) . chr(186) => 'z',
                chr(197) . chr(187) => 'Z',
                chr(197) . chr(188) => 'z',
                chr(197) . chr(189) => 'Z',
                chr(197) . chr(190) => 'z',
                chr(197) . chr(191) => 's',
                // Euro Sign
                chr(226) . chr(130) . chr(172) => 'E',
                // GBP (Pound) Sign
                chr(194) . chr(163) => '',
                'Ä' => 'Ae',
                'ä' => 'ae',
                'Ü' => 'Ue',
                'ü' => 'ue',
                'Ö' => 'Oe',
                'ö' => 'oe',
                'ß' => 'ss',
                // Norwegian characters
                'Å' => 'Aa',
                'Æ' => 'Ae',
                'Ø' => 'O',
                'æ' => 'a',
                'ø' => 'o',
                'å' => 'aa',
            );
            $string = strtr($string, $chars);
        } else {
            $chars = array();
            // Assume ISO-8859-1 if not UTF-8
            $chars['in'] = chr(128) . chr(131) . chr(138) . chr(142) . chr(154) . chr(158)
                    . chr(159) . chr(162) . chr(165) . chr(181) . chr(192) . chr(193) . chr(194)
                    . chr(195) . chr(196) . chr(197) . chr(199) . chr(200) . chr(201) . chr(202)
                    . chr(203) . chr(204) . chr(205) . chr(206) . chr(207) . chr(209) . chr(210)
                    . chr(211) . chr(212) . chr(213) . chr(214) . chr(216) . chr(217) . chr(218)
                    . chr(219) . chr(220) . chr(221) . chr(224) . chr(225) . chr(226) . chr(227)
                    . chr(228) . chr(229) . chr(231) . chr(232) . chr(233) . chr(234) . chr(235)
                    . chr(236) . chr(237) . chr(238) . chr(239) . chr(241) . chr(242) . chr(243)
                    . chr(244) . chr(245) . chr(246) . chr(248) . chr(249) . chr(250) . chr(251)
                    . chr(252) . chr(253) . chr(255);
            $chars['out'] = 'EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy';
            $string = strtr($string, $chars['in'], $chars['out']);
            $doubleChars = array();
            $doubleChars['in'] = array(
                chr(140),
                chr(156),
                chr(198),
                chr(208),
                chr(222),
                chr(223),
                chr(230),
                chr(240),
                chr(254),
            );
            $doubleChars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
            $string = str_replace($doubleChars['in'], $doubleChars['out'], $string);
        }
        return $string;
    }

    /**
     * Transliterates an UTF-8 string to ASCII.
     *
     * US-ASCII transliterations of Unicode text
     * Ported Sean M. Burke's Text::Unidecode Perl module (He did all the hard work!)
     * Warning: you should only pass this well formed UTF-8!
     * Be aware it works by making a copy of the input string which it appends transliterated
     * characters to - it uses a PHP output buffer to do this - it means, memory use will increase,
     * requiring up to the same amount again as the input string.
     *
     * @param string $str     UTF-8 string to convert
     * @param string $unknown Character use if character unknown (default to ?)
     *
     * @return string US-ASCII string
     */
    public static function utf8ToAscii($str, $unknown = '?') {
        static $UTF8_TO_ASCII;
        if (strlen($str) == 0) {
            return '';
        }
        preg_match_all('/.{1}|[^\x00]{1,1}$/us', $str, $ar);
        $chars = $ar[0];
        foreach ($chars as $i => $c) {
            if (ord($c{0}) >= 0 && ord($c{0}) <= 127) {
                continue;
            } // ASCII - next please
            if (ord($c{0}) >= 192 && ord($c{0}) <= 223) {
                $ord = (ord($c{0}) - 192) * 64 + (ord($c{1}) - 128);
            }
            if (ord($c{0}) >= 224 && ord($c{0}) <= 239) {
                $ord = (ord($c{0}) - 224) * 4096 + (ord($c{1}) - 128) * 64 + (ord($c{2}) - 128);
            }
            if (ord($c{0}) >= 240 && ord($c{0}) <= 247) {
                $ord = (ord($c{0}) - 240) * 262144 + (ord($c{1}) - 128) * 4096 + (ord($c{2}) - 128) * 64 + (ord($c{3}) - 128);
            }
            if (ord($c{0}) >= 248 && ord($c{0}) <= 251) {
                $ord = (ord($c{0}) - 248) * 16777216 + (ord($c{1}) - 128) * 262144 + (ord($c{2}) - 128) * 4096 + (ord($c{3}) - 128) * 64 + (ord($c{4}) - 128);
            }
            if (ord($c{0}) >= 252 && ord($c{0}) <= 253) {
                $ord = (ord($c{0}) - 252) * 1073741824 + (ord($c{1}) - 128) * 16777216 + (ord($c{2}) - 128) * 262144 + (ord($c{3}) - 128) * 4096 + (ord($c{4}) - 128) * 64 + (ord($c{5}) - 128);
            }
            if (ord($c{0}) >= 254 && ord($c{0}) <= 255) {
                $chars{$i} = $unknown;
                continue;
            } //error
            $bank = $ord >> 8;
            if (!array_key_exists($bank, (array) $UTF8_TO_ASCII)) {
                $bankfile = __DIR__ . '/data/' . sprintf('x%02x', $bank) . '.php';
                if (file_exists($bankfile)) {
                    include $bankfile;
                } else {
                    $UTF8_TO_ASCII[$bank] = array();
                }
            }
            $newchar = $ord & 255;
            if (array_key_exists($newchar, $UTF8_TO_ASCII[$bank])) {
                $chars{$i} = $UTF8_TO_ASCII[$bank][$newchar];
            } else {
                $chars{$i} = $unknown;
            }
        }
        return implode('', $chars);
    }

    /**
     * Generates a slug of the text.
     *
     * Does not transliterate correctly eastern languages.
     *
     * @see Transliterator::unaccent for the transliteration logic
     *
     * @param string $text
     * @param string $separator
     *
     * @return string
     */
    public static function urlize($text, $separator = '-') {
        $text = self::unaccent($text);
        return self::postProcessText($text, $separator);
    }

    /**
     * Generates a slug of the text after transliterating the UTF-8 string to ASCII.
     *
     * Uses transliteration tables to convert any kind of utf8 character.
     *
     * @param string $text
     * @param string $separator
     *
     * @return string $text
     */
    public static function transliterate($text, $separator = '-') {
        if (preg_match('/[\x80-\xff]/', $text) && self::validUtf8($text)) {
            $text = self::utf8ToAscii($text);
        }
        return self::postProcessText($text, $separator);
    }

    /**
     * Tests a string as to whether it's valid UTF-8 and supported by the
     * Unicode standard.
     *
     * Note: this function has been modified to simple return true or false
     *
     *
     * @param string $str UTF-8 encoded string
     *
     * @return bool
     *
     */
    public static function validUtf8($str) {
        $mState = 0; // cached expected number of octets after the current octet
        // until the beginning of the next UTF8 character sequence
        $mUcs4 = 0; // cached Unicode character
        $mBytes = 1; // cached expected number of octets in the current sequence
        $len = strlen($str);
        for ($i = 0; $i < $len; ++$i) {
            $in = ord($str{$i});
            if ($mState == 0) {
                // When mState is zero we expect either a US-ASCII character or a
                // multi-octet sequence.
                if (0 == (0x80 & ($in))) {
                    // US-ASCII, pass straight through.
                    $mBytes = 1;
                } elseif (0xC0 == (0xE0 & ($in))) {
                    // First octet of 2 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x1F) << 6;
                    $mState = 1;
                    $mBytes = 2;
                } elseif (0xE0 == (0xF0 & ($in))) {
                    // First octet of 3 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x0F) << 12;
                    $mState = 2;
                    $mBytes = 3;
                } elseif (0xF0 == (0xF8 & ($in))) {
                    // First octet of 4 octet sequence
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x07) << 18;
                    $mState = 3;
                    $mBytes = 4;
                } elseif (0xF8 == (0xFC & ($in))) {
                    /* First octet of 5 octet sequence.
                     *
                     * This is illegal because the encoded codepoint must be either
                     * (a) not the shortest form or
                     * (b) outside the Unicode range of 0-0x10FFFF.
                     * Rather than trying to resynchronize, we will carry on until the end
                     * of the sequence and let the later error handling code catch it.
                     */
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x03) << 24;
                    $mState = 4;
                    $mBytes = 5;
                } elseif (0xFC == (0xFE & ($in))) {
                    // First octet of 6 octet sequence, see comments for 5 octet sequence.
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 1) << 30;
                    $mState = 5;
                    $mBytes = 6;
                } else {
                    /* Current octet is neither in the US-ASCII range nor a legal first
                     * octet of a multi-octet sequence.
                     */
                    return false;
                }
            } else {
                // When mState is non-zero, we expect a continuation of the multi-octet
                // sequence
                if (0x80 == (0xC0 & ($in))) {
                    // Legal continuation.
                    $shift = ($mState - 1) * 6;
                    $tmp = $in;
                    $tmp = ($tmp & 0x0000003F) << $shift;
                    $mUcs4 |= $tmp;
                    /*
                     * End of the multi-octet sequence. mUcs4 now contains the final
                     * Unicode codepoint to be output
                     */
                    if (0 == --$mState) {
                        /*
                         * Check for illegal sequences and codepoints.
                         */
                        // From Unicode 3.1, non-shortest form is illegal
                        if (((2 == $mBytes) && ($mUcs4 < 0x0080)) ||
                                ((3 == $mBytes) && ($mUcs4 < 0x0800)) ||
                                ((4 == $mBytes) && ($mUcs4 < 0x10000)) ||
                                (4 < $mBytes) ||
                                // From Unicode 3.2, surrogate characters are illegal
                                (($mUcs4 & 0xFFFFF800) == 0xD800) ||
                                // Codepoints outside the Unicode range are illegal
                                ($mUcs4 > 0x10FFFF)
                        ) {
                            return false;
                        }
                        //initialize UTF8 cache
                        $mState = 0;
                        $mUcs4 = 0;
                        $mBytes = 1;
                    }
                } else {
                    /*
                     * ((0xC0 & (*in) != 0x80) && (mState != 0))
                     * Incomplete multi-octet sequence.
                     */
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Cleans up the text and adds separator.
     *
     * @param string $text
     * @param string $separator
     *
     * @return string
     */
    private static function postProcessText($text, $separator) {
        if (function_exists('mb_strtolower')) {
            $text = mb_strtolower($text);
        } else {
            $text = strtolower($text);
        }
        // Remove all none word characters
        $text = preg_replace('/\W/', ' ', $text);
        // More stripping. Replace spaces with dashes
        $text = strtolower(preg_replace('/[^A-Za-z0-9\/]+/', $separator, preg_replace('/([a-z\d])([A-Z])/', '\1_\2', preg_replace('/([A-Z]+)([A-Z][a-z])/', '\1_\2', preg_replace('/::/', '/', $text)))));
        return trim($text, $separator);
    }

}

/**
 * Genera archivo .vcard y lo guarda en un archivo o los saca como descarga
 *
 */
class VCard {

    /**
     * Elementos definidos
     *
     * @var array
     */
    private $definedElements;
    private $properties; //Array que contiene todos los campos y sus valores
    private $filename; // nombre que tendra el .vcf 
    private $contenido; // Foto en base64
    public $charset = 'iso-8859-1'; //Charset por defecto
    private $multiplePropertiesForElementAllowed = array(
        'email',
        'address',
        'phoneNumber',
        'url'
    );

    // <editor-fold defaultstate="collapsed" desc=" PROPIEDADES - Añadir elementos"> 
    /**
     * Add address
     *
     * @param  string [optional] $name
     * @param  string [optional] $extended
     * @param  string [optional] $street
     * @param  string [optional] $city
     * @param  string [optional] $region
     * @param  string [optional] $zip
     * @param  string [optional] $country
     * @param  string [optional] $type
     *                                     $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK
     *                                     or any combination of these: e.g. "WORK;PARCEL;POSTAL"
     * @return $this
     */
    public function addAddress(
    $name = '', $extended = '', $street = '', $city = '', $region = '', $zip = '', $country = '', $type = 'WORK;POSTAL'
    ) {
        // init value
        $value = $name . ';' . $extended . ';' . $street . ';' . $city . ';' . $region . ';' . $zip . ';' . $country;

        // set property
        $this->setProperty(
                'address', 'ADR' . (($type != '') ? ';' . $type : '') . $this->getCharsetString(), $value
        );

        return $this;
    }

    /**
     * Add birthday
     *
     * @param  string $date Format is YYYY-MM-DD
     * @return $this
     */
    public function addBirthday($date) {
        $this->setProperty(
                'birthday', 'BDAY', $date
        );

        return $this;
    }

    /**
     * Add company
     *
     * @param  string $company
     * @return $this
     */
    public function addCompany($company) {
        $this->setProperty(
                'company', 'ORG' . $this->getCharsetString(), $company
        );

        // if filename is empty, add to filename
        if ($this->getFilename() === null) {
            $this->setFilename($company);
        }

        return $this;
    }

    /**
     * Add email
     *
     * @param  string            $address The e-mail address
     * @param  string [optional] $type    The type of the email address
     *                                    $type may be  PREF | WORK | HOME
     *                                    or any combination of these: e.g. "PREF;WORK"
     * @return $this
     */
    public function addEmail($address, $type = '') {
        $this->setProperty('email', 'EMAIL;INTERNET' . (($type != '') ? ';' . $type : ''), $address);
        return $this;
    }

    /**
     * Add jobtitle
     *
     * @param  string $jobtitle The jobtitle for the person.
     * @return $this
     */
    public function addJobtitle($jobtitle) {
        $this->setProperty(
                'jobtitle', 'TITLE' . $this->getCharsetString(), $jobtitle
        );

        return $this;
    }

    /**
     * Add a photo or logo (depending on property name)
     *
     * @param  string              $property LOGO|PHOTO
     * @param  string              $url      image url or filename
     * @param  bool                $include  Do we include the image in our vcard or not?
     * @throws VCardMediaException if file is empty or not an image file
     */
    private function addMedia($property, $url, $include = true, $element) {
        if ($include) {
            $value = file_get_contents($url);
            if (!$value) {
                throw new VCardMediaException('Nothing returned from URL.');
            }
            $value = base64_encode($value);
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = finfo_file($finfo, 'data://application/octet-stream;base64,' . $value);
            finfo_close($finfo);
            if (preg_match('/^image\//', $mimetype) !== 1) {
                throw new VCardMediaException('Returned data aren\'t an image.' . $value);
            }
            $type = strtoupper(str_replace('image/', '', $mimetype));
            $property .= ";ENCODING=b;TYPE=" . $type;
        } else {
            $value = $url;
        }

        $this->setProperty(
                $element, $property, $value
        );
    }

    /**
     * Add name
     *
     * @param  string [optional] $lastName
     * @param  string [optional] $firstName
     * @param  string [optional] $additional
     * @param  string [optional] $prefix
     * @param  string [optional] $suffix
     * @return $this
     */
    public function addName(
    $lastName = '', $firstName = '', $additional = '', $prefix = '', $suffix = ''
    ) {
        // define values with non-empty values
        $values = array_filter(array(
            $prefix,
            $firstName,
            $additional,
            $lastName,
            $suffix,
        ));

        // define filename
        $this->setFilename($values);

        // set property
        $property = $lastName . ';' . $firstName . ';' . $additional . ';' . $prefix . ';' . $suffix;
        $this->setProperty(
                'name', 'N' . $this->getCharsetString(), $property
        );

        // is property FN set?
        if (!$this->hasProperty('FN')) {
            // set property
            $this->setProperty(
                    'fullname', 'FN' . $this->getCharsetString(), trim(implode(' ', $values))
            );
        }

        return $this;
    }

    /**
     * Add note
     *
     * @param  string $note
     * @return $this
     */
    public function addNote($note) {
        $this->setProperty(
                'note', 'NOTE' . $this->getCharsetString(), $note
        );

        return $this;
    }

    /**
     * Add phone number
     *
     * @param  string            $number
     * @param  string [optional] $type
     *                                   Type may be PREF | WORK | HOME | VOICE | FAX | MSG |
     *                                   CELL | PAGER | BBS | CAR | MODEM | ISDN | VIDEO
     *                                   or any senseful combination, e.g. "PREF;WORK;VOICE"
     * @return $this
     */
    public function addPhoneNumber($number, $type = '') {
        $this->setProperty(
                'phoneNumber', 'TEL' . (($type != '') ? ';' . $type : ''), $number
        );

        return $this;
    }
    /**
     * Add Photo
     *
     * @param  string $url     image url or filename
     * @param  bool   $include Include the image in our vcard?
     * @return $this
     */
    public function addPhoto($url, $include = true) {
        $this->addMedia(
                'PHOTO', $url, $include, 'photo'
        );

        return $this;
    }

    /**
     * Add Logo
     *
     * @param  string $url     image url or filename
     * @param  bool   $include Include the image in our vcard?
     * @return $this
     */
    public function addLogo($url, $include = true) {
        $this->addMedia(
                'LOGO', $url, $include, 'logo'
        );
        return $this;
    }

    /**
     * Add URL
     *
     * @param  string            $url
     * @param  string [optional] $type Type may be WORK | HOME
     * @return $this
     */
    public function addURL($url, $type = '') {
        $this->setProperty(
                'url', 'URL' . (($type != '') ? ';' . $type : ''), $url
        );

        return $this;
    }

    //</editor-fold>
    // <editor-fold defaultstate="collapsed" desc=" Elementos / Funciones Get(elemento) + Set(elemento)"> 

    /**
     * Para definir las propiedades (campos)
     *
     * @param  string $element The element name you want to set, f.e.: name, email, phoneNumber, ...
     * @param  string $key
     * @param  string $value
     * @return void
     */
    private function setProperty($element, $key, $value) {
        if (!in_array($element, $this->multiplePropertiesForElementAllowed) && isset($this->definedElements[$element])
        ) {
            throw new Exception('You can only set "' . $element . '" once.');
        }

        // we define that we set this element
        $this->definedElements[$element] = true;

        // adding property
        $this->properties[] = array(
            'key' => $key,
            'value' => $value
        );
    }

    /**
     * Get properties
     *
     * @return array
     */
    public function getProperties() {
        return $this->properties;
    }

    /**
     * Has property
     *
     * @param  string $key
     * @return bool
     */
    public function hasProperty($key) {
        $properties = $this->getProperties();

        foreach ($properties as $property) {
            if ($property['key'] === $key && $property['value'] !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get charset
     *
     * @return string
     */
    public function getCharset() {
        return $this->charset;
    }

    /**
     * Get charset string
     *
     * @return string
     */
    public function getCharsetString() {
        $charsetString = '';
        if ($this->charset == 'iso-8859-1') {
            $charsetString = ';CHARSET=' . $this->charset;
        }
        return $charsetString;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename() {
        return $this->filename;
    }

    /**
     * Set charset
     *
     * @param  mixed  $charset
     * @return void
     */
    public function setCharset($charset) {
        $this->charset = $charset;
    }

    /**
     * Set filename
     *
     * @param  mixed  $value
     * @param  bool   $overwrite [optional] Default overwrite is true
     * @param  string $separator [optional] Default separator is an underscore '_'
     * @return void
     */
    public function setFilename($value, $overwrite = true, $separator = '_') {
        // recast to string if $value is array
        if (is_array($value)) {
            $value = implode($separator, $value);
        }

        // Hace un trim
        $value = trim($value, $separator);

        // borra espacios
        $value = preg_replace('/\s+/', $separator, $value);

        if (empty($value)) {
            return;
        }

        // Nos hace un decode y nos pone el nombre del archivo en minusculas
        $value = strtolower($this->decode($value));

        // urlize quitandonos accentos y caracters
        $value = Transliterator::urlize($value);

        // overwrite filename or add to filename using a prefix in between
        $this->filename = ($overwrite) ?
                $value : $this->filename . $separator . $value;
    }

    /**
     * Para definir la variable desde fuera de la funcion
     * 
     * @param type $value
     */
    public function setPhotobase($value) {
        $this->contenido = $value;
    }

    /**
     * Recoge la variable desde fuera de la funcion
     * 
     * @return type
     */
    public function getPhotobase() {
        return $this->contenido;
    }

    /**
     * Get headers
     *
     * @param  bool  $asAssociative
     * @return array
     */
    public function getHeaders($asAssociative) {
        $contentType = $this->getContentType() . '; charset=' . $this->getCharset();

        $aux = $this->getFilename();

        $contentDisposition = 'attachment; filename=' . $aux . '.' . $this->getFileExtension();
        $contentLength = strlen($this->getOutput());
        $connection = 'close';

        if ((bool) $asAssociative) {
            return array(
                'Content-type' => $contentType,
                'Content-Disposition' => $contentDisposition,
                'Content-Length' => $contentLength,
                'Connection' => $connection,
            );
        }

        return array(
            'Content-type: ' . $contentType,
            'Content-Disposition: ' . $contentDisposition,
            'Content-Length: ' . $contentLength,
            'Connection: ' . $connection,
        );
    }

    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="Manipula el texto/String llamando a la clase Transliterator"> 

    /**
     * Decode
     *
     * @param  string $value The value to decode
     * @return string decoded
     */
    private function decode($value) {
        // convert cyrlic, greek or other caracters to ASCII characters
        return Transliterator::transliterate($value);
    }

    /**
     * Fold a line according to RFC2425 section 5.8.1.
     *
     * @link http://tools.ietf.org/html/rfc2425#section-5.8.1
     * @param  string $text
     * @return mixed
     */
    protected function fold($text) {
        if (strlen($text) <= 75) {
            return $text;
        }

        // split, wrap and trim trailing separator
        return substr(chunk_split($text, 73, "\r\n "), 0, -3);
    }

    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="Comprueba el SO y asigna un contructor/contenido"> 
    /**
     * Returns the browser user agent string.
     *
     * @return string
     */
    protected function getUserAgent() {
        if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            $browser = strtolower($_SERVER['HTTP_USER_AGENT']);
        } else {
            $browser = 'unknown';
        }

        return $browser;
    }

    /**
     * Get content type
     *
     * @return string
     */
    public function getContentType() {
        return ($this->isIOS7()) ?
                'text/x-vcalendar' : 'text/x-vcard';
    }

    /**
     * Get file extension
     *
     * @return string
     */
    public function getFileExtension() {
        return ($this->isIOS7()) ?
                'ics' : 'vcf';
    }

    /**
     * Is iOS - Check if the user is using an iOS-device
     *
     * @return bool
     */
    public function isIOS() {
        // recoge el UserAgent
        $browser = $this->getUserAgent();
        //si es IOS devuelve info
        return (strpos($browser, 'iphone') || strpos($browser, 'ipod') || strpos($browser, 'ipad'));
    }

    /**
     * Is iOS less than 7 (should cal wrapper be returned)
     *
     * @return bool
     */
    public function isIOS7() {
        return ($this->isIOS() && $this->shouldAttachmentBeCal());
    }

    /**
     * Checks if we should return vcard in cal wrapper
     *
     * @return bool
     */
    protected function shouldAttachmentBeCal() {
        $browser = $this->getUserAgent();

        $matches = array();
        preg_match('/os (\d+)_(\d+)\s+/', $browser, $matches);
        $version = isset($matches[1]) ? ((int) $matches[1]) : 999;

        return ($version < 8);
    }

    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="Constructores del vcf"> 
    /**
     * Build VCard (.vcf) // Construye el Vcard y mete todos los campos
     *
     * @return string
     */
    public function buildVCard() {
        // init string
        $string = "BEGIN:VCARD\r\n";
        $string .= "VERSION:3.0\r\n";
        $string .= "REV:" . date("Y-m-d") . "T" . date("H:i:s") . "Z\r\n";

        // loop all properties
        $properties = $this->getProperties();
        //print_r($properties);
        foreach ($properties as $property) {
            // add to string
            $string .= $this->fold($property['key'] . ':' . $property['value'] . "\r\n");
        }
        $string.="PHOTO;ENCODING=BASE64;TYPE=JPEG:" . $this->getPhotobase() . " \r\n";
        // add to string
        $string .= "END:VCARD\r\n";

        // return
        return $string;
    }

    /**
     * Build VCalender (.ics) - Safari (< iOS 8) can not open .vcf files, so we have build a workaround.
     *
     * @return string
     */
    public function buildVCalendar() { //En caso de IOS le mete unos valores antes y luego llama al buildVCard para meter los campos comunes
        // init dates
        $dtstart = date("Ymd") . "T" . date("Hi") . "00";
        $dtend = date("Ymd") . "T" . date("Hi") . "01";

        // init string
        $string = "BEGIN:VCALENDAR\n";
        $string .= "VERSION:2.0\n";
        $string .= "BEGIN:VEVENT\n";
        $string .= "DTSTART;TZID=Europe/London:" . $dtstart . "\n";
        $string .= "DTEND;TZID=Europe/London:" . $dtend . "\n";
        $string .= "SUMMARY:Click attached contact below to save to your contacts\n";
        $string .= "DTSTAMP:" . $dtstart . "Z\n";
        $string .= "ATTACH;VALUE=BINARY;ENCODING=BASE64;FMTTYPE=text/directory;\n";
        $string .= " X-APPLE-FILENAME=" . $this->getFilename() . "." . $this->getFileExtension() . ":\n";

        // base64 encode it so that it can be used as an attachemnt to the "dummy" calendar appointment
        $aux = $this->buildVCard();
        $b64vcard = base64_encode($this->buildVCard());
        // chunk the single long line of b64 text in accordance with RFC2045
        // (and the exact line length determined from the original .ics file exported from Apple calendar
        $b64mline = chunk_split($b64vcard, 74, "\n");
        // need to indent all the lines by 1 space for the iphone (yes really?!!)
        $b64final = preg_replace('/(.+)/', ' $1', $b64mline);
        $string .= $b64final;

        // output the correctly formatted encoded text
        $string .= "END:VEVENT\n";
        $string .= "END:VCALENDAR\n";

        // return
        return $string;
    }

    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="Output del vcf"> 
    /**
     * Get output as string
     * iOS devices (and safari < iOS 8 in particular) can not read .vcf (= vcard) files.
     * So I build a workaround to build a .ics (= vcalender) file.
     *
     * @return string
     */
    public function getOutput() {
        $output = ($this->isIOS()) ?
                $this->buildVCard() : $this->buildVCard();

        // we need to decode the output for outlook
        if ($this->getCharset() == 'iso-8859-1') {
            $output = utf8_decode($output);
        }

        return $output;
    }

    /**
     * Get output as string
     * @deprecated in the future
     *
     * @return string
     */
    public function get() {
        return $this->getOutput();
    }

    /**
     * Nos inicia la descarga en el navegador
     */
    public function download() {
        // define output
        $output = $this->getOutput();

        foreach ($this->getHeaders(false) as $header) {
            header($header);
        }

        // nos inicia la descarga
        echo $output;
    }

    /**
     * Save to a file
     *
     * @return void
     */
    public function save() {
        //guarda el archivo en la carpeta vcf, si no existe la crea
        if (!file_exists('./vcf/')) {
            mkdir('vcf', 0755, true);
        }
        $file = './vcf/' . $this->getFilename() . '.' . $this->getFileExtension();
        //nos guarda el archivo en la carpeta vcf
        file_put_contents(
                $file, $this->getOutput()
        );
    }

    //</editor-fold>
}

// Definimos vcard
$vcard = new VCard();

// variables para el nombre del archivo
$firstname = $_POST["firstname"];
$lastname = $_POST["lastname"];
$additional = '';
$prefix = '';
$suffix = '';

// Configura el el nombre del fichero dentro de addName setfilename
$vcard->addName($lastname, $firstname, $additional, $prefix, $suffix);

//si el usuario hace click en el boton de IOS y ha introducido el mail nos enviara el mail
$enviar_mail = false;
if (isset($_POST["IOS"]) && ($_POST["mail_IOS"] != "")) {
    $enviar_mail = true;
}
// insertamos los campos
if ($_POST["empresa"] != '') {
    $vcard->addCompany('' . $_POST["empresa"]);
}if ($_POST["cargo"] != '') {
    $vcard->addJobtitle('' . $_POST["cargo"]);
}if ($_POST["mail_profesional"] != '') {
    $vcard->addEmail('' . $_POST["mail_profesional"], 'PREF;WORK');
}if ($_POST["mail_personal"] != '') {
    $vcard->addEmail('' . $_POST["mail_personal"], 'HOME');
}if ($_POST["mail_otro"] != '') {
    $vcard->addEmail('' . $_POST["mail_otro"],'OTHER');
}if ($_POST["telefono_profesional"] != '') {
    $vcard->addPhoneNumber('' . $_POST["telefono_profesional"], 'PREF;WORK');
}if ($_POST["telefono_personal"] != '') {
    $vcard->addPhoneNumber($_POST["telefono_personal"], 'HOME');
}if ($_POST["telefono_otro"] != '') {
    $vcard->addPhoneNumber($_POST["telefono_otro"], 'OTHER');
}if ($_POST["address_calle"] != '' && $_POST["address_provincia"] != '') {
    $vcard->addAddress(null, null, '' . $_POST["address_calle"], '' . $_POST["address_ciudad"], null, '' . $_POST["address_provincia"] . " " . $_POST["address_zip"], null);
}if ($_POST["webpage"] != '') {
    $vcard->addURL('' . $_POST["webpage"]);
}if ($_POST["nota"] != '') {
    $vcard->addNote('' . $_POST["nota"]);
}if ($_POST["photo"] != '') {
    $foto = $_SERVER['DOCUMENT_ROOT'] . $_POST["photo"];
    $foto_size = filesize($foto);
    $handle = fopen($foto, "r");
    $contenido = fread($handle, $foto_size);
    fclose($handle);
    //la convertimos en base64
    $contenido = base64_encode($contenido);
    //la metemos en la variable para poder llamarla desde dentro de la clase
    $vcard->setPhotobase($contenido);
    //$vcard->addPhoto(ABSPATH.$_POST["photo"]);
}
if ($enviar_mail==true) {
    //guarda el archivo en /vcf/ si no existe la carpeta nos la crea en save
    $vcard->save();
    //Recoge los campos del mail
    $to = $_POST["mail_IOS"];
    $subject = $vcard->getFilename();
    $message = "<html><head></head><body><h1></h1></body></html>";
    //Lee el fichero que hemos guardado y lo mete en el contenido del mail
    $file = $vcard->getFilename() . "." . $vcard->getFileExtension();
    $file_size = filesize("./vcf/" . $file);
    $handle = fopen("./vcf/" . $file, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));

    // a random hash will be necessary to send mixed content
    $separator = md5(time());

    // carriage return type (we use a PHP end of line constant)
    $eol = PHP_EOL;

    // Header - Multiple para separar los content - types / Adjunto, contenido
    if ($_POST["mail_empresa"] == "") {
        $mail_empresa = "download@contact.com";
    } else {
        $mail_empresa = $_POST["mail_empresa"];
    }
    $headers = "From: " . $mail_empresa . $eol;
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"";

    // Mensaje
    $body .= "--" . $separator . $eol;
    $body .= "Content-Type: text/html; charset=\"iso-8859-1\"" . $eol;
    $body .= "Content-Transfer-Encoding: 8bit" . $eol . $eol;
    $body .= $message . $eol;

    // Adjunto
    $body .= "--" . $separator . $eol;
    $body .= "Content-Type: application/octet-stream; name=\"" . $file . "\"" . $eol;
    $body .= "Content-Transfer-Encoding: base64" . $eol;
    $body .= "Content-Disposition: attachment" . $eol . $eol;
    $body .= $content . $eol;
    $body .= "--" . $separator . "--";

    // Envia el mail comprobando si es un mail y dependiendo del resultado nos devuelve a la url en la que estabamos con un get para saber si ha habido algun error o se ha enviado correctamente
    if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
        if (mail($to, $subject, $body, $headers)) {
            if (!empty($_SERVER['HTTP_REFERER'])) {
                $url_origen = strstr($_SERVER['HTTP_REFERER'], '?', true);
                if ($url_origen == "") {
                    $url_origen = $_SERVER['HTTP_REFERER'];
                }
                header("Location: " . $url_origen . "?sent=true");
            } else
                echo "No referrer.";
        }else {
            if (!empty($_SERVER['HTTP_REFERER'])) {
                $url_origen = strstr($_SERVER['HTTP_REFERER'], '?', true);
                if ($url_origen == "") {
                    $url_origen = $_SERVER['HTTP_REFERER'];
                }
                header("Location: " . $url_origen . "?sent=true");
            } else
                echo "No referrer.";
        }
    } else {
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $url_origen = strstr($_SERVER['HTTP_REFERER'], '?', true);
            if ($url_origen == "") {
                $url_origen = $_SERVER['HTTP_REFERER'];
            }
            header("Location: " . $url_origen . "?sent=wrong");
        } else
            echo "No referrer.";
    }
} else {
    //si no es IOS nos lo descarga directamente
    return $vcard->download();
}
?>