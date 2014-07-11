<?php
use Ptilz\Json;

class JsonTest extends PHPUnit_Framework_TestCase {
    function testEncode() {
        $this->assertSame('"str"', Json::encode('str'));
        $this->assertSame('"123"', Json::encode('123'));
        $this->assertSame('123', Json::encode('123', JSON_NUMERIC_CHECK));
        $this->assertSame('"J\u2028S"', Json::encode('J S'), "Safe handling of special characters for JavaScript compatibility"); // http://timelessrepo.com/json-isnt-a-javascript-subset
        $this->assertSame('[1,2,3]', Json::encode([1, 2, 3]));
        $this->assertSame('{"0":1,"1":2,"2":3}', Json::encode([1, 2, 3], JSON_FORCE_OBJECT));
        $this->assertSame('{"1":1,"2":2,"3":3}', Json::encode([1 => 1, 2, 3]));
        $this->assertSame('"<\/script>"', Json::encode('</script>'));
        $this->assertSame('"</script>"', Json::encode('</script>', JSON_UNESCAPED_SLASHES));
        $this->assertSame('{"template":function(){},"context":{"name":"json"}}',
            Json::encode([
                'template' => Json::raw('function(){}'),
                'context' => ['name' => 'json']
            ]), "Json::raw");
        $this->assertSame('{"__type":"Person","name":"Mark","gender":"Male"}', Json::encode(new Person('Mark', 'Male')), "JsonSerializable");
    }

    function testDecode() {
        $this->assertSame('str', Json::decode('"str"'));
        $this->assertSame(123, Json::decode('123'));
        $this->assertSame(['a' => 1], Json::decode('{"a":1}'));
    }

    function testDecodeException() {
        $this->setExpectedException('Ptilz\Exceptions\JsonException', null, JSON_ERROR_SYNTAX);
        Json::decode("'str'"); // strings must be quoted with double-quotes in JSON
    }
}

class Person implements JsonSerializable {
    protected $name;
    protected $gender;

    function __construct($name, $gender) {
        $this->name = $name;
        $this->gender = $gender;
    }


    function jsonSerialize() {
        return [
            '__type' => __CLASS__,
            'name' => $this->name,
            'gender' => $this->gender,
        ];
    }
}