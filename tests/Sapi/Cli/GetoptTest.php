<?php
namespace Otto\Sapi\Cli;

class GetoptTest extends TestCase
{
    protected function setUp() : void
    {
        parent::setUp();
        $this->getopt = new Getopt();
    }

    public function testParse_noOptions()
    {
        $options = new Options();
        $input = ['abc', 'def'];
        $actual = $this->getopt->parse($input, $options);
        $this->assertEmpty($actual);
        $expect = ['abc', 'def'];
        $this->assertSame($expect, $input);
    }

    public function testParse_undefinedOption()
    {
        $options = new Options();
        $input = ['-z', 'def'];
        $this->expectException(Exception\OptionNotDefined::CLASS);
        $this->expectExceptionMessage("The option '-z' is not defined.");
        $this->getopt->parse($input, $options);
    }

    public function testParse_longRejected()
    {
        $options = new Options([
            new Option('foo-bar'),
        ]);

        $input = ['--foo-bar'];
        $actual = $this->getopt->parse($input, $options);
        $expect = ['foo-bar' => true];
        $this->assertSame($expect, $actual);

        $input = ['--foo-bar=baz'];
        $this->expectException(Exception\OptionParamRejected::CLASS);
        $this->expectExceptionMessage("The option '--foo-bar' does not accept a parameter.");
        $this->getopt->parse($input, $options);
    }

    public function testParse_longRequired()
    {
        $options = new Options([
            new Option('foo-bar', argument: Option::REQUIRED)
        ]);

        // '=' as separator
        $input = ['--foo-bar=baz'];
        $actual = $this->getopt->parse($input, $options);
        $expect = ['foo-bar' => 'baz'];
        $this->assertSame($expect, $actual);

        // ' ' as separator
        $input = ['--foo-bar', 'baz'];
        $actual = $this->getopt->parse($input, $options);
        $this->assertSame($expect, $actual);

        // missing required value
        $input = ['--foo-bar'];
        $this->expectException(Exception\OptionParamRequired::CLASS);
        $this->expectExceptionMessage("The option '--foo-bar' requires a parameter.");
        $this->getopt->parse($input, $options);
    }

    public function testParse_longOptional()
    {
        $options = new Options([
            new Option('foo-bar', argument: Option::OPTIONAL)
        ]);

        $input = ['--foo-bar'];
        $actual = $this->getopt->parse($input, $options);
        $expect = ['foo-bar' => true];
        $this->assertSame($expect, $actual);

        $input = ['--foo-bar=baz'];
        $actual = $this->getopt->parse($input, $options);
        $expect = ['foo-bar' => 'baz'];
        $this->assertSame($expect, $actual);
    }

    public function testParse_longMultiple()
    {
        $options = new Options([
            new Option('foo-bar', argument: Option::OPTIONAL, multiple: true)
        ]);

        $input = [
            '--foo-bar',
            '--foo-bar',
            '--foo-bar=baz',
            '--foo-bar=dib',
            '--foo-bar'
        ];
        $actual = $this->getopt->parse($input, $options);
        $expect = ['foo-bar' => [true, true, 'baz', 'dib', true]];
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortRejected()
    {
        $options = new Options([
            new Option('f')
        ]);

        $input = ['-f'];
        $actual = $this->getopt->parse($input, $options);
        $expect = ['f' => true];
        $this->assertSame($expect, $actual);

        $input = ['-f', 'baz'];
        $actual = $this->getopt->parse($input, $options);
        $expect = ['f' => true];
        $this->assertSame($expect, $actual);
        $this->assertSame(['baz'], $input);
    }

    public function testParse_shortRequired()
    {
        $options = new Options([
            new Option('f', argument: Option::REQUIRED)
        ]);

        $input = ['-f', 'baz'];
        $actual = $this->getopt->parse($input, $options);
        $expect = ['f' => 'baz'];
        $this->assertSame($expect, $actual);

        $input = ['-f'];
        $this->expectException(Exception\OptionParamRequired::CLASS);
        $this->expectExceptionMessage("The option '-f' requires a parameter.");
        $this->getopt->parse($input, $options);
    }

    public function testParse_shortOptional()
    {
        $options = new Options([
            new Option('f', argument: Option::OPTIONAL)
        ]);

        $input = ['-f'];
        $actual = $this->getopt->parse($input, $options);
        $expect = ['f' => true];
        $this->assertSame($expect, $actual);

        $input = ['-f', 'baz'];
        $actual = $this->getopt->parse($input, $options);
        $expect = ['f' => 'baz'];
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortMultiple()
    {
        $options = new Options([
            new Option('f', argument: Option::OPTIONAL, multiple: true)
        ]);

        $input = ['-f', '-f', '-f', 'baz', '-f', 'dib', '-f'];
        $actual = $this->getopt->parse($input, $options);
        $expect = ['f' => [true, true, 'baz', 'dib', true]];
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortCluster()
    {
        $options = new Options([
            new Option('f'),
            new Option('b'),
            new Option('z'),
        ]);

        $input = ['-fbz'];
        $actual = $this->getopt->parse($input, $options);
        $expect = [
            'f' => true,
            'b' => true,
            'z' => true,
        ];
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortClusterRequired()
    {
        $options = new Options([
            new Option('f'),
            new Option('b', argument: Option::REQUIRED),
            new Option('z'),
        ]);

        $input = ['-fbz'];
        $this->expectException(Exception\OptionParamRequired::CLASS);
        $this->expectExceptionMessage("The option '-b' requires a parameter.");
        $this->getopt->parse($input, $options);
    }

    public function testParseAndGet()
    {
        $options = new Options([
            new Option('foo-bar', argument: Option::REQUIRED),
            new Option('b'),
            new Option('z', argument: Option::OPTIONAL),
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

        $actual = $this->getopt->parse($input, $options);

        $expectOptv = [
            'foo-bar' => 'zim',
            'b' => true,
            'z' => 'qux',
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
        $options = new Options([
            new Option('-f|--foo', argument: Option::OPTIONAL, multiple: true)
        ]);

        $input = ['-f', '-f', '-f', 'baz', '-f', 'dib', '-f'];
        $actual = $this->getopt->parse($input, $options);
        $expect = [
            'f' => [true, true, 'baz', 'dib', true],
            'foo' => [true, true, 'baz', 'dib', true],
        ];
        $this->assertSame($expect, $actual);
    }
}
