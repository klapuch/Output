<?php
/**
 * @testCase
 * @phpVersion > 7.0
 */
namespace Klapuch\Unit\Output;

use Klapuch\Output;
use Tester;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

final class RemoteXml extends Tester\TestCase {
    public function testReadingFromExistingSource() {
        Assert::contains(
            '<root><content>a</content></root>',
            (string)new Output\RemoteXml(
                Tester\FileMock::create(
                    '<root><content>a</content></root>'
                )
            )
        );
    }

    public function testReadingWithCorrectEncoding() {
        Assert::contains(
            '<root>Příliš žluťoučký kůň úpěl ďábelské ódy.</root>',
            (string)new Output\RemoteXml(
                Tester\FileMock::create(
                    '<root>Příliš žluťoučký kůň úpěl ďábelské ódy.</root>'
                )
            )
        );
    }

    public function testAddingNewTag() {
        Assert::contains(
            '<outer>OUTER</outer>',
            (string)(new Output\RemoteXml(
                Tester\FileMock::create(
                    '<root><content>a</content></root>'
                )
            ))->with('outer', 'OUTER')
        );
    }
}

(new RemoteXml())->run();
