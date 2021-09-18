<?php
declare (strict_types = 1);
use PHPUnit\Framework\TestCase;
use Rasba\Html;

final class HtmlTest extends TestCase
{
    public function testHtmlBody(): void
    {
        $html = new Html(null, ["rasbajs" => false], null, []);
        $html->addBody($html->h1("Test"));
        $html->Html->addChild([
            $html->Head, $html->Body,
        ]);
        $this->assertEquals(
            $html->Html->html(),
            "<html><head></head><body><h1>Test</h1></body></html>"
        );
    }

    public function testHtmlHead(): void
    {
        $html = new Html(null, ["rasbajs" => false], null, []);
        $html->addHead($html->title("Test"));
        $html->Html->addChild([
            $html->Head, $html->Body,
        ]);
        $this->assertEquals(
            $html->Html->html(),
            "<html><head><title>Test</title></head><body></body></html>"
        );
    }
}
