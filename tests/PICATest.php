<?php

use PHPUnit\Framework\TestCase;
use PICA\Field;


class PICATest extends TestCase {

    public function testField() {
        $fields = [
            '123@ $abc$def$a' => [
                'tag' => '123@',
                'occ' => null,
                'subfields' => ['a','bc','d','ef','a',''],
                'a' => 'bc',
                'aa' => ['bc',''],
                'dd' => ['ef'],
            ],
            '000A/00 $0$$'  => [
                'tag' => '000A',
                'occ' => '00',
                'subfields' => ['0','$'],
                '0' => '$',
                '00' => ['$'],
                'x' => null,
                'xx' => [],
                'abc' => null,
            ]
        ];
        foreach ($fields as $str => $expect) {
            $f = new Field($str);
            foreach ($expect as $name => $value) {
                $this->assertSame($f->$name, $value);
                if (strlen($name) == 1) {
                    $this->assertSame($f->value($name), $value);
                } elseif (strlen($name) == 2 and $name[0]==$name[1]) {
                    $this->assertSame($f->values($name[0]), $value);
                }
            }
            $this->assertSame("$f", $str);
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFieldConstructor() {
        $f = new Field("333");
    }
}
