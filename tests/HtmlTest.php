<?php
use Ptilz\Html;

class HtmlTest extends PHPUnit_Framework_TestCase {

    function testArrs() {
        $this->assertSame('', Html::attrs([]));
        $this->assertSame(' a="b" c="d"', Html::attrs(['a' => 'b', 'c' => 'd']));
        $this->assertSame(' data-id="1" checked selected="" readonly indeterminate', Html::attrs(['data-id' => 1, 'checked' => true, 'disabled' => false, 'selected' => null, 'readonly','999'=>'indeterminate']));
        $this->assertSame(' html="&lt;&gt;&quot;\'"', Html::attrs(['html' => '<>"\'']));
        $this->assertSame(' class="a b c" style="color:red;width:100px"', Html::attrs(['class' => ['a', 'b', 'c'], 'style' => ['color' => 'red', 'width' => '100px']]));
    }

    function testStripTags() {
        $html = '<a>b<!-- <c> --><d>e</d><f>g</f>';
        $this->assertSame('b<!-- <c> -->e<f>g</f>', Html::stripTags($html, ['f'], true));
        $this->assertSame('<a>b<d>e</d>g', Html::stripTags($html, ['a', 'd'], false));
        $this->assertSame('b', Html::stripTags('<?php a ?>b<? c'));
    }
}