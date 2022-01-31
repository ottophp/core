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
        $arguments = $this->getopt->parse($input, $options);
        $this->assertEmpty($options->getValues());
        $expect = ['abc', 'def'];
        $this->assertSame($expect, $input);
    }

    public function testParse_undefinedOption()
    {
        $options = new Options();
        $input = ['-z', 'def'];
        $this->expectException(Exception\OptionNotDefined::CLASS);
        $this->expectExceptionMessage("-z is not defined.");
        $this->getopt->parse($input, $options);
    }

    public function testParse_longRejected()
    {
        $options = new Options([
            new Option('foo-bar'),
        ]);
        $input = ['--foo-bar'];
        $arguments = $this->getopt->parse($input, $options);
        $expect = ['foo-bar' => true];
        $this->assertSame($expect, $options->getValues());

        $options = new Options([
            new Option('foo-bar'),
        ]);
        $input = ['--foo-bar=baz'];
        $this->expectException(Exception\OptionParamRejected::CLASS);
        $this->expectExceptionMessage("--foo-bar does not accept an argument.");
        $this->getopt->parse($input, $options);
    }

    public function testParse_longRequired()
    {

        // '=' as separator
        $options = new Options([
            new Option('foo-bar', argument: Option::REQUIRED)
        ]);
        $input = ['--foo-bar=baz'];
        $arguments = $this->getopt->parse($input, $options);
        $expect = ['foo-bar' => 'baz'];
        $this->assertSame($expect, $options->getValues());

        // ' ' as separator
        $options = new Options([
            new Option('foo-bar', argument: Option::REQUIRED)
        ]);
        $input = ['--foo-bar', 'baz'];
        $arguments = $this->getopt->parse($input, $options);
        $this->assertSame($expect, $options->getValues());

        // missing required value
        $options = new Options([
            new Option('foo-bar', argument: Option::REQUIRED)
        ]);
        $input = ['--foo-bar'];
        $this->expectException(Exception\OptionParamRequired::CLASS);
        $this->expectExceptionMessage("--foo-bar requires an argument.");
        $this->getopt->parse($input, $options);
    }

    public function testParse_longOptional()
    {
        $options = new Options([
            new Option('foo-bar', argument: Option::OPTIONAL)
        ]);
        $input = ['--foo-bar'];
        $arguments = $this->getopt->parse($input, $options);
        $expect = ['foo-bar' => true];
        $this->assertSame($expect, $options->getValues());

        $options = new Options([
            new Option('foo-bar', argument: Option::OPTIONAL)
        ]);
        $input = ['--foo-bar=baz'];
        $arguments = $this->getopt->parse($input, $options);
        $expect = ['foo-bar' => 'baz'];
        $this->assertSame($expect, $options->getValues());
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
        $arguments = $this->getopt->parse($input, $options);
        $expect = ['foo-bar' => [true, true, 'baz', 'dib', true]];
        $this->assertSame($expect, $options->getValues());
    }

    public function testParse_shortRejected()
    {
        $options = new Options([
            new Option('f')
        ]);
        $input = ['-f'];
        $arguments = $this->getopt->parse($input, $options);
        $expect = ['f' => true];
        $this->assertSame($expect, $options->getValues());

        $options = new Options([
            new Option('f')
        ]);
        $input = ['-f', 'baz'];
        $arguments = $this->getopt->parse($input, $options);
        $expect = ['f' => true];
        $this->assertSame($expect, $options->getValues());
        $this->assertSame(['baz'], $arguments);
    }

    public function testParse_shortRequired()
    {
        $options = new Options([
            new Option('f', argument: Option::REQUIRED)
        ]);
        $input = ['-f', 'baz'];
        $arguments = $this->getopt->parse($input, $options);
        $expect = ['f' => 'baz'];
        $this->assertSame($expect, $options->getValues());

        $options = new Options([
            new Option('f', argument: Option::REQUIRED)
        ]);
        $input = ['-f'];
        $this->expectException(Exception\OptionParamRequired::CLASS);
        $this->expectExceptionMessage("-f requires an argument.");
        $this->getopt->parse($input, $options);
    }

    public function testParse_shortOptional()
    {
        $options = new Options([
            new Option('f', argument: Option::OPTIONAL)
        ]);
        $input = ['-f'];
        $arguments = $this->getopt->parse($input, $options);
        $expect = ['f' => true];
        $this->assertSame($expect, $options->getValues());

        $options = new Options([
            new Option('f', argument: Option::OPTIONAL)
        ]);
        $input = ['-f', 'baz'];
        $arguments = $this->getopt->parse($input, $options);
        $expect = ['f' => 'baz'];
        $this->assertSame($expect, $options->getValues());
    }

    public function testParse_shortMultiple()
    {
        $options = new Options([
            new Option('f', argument: Option::OPTIONAL, multiple: true)
        ]);

        $input = ['-f', '-f', '-f', 'baz', '-f', 'dib', '-f'];
        $arguments = $this->getopt->parse($input, $options);
        $expect = ['f' => [true, true, 'baz', 'dib', true]];
        $this->assertSame($expect, $options->getValues());
    }

    public function testParse_shortCluster()
    {
        $options = new Options([
            new Option('f'),
            new Option('b'),
            new Option('z'),
        ]);

        $input = ['-fbz'];
        $arguments = $this->getopt->parse($input, $options);
        $expect = [
            'f' => true,
            'b' => true,
            'z' => true,
        ];
        $this->assertSame($expect, $options->getValues());
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
        $this->expectExceptionMessage("-b requires an argument.");
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

        $arguments = $this->getopt->parse($input, $options);

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

        $this->assertSame($expectOptv, $options->getValues());
        $this->assertSame($expectArgv, $arguments);
    }

    public function testMultipleWithAlias()
    {
        $options = new Options([
            new Option('-f|--foo', argument: Option::OPTIONAL, multiple: true)
        ]);

        $input = ['-f', '-f', '-f', 'baz', '-f', 'dib', '-f'];
        $arguments = $this->getopt->parse($input, $options);
        $expect = [
            'f' => [true, true, 'baz', 'dib', true],
            'foo' => [true, true, 'baz', 'dib', true],
        ];
        $this->assertSame($expect, $options->getValues());
    }
}
