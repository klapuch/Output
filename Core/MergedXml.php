<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Merged multiple XML elements into one
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

	public function with(string $tag, $content = null): Format {
		return new self(
			$this->root,
			...array_merge(
				$this->elements,
				[
					new \SimpleXMLElement(
						sprintf('<%1$s>%2$s</%1$s>', $tag, $content)
					)
				]
			)
		);
	}

	public function serialization(): string {
		foreach($this->elements as $element) {
			$fragment = $this->root->createDocumentFragment();     
			$fragment->appendXML(
				$this->withoutDeclaration(
					$this->withoutTrailingSpaces($element->saveXML())
				)
			);
			$this->root->documentElement->appendChild($fragment);
		}
		return $this->withoutTrailingSpaces($this->root->saveXML());
	}

	/**
	 * XML string without declaration <?xml ... ?>
	 * @param string $xml
	 * @return string
	 */
	private function withoutDeclaration(string $xml): string {
		return substr($xml, strpos($xml, '?>') + 2);
	}

	/**
	 * XML string without trailing tabs, spaces, new lines or BOMs
	 * @param string $xml
	 * @return string
	 */
	private function withoutTrailingSpaces(string $xml): string {
		$bom = pack('H*','EFBBBF');
		return preg_replace("~^$bom~", '', trim($xml));
	}
}