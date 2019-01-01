<?php
declare(strict_types = 1);

namespace Klapuch\Output;

/**
 * Immutable array format
 */
final class ArrayFormat implements Format {
	/** @var mixed[] */
	private $values;

	public function __construct(array $values) {
		$this->values = $values;
	}

	/**
	 * @param mixed $key
	 * @param mixed|null $content
	 * @return \Klapuch\Output\Format
	 */
	public function with($key, $content = null): Format {
		return new self($this->values + [$key => $content]);
	}

	/**
	 * @param mixed $key
	 * @param callable $adjustment
	 * @return \Klapuch\Output\Format
	 */
	public function adjusted($key, callable $adjustment): Format {
		return new self(
			array_replace(
				$this->values,
				array_key_exists($key, $this->values)
				? [$key => call_user_func($adjustment, $this->values[$key])]
				: []
			)
		);
	}

	public function serialization(): string {
		return implode($this->values);
	}
}
