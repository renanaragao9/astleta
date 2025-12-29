<?php

namespace App\Utils;

class NumberToWords
{
    private static $units = [
        0 => 'zero',
        1 => 'um',
        2 => 'dois',
        3 => 'três',
        4 => 'quatro',
        5 => 'cinco',
        6 => 'seis',
        7 => 'sete',
        8 => 'oito',
        9 => 'nove',
        10 => 'dez',
        11 => 'onze',
        12 => 'doze',
        13 => 'treze',
        14 => 'quatorze',
        15 => 'quinze',
        16 => 'dezesseis',
        17 => 'dezessete',
        18 => 'dezoito',
        19 => 'dezenove',
    ];

    private static $tens = [
        2 => 'vinte',
        3 => 'trinta',
        4 => 'quarenta',
        5 => 'cinquenta',
        6 => 'sessenta',
        7 => 'setenta',
        8 => 'oitenta',
        9 => 'noventa',
    ];

    private static $scale = [
        100 => 'cento',
        1000 => 'mil',
        1000000 => 'milhão',
        1000000000 => 'bilhão',
    ];

    /**
     * Converte um número em sua representação por extenso
     */
    public static function convert($number)
    {
        $number = (float) $number;

        if ($number == 0) {
            return 'zero reais';
        }

        $parts = explode('.', number_format($number, 2, '.', ''));
        $integerPart = (int) $parts[0];
        $decimalPart = (int) $parts[1];

        $result = '';

        // Parte inteira
        if ($integerPart > 0) {
            $result .= self::convertInteger($integerPart);
            $result .= $integerPart == 1 ? ' real' : ' reais';
        }

        // Parte decimal
        if ($decimalPart > 0) {
            if ($result !== '') {
                $result .= ' e ';
            }
            $result .= self::convertInteger($decimalPart);
            $result .= $decimalPart == 1 ? ' centavo' : ' centavos';
        }

        return $result;
    }

    /**
     * Converte a parte inteira de um número
     */
    private static function convertInteger($number)
    {
        if ($number == 0) {
            return '';
        }

        if ($number < 20) {
            return self::$units[$number];
        }

        if ($number < 100) {
            $tens = (int) ($number / 10);
            $units = $number % 10;
            $result = self::$tens[$tens];
            if ($units > 0) {
                $result .= ' e '.self::$units[$units];
            }

            return $result;
        }

        if ($number < 1000) {
            $hundreds = (int) ($number / 100);
            $remainder = $number % 100;

            if ($hundreds == 1) {
                $result = 'cento';
            } else {
                $result = self::$units[$hundreds].'centos';
            }

            if ($remainder > 0) {
                $result .= ' e '.self::convertInteger($remainder);
            }

            return $result;
        }

        if ($number < 1000000) {
            $thousands = (int) ($number / 1000);
            $remainder = $number % 1000;

            if ($thousands == 1) {
                $result = 'mil';
            } else {
                $result = self::convertInteger($thousands).' mil';
            }

            if ($remainder > 0) {
                $result .= ' '.self::convertInteger($remainder);
            }

            return $result;
        }

        if ($number < 1000000000) {
            $millions = (int) ($number / 1000000);
            $remainder = $number % 1000000;

            $result = self::convertInteger($millions);
            $result .= $millions == 1 ? ' milhão' : ' milhões';

            if ($remainder > 0) {
                $result .= ' '.self::convertInteger($remainder);
            }

            return $result;
        }

        $billions = (int) ($number / 1000000000);
        $remainder = $number % 1000000000;

        $result = self::convertInteger($billions);
        $result .= $billions == 1 ? ' bilhão' : ' bilhões';

        if ($remainder > 0) {
            $result .= ' '.self::convertInteger($remainder);
        }

        return $result;
    }
}
