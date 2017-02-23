<?php

namespace GBV;


class ISIL
{
    static function ok($isil)
    {        
        return is_string($isil)
               and preg_match('!^[A-Z]+-[A-Za-z0-9-/:]+!', $isil)
               and strlen($isil) <= 11;
    }
}
