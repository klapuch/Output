<?php
declare(strict_types = 1);

namespace Klapuch\Output;

/**
 * Values printed in JSON format
 */
final class Json implements Format {
	/** @var mixed[] */
	private $values;

	public function __construct(array $values = []) {
		$this->values = $values;
	}

	/**
	 * @param mixed $tag
	 * @param mixed|null $content
	 * @return \Klapuch\Output\Format
	 */
	public function with($tag, $content = null): Format {
		if (is_array($content))
			return new self([$tag => $content + ($this->values[$tag] ?? [])] + $this->values);
		return new self([$tag => $content] + $this->values);
	}

	/**
	 * @param mixed|null $tag
	 * @param callable $adjustment
	 * @return \Klapuch\Output\Format
	 */
	public function adjusted($tag, callable $adjustment): Format {
		if ($tag === null)
			return new self(call_user_func($adjustment, $this->values));
		return new self(
			[$tag => call_user_func($adjustment, $this->values[$tag])] + $this->values
		);
	}

	public function serialization(): string {
		return (string) json_encode($this->values, JSON_PRETTY_PRINT);
	}
}
