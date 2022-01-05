<?php
namespace Otto\Sapi\Cli;

class GetoptTest extends \PHPUnit\Framework\TestCase
{
    protected $getopt;

    protected function setUp(): void
    {
        $this->getopt = new Getopt();
    }

    // public function testSetOptions()
    // {
    //     $options = array(
    //         'foo-bar,f*:',
    //         'baz-dib,b::',
    //         'z,zim-gir',
    //     );

    //     $this->getopt->setOptions($options);
    //     $expect = array(
    //         '--foo-bar' => new Option(
    //             name: '--foo-bar',
    //             alias: '-f',
    //             multi: true,
    //             param: 'required',
    //             descr: null,
    //         ),
    //         '--baz-dib' => new Option(
    //             name: '--baz-dib',
    //             alias: '-b',
    //             multi: false,
    //             param: 'optional',
    //             descr: null,
    //         ),
    //         '-z' => new Option(
    //             name: '-z',
    //             alias: '--zim-gir',
    //             multi: false,
    //             param: 'rejected',
    //             descr: null,
    //         ),
    //     );

    //     $actual = $this->getopt->getOptions();
    //     $this->assertEquals($expect, $actual);

    //     // get an aliased option
    //     $actual = $this->getopt->getOption('--zim-gir');
    //     $this->assertEquals($expect['-z'], $actual);
    // }

    public function testParse_noOptions()
    {
        $result = $this->getopt->parseInput(array('abc', 'def'));
        $this->assertTrue($result);

        $expect = array('abc', 'def');
        $actual = $this->getopt->getValues();
        $this->assertSame($expect, $actual);
    }

    public function testParse_undefinedOption()
    {
        $this->expectException(Exception\OptionNotDefined::CLASS);
        $this->expectExceptionMessage("The option '-z' is not defined.");
        $this->getopt->parseInput(array('-z', 'def'));
    }

    public function testParse_longRejected()
    {
        $options = array('foo-bar');
        $this->getopt->setOptions($options);

        $result = $this->getopt->parseInput(array('--foo-bar'));
        $this->assertTrue($result);

        $expect = array('--foo-bar' => true);
        $actual = $this->getopt->getValues();
        $this->assertSame($expect, $actual);

        $this->expectException(Exception\OptionParamRejected::CLASS);
        $this->expectExceptionMessage("The option '--foo-bar' does not accept a parameter.");
        $this->getopt->parseInput(array('--foo-bar=baz'));
    }

    public function testParse_longRequired()
    {
        $options = array('foo-bar:');
        $this->getopt->setOptions($options);

        // '=' as separator
        $result = $this->getopt->parseInput(array('--foo-bar=baz'));
        $this->assertTrue($result);

        $expect = array('--foo-bar' => 'baz');
        $actual = $this->getopt->getValues();
        $this->assertSame($expect, $actual);

        // ' ' as separator
        $result = $this->getopt->parseInput(array('--foo-bar', 'baz'));
        $this->assertTrue($result);

        $expect = array('--foo-bar' => 'baz');
        $actual = $this->getopt->getValues();
        $this->assertSame($expect, $actual);

        // missing required value
        $this->expectException(Exception\OptionParamRequired::CLASS);
        $this->expectExceptionMessage("The option '--foo-bar' requires a parameter.");
        $this->getopt->parseInput(array('--foo-bar'));
    }

    public function testParse_longOptional()
    {
        $options = array('foo-bar::');
        $this->getopt->setOptions($options);

        $result = $this->getopt->parseInput(array('--foo-bar'));
        $this->assertTrue($result);

        $expect = array('--foo-bar' => true);
        $actual = $this->getopt->getValues();
        $this->assertSame($expect, $actual);

        $result = $this->getopt->parseInput(array('--foo-bar=baz'));
        $this->assertTrue($result);

        $expect = array('--foo-bar' => 'baz');
        $actual = $this->getopt->getValues();
        $this->assertSame($expect, $actual);
    }

