<?php
use Ptilz\Html;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase {

    function testAttrs() {
        $this->assertSame('', Html::attrs([]));
        $this->assertSame(' a="b" c="d"', Html::attrs(['a' => 'b', 'c' => 'd']));
        $this->assertSame(' data-id="1" checked selected="" readonly indeterminate', Html::attrs(['data-id' => 1, 'checked' => true, 'disabled' => false, 'selected' => '', 'readonly','999'=>'indeterminate', 'badattr'=>null]));
        $this->assertSame(' html="&lt;&gt;&quot;\'"', Html::attrs(['html' => '<>"\'']));
        $this->assertSame(' class="a b c" style="color:red;width:100px"', Html::attrs(['class' => ['a', 'b', 'c'], 'style' => ['color' => 'red', 'width' => '100px']]));
    }

    function testStripTags() {
        $html = '<a>b<!-- <c> --><d>e</d><f>g</f>';
        $this->assertSame('b<!-- <c> -->e<f>g</f>', Html::stripTags($html, ['f'], true));
        $this->assertSame('<a>b<d>e</d>g', Html::stripTags($html, ['a', 'd'], false));
        $this->assertSame('b', Html::stripTags('<?php a ?>b<? c'));
    }

    function testMergeAttrs() {
        $this->assertSame(['a'=>'x','b'=>'y'],Html::mergeAttrs(['a'=>'x'],['b'=>'y']));
        $this->assertSame(['class'=>'foo bar baz'],Html::mergeAttrs(['class'=>'foo bar'],['class'=>'baz']));
        $this->assertSame(['class'=>'foo bar baz'],Html::mergeAttrs(['class'=>['foo','bar']],['class'=>'baz']));
        $this->assertSame(['class'=>'foo bar baz'],Html::mergeAttrs(['class'=>'foo bar'],['class'=>['baz']]));
        $this->assertSame(['class'=>'foo bar'],Html::mergeAttrs(['class'=>'foo bar'],['class'=>'foo']));

        $this->assertSame(['style'=>'font-weight:bold;color:red'],Html::mergeAttrs(
            ['style'=>['font-weight'=>'bold']],
            ['style'=>['color'=>'red']])
        );
        $this->assertSame(['style'=>'font-weight:bold;color:red'],Html::mergeAttrs(
            ['style'=>'font-weight:bold'],
            ['style'=>'color:red'])
        );
        $this->assertSame(['style'=>'font-weight:bold;color:red'],Html::mergeAttrs(
            ['style'=>['font-weight'=>'bold']],
            ['style'=>'color:red'])
        );
        $this->assertSame(['style'=>'color:blue;font-weight:bold'],Html::mergeAttrs(
            ['style'=>'color:red'],
            ['style'=>['font-weight'=>'bold','color'=>'blue']])
        );

        $this->assertSame(['disabled'=>false,'class'=>'danger btn','name'=>'explode','id'=>'ignite'],Html::mergeAttrs(
            ['disabled'=>true,'class'=>'danger','name'=>'explode'],
            ['class'=>'btn','disabled'=>false,'id'=>'ignite']
        ));

        $this->assertSame(['data-Foo'=>'Bar','data-BAZ'=>'QUX'],Html::mergeAttrs(['data-Foo'=>'Bar'],['DATA-BAZ'=>'QUX']));
    }

    function testDataAttrs() {
        $this->assertSame(['data-foo'=>'bar','data-baz'=>'qux'],Html::dataAttrs(['foo'=>'bar','baz'=>'qux']));
        $this->assertSame(['data-foo'=>'true','data-bar'=>'false','data-baz'=>'null'],Html::dataAttrs(['foo'=>true,'bar'=>false,'baz'=>null]));
        $this->assertSame(['data-foo'=>'["bar","baz"]'],Html::dataAttrs(['foo'=>['bar','baz']]));
        $this->assertSame(['data-foo'=>'{"bar":"baz"}'],Html::dataAttrs(['foo'=>['bar'=>'baz']]));
    }
}