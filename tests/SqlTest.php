<?php
use Ptilz\Sql;
use PHPUnit\Framework\TestCase;

class SqlTest extends TestCase {


    function testFormat() {
        $this->assertSame("select * from t",Sql::format("select * from t"));
        $this->assertSame("select `column` from `table` where `field1`=1 and `field2` in ('x','y','z')",Sql::format("select ?? from ?? where ??=? and ?? in ?",['column','table','field1',1,'field2',['x','y','z']]));
        $this->assertSame("select `column` from `table` where `field1`=1 and `field2` in ('x','y','z')",Sql::format("select ::column from ::table where ::field1=:value1 and ::field2 in :value2",['column'=>'column','table'=>'table','field1'=>'field1','value1'=>1,'field2'=>'field2','value2'=>['x','y','z']]));
        $this->assertSame("select 'who?', `what?`, \"when?\" from `some`.`table`",Sql::format("select 'who?', `what?`, \"when?\" from ??",['some.table']));
        $this->assertSame("insert into `t` set `a`=1, `b`=NULL",Sql::format("insert into ::table set :data",['table'=>'t','data'=>['a'=>1,'b'=>null]]));
    }

}