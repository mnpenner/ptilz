<?php
use Ptilz\Exceptions\InvalidOperationException;
use Ptilz\IJavaScriptSerializable;
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
        $this->assertSame('"/<\\/script>/"', Json::encode("/</script>/", JSON_ESCAPE_SCRIPTS), 'JSON_ESCAPE_SCRIPTS');
        $this->assertSame('"<\/ScRiPt><script>alert(\'hack!\')<\/script>"', Json::encode("</ScRiPt><script>alert('hack!')</script>", JSON_ESCAPE_SCRIPTS), 'JSON_ESCAPE_SCRIPTS');
        $this->assertSame('"/</script>/"', Json::encode("/</script>/", JSON_UNESCAPED_SLASHES), 'JSON_UNESCAPED_SLASHES');
        $this->assertSame('"\u00c8"', Json::encode(chr(200), JSON_FORCE_UTF8), "JSON_FORCE_UTF8");
        $this->assertSame('"È"', Json::encode(chr(200), JSON_FORCE_UTF8 | JSON_UNESCAPED_UNICODE), "JSON_FORCE_UTF8 | JSON_UNESCAPED_UNICODE");
        $this->assertSame('"\\/<\\/script>\\/"', Json::encode("/</script>/", 0), 'No options');
        $this->assertSame('new Dog("Bella", "bone")', Json::encode(new Dog('Bella', 'bone')), "IJavaScriptSerializable");

        $pretty = ['a'=>1,'b'=>[2,false,'c'=>[4,true]],null];
        $this->assertSame(str_replace("\n",PHP_EOL,json_encode($pretty, JSON_PRETTY_PRINT)), Json::encode($pretty, Json::PRETTY_PRINT), "PRETTY_PRINT");
        $this->assertSame('[]', Json::encode([]), "Empty array");
        $this->assertSame('{}', Json::encode(new \stdClass), "Empty object");
        $std = new \stdClass;
        $std->foo = 'bar';
        $this->assertSame('[{},[{"foo":"bar"}]]', Json::encode([new \stdClass,[$std]]), "Standard class properties");

    }

    function testEncodeException() {
        $this->setExpectedException('Ptilz\Exceptions\InvalidOperationException', null, JSON_ERROR_UTF8);
        Json::encode(chr(200));
    }

    function testDecode() {
        $this->assertSame('str', Json::decode('"str"'));
        $this->assertSame(123, Json::decode('123'));
        $this->assertSame(['a' => 1], Json::decode('{"a":1}'));
    }

    function testDecodeException() {
        $this->setExpectedException(InvalidOperationException::class, null, JSON_ERROR_SYNTAX);
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

class Dog implements IJavaScriptSerializable {
    protected $name;
    protected $favToy;

    function __construct($name, $favToy) {
        $this->name = $name;
        $this->favToy = $favToy;
    }

    function jsSerialize($options) {
        return 'new Dog('.json_encode($this->name, $options).', '.json_encode($this->favToy, $options).')';
    }
}