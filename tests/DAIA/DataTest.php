<?php declare(strict_types=1);

namespace DAIA;

class DataTest extends \PHPUnit\Framework\TestCase
{
    public function testEntity()
    {
        foreach (['Institution', 'Department', 'Storage', 'Limitation'] as $class) {
            $class = "DAIA\\$class";

            // empty constructor
            $object = new $class();
            $this->assertSame("$object", '{}');

            // stringify as JSON
            $e = new $class(['id'=>'x:y']);
            $this->assertSame("$e", '{"id":"x:y"}');

            // pretty printed JSON
            $this->assertSame($e->json(), "{\n    \"id\": \"x:y\"\n}");

            $data = [
                'id' => 'x:y', 
                'content' => 'foo',
                'href' => 'http://example.org',
                'ignore' => 123
            ];
            $e = new $class($data);
            $this->assertSame("$e", '{"content":"foo","href":"http://example.org","id":"x:y"}');        
        }
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage DAIA\Storage->foo does not exist
     */
    public function testGetterException() {
        $entity = new Storage();
        $entity->foo;
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage DAIA\Limitation->foo does not exist
     */
    public function testSetterException() {
        $entity = new Limitation();
        $entity->foo = 'bar';
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage DAIA\Document->id is required
     */
    public function testRequiredException() {
        $document = new Document([]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage DAIA\Document->id is required
     */
    public function testNotNullException() {
        $document = new Document(['id'=>'i:d']);
        $document->id = NULL;
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Call to undefined method DAIA\Document->addFoo
     */
    public function testCallUndefined() {
        $document = new Document(['id'=>'i:d']);
        $document->addFoo(123);
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage DAIA\Document->id is not repeatable
     */
    public function testAddNonRepeatable() {
        $document = new Document(['id'=>'i:d']);
        $document->addId('x:y');
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage DAIA\Storage->content must be string
     */
    public function testStringRequired() {
        $document = new Storage(['content'=>[]]);
    }
}
