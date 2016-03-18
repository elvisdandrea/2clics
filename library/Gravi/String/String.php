<?php

/**
 * Class String
 *
 * A library to manipulate and convert string values
 *
 * Author: Elvis D'Andrea
 * E-mail: elvis@vistasoft.com.br
 *
 */

class String {

    /**
     * Preventing string from having string injections
     *
     * @param   string      $string     - The original string
     * @return  string                  - The escaped string
     */
    public static function ClearString( $string ) {

        $string = filter_var($string, FILTER_SANITIZE_MAGIC_QUOTES, FILTER_SANITIZE_URL);
        return $string;
    }


    /**
     * Cleans an entire array recursively
     * from having string injection
     *
     * @param   array       $array      - The original array
     * @return  array                   - The escaped array
     */
    public static function ClearArray( $array ) {
        array_walk_recursive($array, function(&$item){
            $item = String::ClearString($item);
        });
        return $array;
    }

    /**
     * Remove new line characters from a string
     *
     * @param   string      $string     - The original string
     * @return  string                  - The string without new lines
     */
    public static function RemoveNewLines( $string ) {
        return preg_replace( '/\s+/', ' ', trim( $string ) );
    }

    /**
     * Concatenates each line of a string into slashes
     * and a concatenation character
     *
     * @param   string    $string           - The original string
     * @param   string    $concat_char      - The concatenation character
     * @return  string                      - The converted string
     */
    public static function BuildStringNewLines( $string, $concat_char = '+' ) {
        return preg_replace( '/' . PHP_EOL . '+/', '\'' . PHP_EOL . $concat_char .'"\\' . PHP_EOL .'"' . $concat_char . '\'', trim( $string ));
    }

    /**
     * An "addslashes" for single quotes only
     *
     * @param   string      $string     - The original string
     * @return  string
     */
    public static function AddSQSlashes( $string ) {
        return str_replace( '\'', '\\\'', $string );
    }

    /**
     * A non-validation version to format string dates
     * from dd/mm/yyyy to yyyy-mm-dd
     *
     * The purpose is to do it fast, so it's not secure
     * if the incoming string isn't correct
     *
     * @param   string      $date       - The original string date in dd/mm/yyyy format
     * @return  string                  - The string formatted to yyyy-mm-dd
     */
    public static function formatDateToSave($date) {

        return date('Y-m-d', strtotime($date));
    }

    /**
     * A non-validation version to format string dates
     * from yyyy-mm-dd to dd/mm/yyyy
     *
     * The purpose is to do it fast, so it's not secure
     * if the incoming string isn't correct
     *
     * @param   string      $date       - The original string date in yyyy-mm-dd format
     * @return  string                  - The string formatted to dd/mm/yyyy
     */
    public static function formatDateToLoad($date) {

        return date('d/m/Y', strtotime($date));
    }

    /**
     * Applies any mask based on # character
     *
     * Such a wow I just did
     *
     * @param   string      $val        - The number value
     * @param   string      $mask       - The desired mask
     * @return  string
     */
    public static function mask($val, $mask) {

        $val = preg_replace('/[^a-z0-9\-]/i','', $val);
        $masked = '';
        $k = strlen($val) - 1;
        for($i = strlen($mask)-1; $i>=0; $i--) {
            if ($k < 0) break;
            $mask[$i] != '#' || $masked = $val[$k--] . $masked;
            $mask[$i] == '#' || $masked = $mask[$i]  . $masked;
        }
        return $masked;
    }

    /**
     * Removes empty values for arrays
     * with numeric indexes
     *
     * The indexes that contain values will
     * be moved upwards, so numeric indexes
     * will remain in sequence
     *
     * @param   array       $array      - The original array
     */
    public static function arrayTrimNumericIndexed(&$array) {

        $result = array();

        foreach ($array as $value) $value == '' || $result[] = $value;
        $array = $result;
    }

    /**
     * Converts CameCase text to Uppercase-First-Letter words
     *
     * @param   string      $word           - The CamelCased Text
     * @return  string                      - The Uppercase-First-Letter text
     */
    public static function decamelize($word) {
        return preg_replace(
            '/(^|[a-z])([A-Z])/e',
            'strlen("\\1") ? "\\1 \\2" : "\\2"',
            $word
        );
    }

    /**
     * Converts underline_separated_text to CamelCase Text
     *
     * @param   string      $word           - The underlined_separated_text
     * @return  string                      - The CamelCased text
     */
    public static function camelize($word) {
        return preg_replace('/(^|_)([a-z])/e', 'strtoupper("\\2")', $word);
    }

