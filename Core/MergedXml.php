<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Merged multiple XML files into one
 */
final class MergedXml implements Format {
    private $root;
    private $elements;

    public function __construct(
        \DOMDocument $root,
        \SimpleXMLElement ...$elements
    ) {
        $this->root = $root;
        $this->elements = $elements;
    }

    public function with(string $tag, $value = null): Format {
        $elements = array_merge(
            $this->elements,
            [
                new \SimpleXMLElement(
                    sprintf('<%1$s>%2$s</%1$s>', $tag, $value)
                )
            ]
        );
        return new self($this->root, ...$elements);
    }

    public function __toString(): string {
        $rootElement = simplexml_import_dom($this->root);
        foreach($this->elements as $element) {
            $rootElement->addChild(
                $element->getName(),
                $this->withoutElement(
                    $element->getName(),
                    $this->withoutDeclaration(
                        $this->withoutWhiteSpaces($element->saveXML())
                    )
                )
            );
        }
        return $this->withoutWhiteSpaces($rootElement->saveXML());
    }

    /**
     * XML string without declaration <?xml ... ?>
     * @param string $xml
     * @return string
     */
    private function withoutDeclaration(string $xml): string {
        return preg_replace('~^.+\n~', '', $xml);
    }

    /**
     * XML string without tabs, spaces, new lines or BOMs
     * @param string $xml
     * @return string
     */
    private function withoutWhiteSpaces(string $xml): string {
        $bom = pack('H*','EFBBBF');
        return preg_replace("~^$bom~", '', trim($xml));
    }

    /**
     * XML string without both elements (starting and ending)
     * @param string $element
     * @param string $xml
     * @return string
     */
    private function withoutElement(string $element, string $xml): string {
        if(strpos($xml, sprintf('<%s/>', $element)) !== false)
            return str_replace(sprintf('<%s/>', $element), '', $xml);
        return substr(
            substr($xml, 0, (strlen($element) + 3) * -1),
            strlen($element) + 2
        );
    }
}
