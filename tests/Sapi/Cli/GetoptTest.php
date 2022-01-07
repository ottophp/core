<?php
namespace Otto\Sapi\Cli;

class GetoptTest extends \PHPUnit\Framework\TestCase
{
    public function testParse_noOptions()
    {
        $getopt = new Getopt();
        $input = ['abc', 'def'];
        $actual = $getopt->parse($input);
        $this->assertEmpty($actual);
        $expect = ['abc', 'def'];
        $this->assertSame($expect, $input);
    }

    public function testParse_undefinedOption()
    {
        $getopt = new Getopt();
        $input = ['-z', 'def'];
        $this->expectException(Exception\OptionNotDefined::CLASS);
        $this->expectExceptionMessage("The option '-z' is not defined.");
        $getopt->parse($input);
    }

    public function testParse_longRejected()
    {
        $getopt = new Getopt([
            new Option('foo-bar'),
        ]);

        $input = ['--foo-bar'];
        $actual = $getopt->parse($input);
        $expect = ['foo-bar' => true];
        $this->assertSame($expect, $actual);

        $input = ['--foo-bar=baz'];
        $this->expectException(Exception\OptionParamRejected::CLASS);
        $this->expectExceptionMessage("The option '--foo-bar' does not accept a parameter.");
        $getopt->parse($input);
    }

    public function testParse_longRequired()
    {
        $getopt = new Getopt([
            new Option('foo-bar:')
        ]);

        // '=' as separator
        $input = ['--foo-bar=baz'];
        $actual = $getopt->parse($input);
        $expect = ['foo-bar' => 'baz'];
        $this->assertSame($expect, $actual);

        // ' ' as separator
        $input = ['--foo-bar', 'baz'];
        $actual = $getopt->parse($input);
        $this->assertSame($expect, $actual);

        // missing required value
        $input = ['--foo-bar'];
        $this->expectException(Exception\OptionParamRequired::CLASS);
        $this->expectExceptionMessage("The option '--foo-bar' requires a parameter.");
        $getopt->parse($input);
    }

    public function testParse_longOptional()
    {
        $getopt = new Getopt([
            new Option('foo-bar::')
        ]);

        $input = ['--foo-bar'];
        $actual = $getopt->parse($input);
        $expect = ['foo-bar' => true];
        $this->assertSame($expect, $actual);

        $input = ['--foo-bar=baz'];
        $actual = $getopt->parse($input);
        $expect = ['foo-bar' => 'baz'];
        $this->assertSame($expect, $actual);
    }

    public function testParse_longMultiple()
    {
        $getopt = new Getopt([
            new Option('foo-bar*::')
        ]);

        $input = [
            '--foo-bar',
            '--foo-bar',
            '--foo-bar=baz',
            '--foo-bar=dib',
            '--foo-bar'
        ];
        $actual = $getopt->parse($input);
        $expect = ['foo-bar' => [true, true, 'baz', 'dib', true]];
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortRejected()
    {
        $getopt = new Getopt([
            new Option('f')
        ]);

        $input = ['-f'];
        $actual = $getopt->parse($input);
        $expect = ['f' => true];
        $this->assertSame($expect, $actual);

        $input = ['-f', 'baz'];
        $actual = $getopt->parse($input);
        $expect = ['f' => true];
        $this->assertSame($expect, $actual);
        $this->assertSame(['baz'], $input);
    }

    public function testParse_shortRequired()
    {
        $getopt = new Getopt([
            new Option('f:')
        ]);

        $input = ['-f', 'baz'];
        $actual = $getopt->parse($input);
        $expect = ['f' => 'baz'];
        $this->assertSame($expect, $actual);

        $input = ['-f'];
        $this->expectException(Exception\OptionParamRequired::CLASS);
        $this->expectExceptionMessage("The option '-f' requires a parameter.");
        $getopt->parse($input);
    }

    public function testParse_shortOptional()
    {
        $getopt = new Getopt([
            new Option('f::')
        ]);

        $input = ['-f'];
        $actual = $getopt->parse($input);
        $expect = ['f' => true];
        $this->assertSame($expect, $actual);

        $input = ['-f', 'baz'];
        $actual = $getopt->parse($input);
        $expect = ['f' => 'baz'];
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortMultiple()
    {
        $getopt = new Getopt([
            new Option('f*::')
        ]);

        $input = ['-f', '-f', '-f', 'baz', '-f', 'dib', '-f'];
        $actual = $getopt->parse($input);
        $expect = ['f' => [true, true, 'baz', 'dib', true]];
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortCluster()
    {
        $getopt = new Getopt([
            new Option('f'),
            new Option('b'),
            new Option('z'),
        ]);

        $input = ['-fbz'];
        $actual = $getopt->parse($input);
        $expect = [
            'f' => true,
            'b' => true,
            'z' => true,
        ];
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortClusterRequired()
    {
        $getopt = new Getopt([
            new Option('f'),
            new Option('b:'),
            new Option('z'),
        ]);

        $input = ['-fbz'];
        $this->expectException(Exception\OptionParamRequired::CLASS);
        $this->expectExceptionMessage("The option '-b' requires a parameter.");
        $getopt->parse($input);
    }

    public function testParseAndGet()
    {
        $getopt = new Getopt([
            new Option('foo-bar:'),
            new Option('b'),
            new Option('z::'),
        ]);

        $input = [
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
        ];

        $actual = $getopt->parse($input);

        $expectOptv = [
            'foo-bar' => 'zim',
            'z' => 'qux',
            'b' => true,
        ];

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

        $input = ['-f', '-f', '-f', 'baz', '-f', 'dib', '-f'];
        $actual = $getopt->parse($input);
        $expect = [
            'f' => [true, true, 'baz', 'dib', true],
            'foo' => [true, true, 'baz', 'dib', true],
        ];
        $this->assertSame($expect, $actual);
    }
}