    /**
     * Generates numeric intervals between 2 numbers
     * with a gap of specific percentage
     *
     * @param   $min            - min value
     * @param   $max            - max value
     * @param   $percentage     - gap percentage (% of (min - max) for each value)
     * @return  array
     */
    public static function generateIntervals($min, $max, $percentage) {

        $interval = ((($max - $min) * $percentage) / 100);
        $start  = 1;
        $result = array();
        $result[] = $min;
        while ($min < $max) {
            $result[] = $min + $interval;
            $min += $interval;
            $start++;
        }

        return $result;
    }

    /**
     * Converte o texto do campo conforme opcao
     *
     * @param   string      $string         - O conteúdo do campo
     * @param   int         $option         - A opcao
     * @return  string
     */
    public static function convertTextFormat($string, $option) {
        switch ($option) {
            case 'ln': //Letras e Números
                $string = preg_replace('/[^a-z0-9\-]/i','',$string);
                break;
            case 'l': //Letras
                $string = preg_replace('/convertTe[^a-z\-]/i','',$string);
                break;
            case 'n': //Números
                $string = preg_replace('/[^0-9\-]/i','',$string);
                break;
            case 'fone': //Telefone
                $string = self::phoneFormat($string);
                break;
            case 'ddd': //Telefone com DDD
                $string = self::phoneFormat($string);
                break;
            case 'cnpj': //CNPJ
                $string = self::mask($string, '##.####.###/####-##');
                break;
            case 'cpf': //CPF
                $string = self::mask($string, '###.###.###-##');
                break;
            case 'cep': //CEP
                $string = self::mask($string, '#####-###');
                break;
            case 'currency':
                if (intval($string) == 0) $string = '0';
                $string = 'R$ ' . number_format($string, 2, ',', '.');
                break;
            case 'int': //Numeros Inteiros
                $string = preg_replace('/[^0-9\-]/i','',$string);
                break;
            case 'float':
                $string = number_format($string, 2, ',', '.');
                break;
        }

        return $string;
    }

    /**
     * Quick method para currency
     *
     * @param   string      $string     - O conteúdo
     * @return  string
     */
    public static function asCurrency($string) {

        return self::convertTextFormat($string, 'currency');
    }

    /**
     * Replaces http by https protocol
     *
     * @param   $url        - The string URL
     * @return  mixed
     */
    public static function asHttps($url) {

        return str_replace('http://', 'https://', $url);
    }

    /**
     * Valida o formato dos dados em um campo de texto
     *
     * @param   string      $string     - O conteúdo
     * @param   int         $option     - O valor da opcao
     * @return  bool
     */
    public static function validateTextFormat($string, $option) {

        switch ($option) {
            case 'ln': //Letras e Números
                return preg_match('/[a-z0-9]/i', $string);
                break;
            case 'l': //Letras
                return preg_match('/[a-z]/i', $string);
                break;
            case 'n': //Números
                return preg_match('/[0-9]/i', $string);
                break;
            case 'fone': //Telefone
                return preg_match('/^([0-9]{2}\s[0-9]{4}\-[0-9]{4})|([0-9]{2}\s[0-9]{5}\-[0-9]{4})$/', $string);
                break;
            case 'ddd': //Telefone com DDD
                return preg_match('/^([0-9]{2}\s[0-9]{4}\-[0-9]{4})|([0-9]{2}\s[0-9]{5}\-[0-9]{4})$/', $string);
                break;
            case 'cnpj': //CNPJ
                return preg_match('/^(\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2})$/', $string);
                break;
            case 'cpf': //CPF
                return preg_match('/^(\d{3}\.\d{3}\.\d{3}\-\d{2})$/', $string);
                break;
            case 'cnpjcpf': //CPNJ ou CPF
                return preg_match('/(^\d{3}\.\d{3}\.\d{3}\-d{2}$)|(^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$)/', $string);
                break;
            case 'cep': //CEP
                return preg_match('/^(\d{5}\-\d{3})$/', $string);
                break;
            case 'email': //E-MAIL
                return filter_var($string, FILTER_VALIDATE_EMAIL);
                break;
            case 'ip': //E-MAIL
                return filter_var($string, FILTER_VALIDATE_IP);
                break;
            case 'int': //Numeros Inteiros
                return filter_var($string, FILTER_VALIDATE_INT);
                break;
            case 'float':
                return filter_var($string, FILTER_VALIDATE_FLOAT);
                break;
            default:
                return true;
                break;
        }
    }

    public static function isChecked($params, $field, $content) {

        if (array_key_exists($field, $params)) {
            if (is_array($params[$field])) {
                return in_array($content, $params[$field]);
            } else {
                return $params[$field] == $content;
            }
        }

        return false;
    }

}

?>
