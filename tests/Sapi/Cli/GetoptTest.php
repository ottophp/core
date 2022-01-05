<?php
namespace Otto\Sapi\Cli;

class GetoptTest extends \PHPUnit\Framework\TestCase
{
    public function testParse_noOptions()
    {
        $getopt = new Getopt();
        $actual = $getopt->parseInput(array('abc', 'def'));
        $expect = array('abc', 'def');
        $this->assertSame($expect, $actual);
    }

    public function testParse_undefinedOption()
    {
        $getopt = new Getopt();
        $this->expectException(Exception\OptionNotDefined::CLASS);
        $this->expectExceptionMessage("The option '-z' is not defined.");
        $getopt->parseInput(array('-z', 'def'));
    }

    public function testParse_longRejected()
    {
        $getopt = new Getopt([
            new Option('foo-bar'),
        ]);

        $actual = $getopt->parseInput(array('--foo-bar'));
        $expect = array('--foo-bar' => true);
        $this->assertSame($expect, $actual);

        $this->expectException(Exception\OptionParamRejected::CLASS);
        $this->expectExceptionMessage("The option '--foo-bar' does not accept a parameter.");
        $getopt->parseInput(array('--foo-bar=baz'));
    }

    public function testParse_longRequired()
    {
        $getopt = new Getopt([
            new Option('foo-bar:')
        ]);


        // '=' as separator
        $actual = $getopt->parseInput(array('--foo-bar=baz'));
        $expect = array('--foo-bar' => 'baz');
        $this->assertSame($expect, $actual);

        // ' ' as separator
        $actual = $getopt->parseInput(array('--foo-bar', 'baz'));
        $expect = array('--foo-bar' => 'baz');
        $this->assertSame($expect, $actual);

        // missing required value
        $this->expectException(Exception\OptionParamRequired::CLASS);
        $this->expectExceptionMessage("The option '--foo-bar' requires a parameter.");
        $getopt->parseInput(array('--foo-bar'));
    }

    public function testParse_longOptional()
    {
        $getopt = new Getopt([
            new Option('foo-bar::')
        ]);

        $actual = $getopt->parseInput(array('--foo-bar'));
        $expect = array('--foo-bar' => true);
        $this->assertSame($expect, $actual);

        $actual = $getopt->parseInput(array('--foo-bar=baz'));
        $expect = array('--foo-bar' => 'baz');
        $this->assertSame($expect, $actual);
    }

    public function testParse_longMultiple()
    {
        $getopt = new Getopt([
            new Option('foo-bar*::')
        ]);

        $actual = $getopt->parseInput(array(
            '--foo-bar',
            '--foo-bar',
            '--foo-bar=baz',
            '--foo-bar=dib',
            '--foo-bar'
        ));
        $expect = array('--foo-bar' => array(true, true, 'baz', 'dib', true));
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortRejected()
    {
        $getopt = new Getopt([
            new Option('f')
        ]);

        $actual = $getopt->parseInput(array('-f'));
        $expect = array('-f' => true);
        $this->assertSame($expect, $actual);

        $actual = $getopt->parseInput(array('-f', 'baz'));
        $expect = array('-f' => true, 'baz');
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortRequired()
    {
        $getopt = new Getopt([
            new Option('f:')
        ]);

        $actual = $getopt->parseInput(array('-f', 'baz'));
        $expect = array('-f' => 'baz');
        $this->assertSame($expect, $actual);

        $this->expectException(Exception\OptionParamRequired::CLASS);
        $this->expectExceptionMessage("The option '-f' requires a parameter.");
        $getopt->parseInput(array('-f'));
    }

    public function testParse_shortOptional()
    {
        $getopt = new Getopt([
            new Option('f::')
        ]);

        $actual = $getopt->parseInput(array('-f'));
        $expect = array('-f' => true);
        $this->assertSame($expect, $actual);

        $actual = $getopt->parseInput(array('-f', 'baz'));
        $expect = array('-f' => 'baz');
        $this->assertSame($expect, $actual);
    }

    public function testParse_shortMultiple()
    {
        $getopt = new Getopt([
            new Option('f*::')
        ]);

        $actual = $getopt->parseInput(array('-f', '-f', '-f', 'baz', '-f', 'dib', '-f'));
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

        $actual = $getopt->parseInput(array('-fbz'));
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
        $getopt->parseInput(array('-fbz'));
    }

    public function testParseAndGet()
    {
        $getopt = new Getopt([
            new Option('foo-bar:'),
            new Option('b'),
            new Option('z::'),
        ]);

        $actual = $getopt->parseInput(array(
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
        $this->assertSame($expect, $actual);
    }

    public function testMultipleWithAlias()
    {
        $getopt = new Getopt([
            new Option('f,foo*::'),
        ]);

        $actual = $getopt->parseInput(array('-f', '-f', '-f', 'baz', '-f', 'dib', '-f'));
        $expect = array(
            '-f' => array(true, true, 'baz', 'dib', true),
            '--foo' => array(true, true, 'baz', 'dib', true),
        );
        $this->assertSame($expect, $actual);
    }


    // public function testSetOptions()
    // {
    //     $options = array(
    //         'foo-bar,f*:',
    //         'baz-dib,b::',
    //         'z,zim-gir',
    //     );

    //     $getopt->setOptions($options);
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

    //     $actual = $getopt->getOptions();
    //     $this->assertEquals($expect, $actual);

    //     // get an aliased option
    //     $actual = $getopt->getOption('--zim-gir');
    //     $this->assertEquals($expect['-z'], $actual);
    // }
}
