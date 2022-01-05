<?php
namespace Otto\Sapi\Cli;

class GetoptTest extends \PHPUnit\Framework\TestCase
{
    public function testParse_noOptions()
    {
        $getopt = new Getopt();
        $input = array('abc', 'def');
        $actual = $getopt->parse($input);
        $this->assertEmpty($actual);
        $expect = array('abc', 'def');
        $this->assertSame($expect, $input);
    }

    public function testParse_undefinedOption()
    {
        $getopt = new Getopt();
        $this->expectException(Exception\OptionNotDefined::CLASS);
        $this->expectExceptionMessage("The option '-z' is not defined.");
        $input = ['-z', 'def'];
        $getopt->parse($input);
    }

    public function testParse_longRejected()
    {
        $getopt = new Getopt([
            new Option('foo-bar'),
        ]);

        $input = array('--foo-bar');
        $actual = $getopt->parse($input);
        $expect = array('--foo-bar' => true);
        $this->assertSame($expect, $actual);

        $this->expectException(Exception\OptionParamRejected::CLASS);
        $this->expectExceptionMessage("The option '--foo-bar' does not accept a parameter.");
        $input = array('--foo-bar=baz');
        $getopt->parse($input);
    }

    public function testParse_longRequired()
    {
        $getopt = new Getopt([
            new Option('foo-bar:')
        ]);


        // '=' as separator
        $input = array('--foo-bar=baz');
        $actual = $getopt->parse($input);
        $expect = array('--foo-bar' => 'baz');
        $this->assertSame($expect, $actual);

        // ' ' as separator
        $input = array('--foo-bar', 'baz');
        $actual = $getopt->parse($input);
        $expect = array('--foo-bar' => 'baz');
        $this->assertSame($expect, $actual);

        // missing required value
        $this->expectException(Exception\OptionParamRequired::CLASS);
        $this->expectExceptionMessage("The option '--foo-bar' requires a parameter.");
        $input = array('--foo-bar');
        $getopt->parse($input);
    }

    public function testParse_longOptional()
    {
        $getopt = new Getopt([
            new Option('foo-bar::')
        ]);

        $input = array('--foo-bar');
        $actual = $getopt->parse($input);
        $expect = array('--foo-bar' => true);
        $this->assertSame($expect, $actual);

        $input = array('--foo-bar=baz');
        $actual = $getopt->parse($input);
        $expect = array('--foo-bar' => 'baz');
        $this->assertSame($expect, $actual);
    }

    public function testParse_longMultiple()
    {
        $getopt = new Getopt([
            new Option('foo-bar*::')
        ]);

        $input = array(
            '--foo-bar',
            '--foo-bar',
            '--foo-bar=baz',
            '--foo-bar=dib',
            '--foo-bar'
        );
        $actual = $getopt->parse($input);
        $expect = array('--foo-bar' => array(true, true, 'baz', 'dib', true));
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortRejected()
    {
        $getopt = new Getopt([
            new Option('f')
        ]);

        $input = array('-f');
        $actual = $getopt->parse($input);
        $expect = array('-f' => true);
        $this->assertSame($expect, $actual);

        $input = array('-f', 'baz');
        $actual = $getopt->parse($input);
        $expect = array('-f' => true);
        $this->assertSame($expect, $actual);
        $this->assertSame(['baz'], $input);
    }

    public function testParse_shortRequired()
    {
        $getopt = new Getopt([
            new Option('f:')
        ]);

        $input = array('-f', 'baz');
        $actual = $getopt->parse($input);
        $expect = array('-f' => 'baz');
        $this->assertSame($expect, $actual);

        $this->expectException(Exception\OptionParamRequired::CLASS);
        $this->expectExceptionMessage("The option '-f' requires a parameter.");
        $input = array('-f');
        $getopt->parse($input);
    }

    public function testParse_shortOptional()
    {
        $getopt = new Getopt([
            new Option('f::')
        ]);

        $input = array('-f');
        $actual = $getopt->parse($input);
        $expect = array('-f' => true);
        $this->assertSame($expect, $actual);

        $input = array('-f', 'baz');
        $actual = $getopt->parse($input);
        $expect = array('-f' => 'baz');
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortMultiple()
    {
        $getopt = new Getopt([
            new Option('f*::')
        ]);

        $input = array('-f', '-f', '-f', 'baz', '-f', 'dib', '-f');
        $actual = $getopt->parse($input);
        $expect = array('-f' => array(true, true, 'baz', 'dib', true));
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortCluster()
    {
        $getopt = new Getopt([
            new Option('f'),
            new Option('b'),
            new Option('z'),
        ]);

        $input = array('-fbz');
        $actual = $getopt->parse($input);
        $expect = array(
            '-f' => true,
            '-b' => true,
            '-z' => true,
        );
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortClusterRequired()
    {
        $getopt = new Getopt([
            new Option('f'),
            new Option('b:'),
            new Option('z'),
        ]);

        $this->expectException(Exception\OptionParamRequired::CLASS);
        $this->expectExceptionMessage("The option '-b' requires a parameter.");
        $input = array('-fbz');
        $getopt->parse($input);
    }

    public function testParseAndGet()
    {
        $getopt = new Getopt([
            new Option('foo-bar:'),
            new Option('b'),
            new Option('z::'),
        ]);

        $input = array(
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
        );

        $actual = $getopt->parse($input);

        $expectOptv = array(
            '--foo-bar' => 'zim',
            '-z' => 'qux',
            '-b' => true,
        );

        $expectArgv = [
            'abc',
            'def',
            'gir',
            '--after-double-dash=123',
            '-n',
            '456',
            'ghi',
        ];

        $this->assertSame($expectOptv, $actual);
        $this->assertSame($expectArgv, $input);
    }

    public function testMultipleWithAlias()
    {
        $getopt = new Getopt([
            new Option('f,foo*::'),
        ]);

        $input = array('-f', '-f', '-f', 'baz', '-f', 'dib', '-f');
        $actual = $getopt->parse($input);
        $expect = array(
            '-f' => array(true, true, 'baz', 'dib', true),
            '--foo' => array(true, true, 'baz', 'dib', true),
        );
        $this->assertSame($expect, $actual);
    }
}
