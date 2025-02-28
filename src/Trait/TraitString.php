<?php

declare(strict_types=1);

namespace Bidoch78\Bimi\Trait;

trait TraitString {

    public function explodeEx(array $separator, string $text, int $limit = PHP_INT_MAX): array {

        if (!count($separator)) throw new \ValueError('$separator array must have at least 1 element');

        $len = strlen($text);
        if (!$len) return [ "" ];

        $useSep = []; $validSep = false;
        foreach($separator as $sep) {
            if ($sep && strlen($sep)) { $validSep = true; $useSep[] = $sep; }
        }
        if (!$validSep) throw new \ValueError('$separator array must have at least 1 string element');
        
        if (!$limit) $limit = 1;

        $return = [];
        $pos = 0;
        $countSep = 0;
       
        while($pos !== false) {

            $aPos = false; $cPos = null;
            foreach($useSep as $sep) {
                $cPos = strpos($text, $sep, $pos);
                if ($cPos !== false && ($aPos === false || $cPos < $aPos)) $aPos = $cPos;
            }

            if ($aPos === false || ($limit > 0 && $countSep+1 >= $limit)) { 
                $return[] = substr($text, $pos);
                $pos = false; 
                continue;
            }

            $return[] = substr($text, $pos, $aPos - $pos);
            $pos = $aPos + 1;
            $countSep++;

        }

        if ($limit < 0) array_splice($return, count($return) + $limit);

        return $return;
 
    }

}