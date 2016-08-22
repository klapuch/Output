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
        foreach($this->elements as $element) {
            $fragment = $this->root->createDocumentFragment();     
            $fragment->appendXML(
                $this->withoutDeclaration(
                    $this->withoutWhiteSpaces($element->saveXML())
                )
            );
            $this->root->documentElement->appendChild($fragment);
        }
        return $this->withoutWhiteSpaces($this->root->saveXML());
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
}
