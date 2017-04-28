<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Values printed in XML format
 */
final class Xml implements Format {
	const EMPTY_XML = '';
	const EMPTY_MATCH = [];
	private $values;
	private $root;

	public function __construct(array $values, string $root = null) {
		$this->values = $values;
		$this->root = $root;
	}

	public function with($tag, $content = null): Format {
		if($content === null)
			return new self([$tag => $this->values], $this->root);
		return new self($this->values + [$tag => $content], $this->root);
	}

	public function adjusted($tag, callable $adjustment): Format {
		if(!$this->adjustable($tag, $this->values))
			return $this;
		return new self(
			[$tag => call_user_func($adjustment, $this->values[$tag])] + $this->values,
			$this->root
		);
	}

	public function serialization(): string {
		if($this->root === null) {
			return array_reduce(
				array_keys($this->values),
				function(string $xml, string $tag): string {
					$xml .= $this->element(
						$tag,
						$this->isParent($this->values[$tag])
						? (new self($this->values[$tag]))->serialization()
						: $this->toXml($this->cast($this->values[$tag]))
					);
					return $xml;
				},
				self::EMPTY_XML
			);
		}
		return $this->element(
			$this->root,
			(new self($this->values))->serialization()
		);
	}

	/**
	 * Element with tag and its value
	 * If the tag is numeric, skip it as it is not allowed
	 * @param string $tag
	 * @param string|self $content
	 * @return string
	 */
	private function element(string $tag, $content) {
		return is_numeric($tag)
			? $content
			: sprintf('<%1$s>%2$s</%1$s>', $tag, $content);
	}

	/**
	 * Check if the given element is parent of childrens
	 * Faster version of is_array (because of the recursion)
	 * @param mixed $content
	 * @return bool
	 */
	private function isParent($element): bool {
		return $element === (array)$element;
	}

	/**
	 * Cast the value to be proper XML and XSD proof
	 * @param mixed $value
	 * @return string
	 */
	private function cast($value): string {
		if(is_bool($value)) {
			return $value ? 'true' : 'false';
		}
		return (string)$value;
	}

	/**
	 * Value satisfying XML standards
	 * @param string $content
	 * @return string
	 */
	private function toXml(string $content): string {
		return htmlspecialchars($content, ENT_QUOTES | ENT_XML1, 'UTF-8');
	}

	/**
	 * Is the tag adjustable?
	 * @param string $tag
	 * @param array $values
	 * @return bool
	 */
	private function adjustable(string $tag, array $values): bool {
		return isset($values[$tag]);
	}
}