    public function testParse_longMultiple()
    {
        $options = array('foo-bar*::');
        $this->getopt->setOptions($options);

        $result = $this->getopt->parseInput(array(
            '--foo-bar',
            '--foo-bar',
            '--foo-bar=baz',
            '--foo-bar=dib',
            '--foo-bar'
        ));
        $this->assertTrue($result);

        $expect = array('--foo-bar' => array(true, true, 'baz', 'dib', true));
        $actual = $this->getopt->getValues();
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortRejected()
    {
        $options = array('f');
        $this->getopt->setOptions($options);

        $result = $this->getopt->parseInput(array('-f'));
        $this->assertTrue($result);

        $expect = array('-f' => true);
        $actual = $this->getopt->getValues();
        $this->assertSame($expect, $actual);

        $result = $this->getopt->parseInput(array('-f', 'baz'));
        $this->assertTrue($result);

        $expect = array('-f' => true, 'baz');
        $actual = $this->getopt->getValues();
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortRequired()
    {
        $options = array('f:');
        $this->getopt->setOptions($options);

        $result = $this->getopt->parseInput(array('-f', 'baz'));
        $this->assertTrue($result);

        $expect = array('-f' => 'baz');
        $actual = $this->getopt->getValues();
        $this->assertSame($expect, $actual);

        $this->expectException(Exception\OptionParamRequired::CLASS);
        $this->expectExceptionMessage("The option '-f' requires a parameter.");
        $this->getopt->parseInput(array('-f'));
    }

    public function testParse_shortOptional()
    {
        $options = array('f::');
        $this->getopt->setOptions($options);

        $result = $this->getopt->parseInput(array('-f'));
        $this->assertTrue($result);

        $expect = array('-f' => true);
        $actual = $this->getopt->getValues();
        $this->assertSame($expect, $actual);

        $result = $this->getopt->parseInput(array('-f', 'baz'));
        $this->assertTrue($result);

        $expect = array('-f' => 'baz');
        $actual = $this->getopt->getValues();
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortMultiple()
    {
        $options = array('f*::');
        $this->getopt->setOptions($options);

        $result = $this->getopt->parseInput(array('-f', '-f', '-f', 'baz', '-f', 'dib', '-f'));
        $this->assertTrue($result);

        $expect = array('-f' => array(true, true, 'baz', 'dib', true));
        $actual = $this->getopt->getValues();
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortCluster()
    {
        $options = array('f', 'b', 'z');
        $this->getopt->setOptions($options);

        $result = $this->getopt->parseInput(array('-fbz'));
        $this->assertTrue($result);

        $expect = array(
            '-f' => true,
            '-b' => true,
            '-z' => true,
        );
        $actual = $this->getopt->getValues();
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortClusterRequired()
    {
        $options = array('f', 'b:', 'z');
        $this->getopt->setOptions($options);

        $this->expectException(Exception\OptionParamRequired::CLASS);
        $this->expectExceptionMessage("The option '-b' requires a parameter.");
        $this->getopt->parseInput(array('-fbz'));
    }

    public function testParseAndGet()
    {
        $this->getopt->setOptions(array('#foo', 'foo-bar:', '#bar', 'b', '#baz?', 'z::'));
        $this->getopt->parseInput(array(
            'abc',
            '--foo-bar=zim',
            'def',
            '-z',
            'qux',
            '-b',
            'gir',
            '--',
            '--after-double-dash=123',
            '-n',
            '456',
            'ghi',
        ));

        // all values
        $expect = array(
            'abc',
            '--foo-bar' => 'zim',
            'def',
            '-z' => 'qux',
            '-b' => true,
            'gir',
            '--after-double-dash=123',
            '-n',
            '456',
            'ghi',
        );

        // get all values
        $actual = $this->getopt->getValues();
        $this->assertSame($expect, $actual);
    }

    public function testMultipleWithAlias()
    {
        $options = array('f,foo*::');
        $this->getopt->setOptions($options);

        $result = $this->getopt->parseInput(array('-f', '-f', '-f', 'baz', '-f', 'dib', '-f'));
        $this->assertTrue($result);

        $expect = array(
            '-f' => array(true, true, 'baz', 'dib', true),
            '--foo' => array(true, true, 'baz', 'dib', true),
        );
        $actual = $this->getopt->getValues();
        $this->assertSame($expect, $actual);
    }
}
