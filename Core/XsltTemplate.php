<?php
declare(strict_types = 1);
namespace Klapuch\Output;

final class XsltTemplate implements Template {
    private $template;
    private $stylesheet;

    public function __construct(string $template, Format $stylesheet) {
        $this->template = $template;
        $this->stylesheet = $stylesheet;
    }

    public function render(array $variables = []): string {
        $xsl = new \DOMDocument();
        $xsl->load($this->template);
        $xslt = new \XSLTProcessor();
        $xslt->setParameter('', $variables);
        $xslt->importStylesheet($xsl);
        $xml = new \DOMDocument();
        $xml->loadXml((string)$this->stylesheet);
        return $xslt->transformToXml($xml);
    }
}
