<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Values printed in JSON format
 */
final class Json implements Format {
	private $values;

	public function __construct(array $values = []) {
		$this->values = $values;
	}

	public function with($tag, $content = null): Format {
		if (is_array($content))
			return new self([$tag => $content + ($this->values[$tag] ?? [])] + $this->values);
		return new self([$tag => $content] + $this->values);
	}

	public function adjusted($tag, callable $adjustment): Format {
		return new self(
			[$tag => call_user_func($adjustment, $this->values[$tag])] + $this->values
		);
	}

	public function serialization(): string {
		return json_encode($this->values, JSON_PRETTY_PRINT);
	}
}