<?php
declare(strict_types = 1);

namespace Klapuch\Output;

/**
 * Fake format
 */
final class FakeFormat implements Format {
	/** @var string */
	private $output;

	public function __construct(string $output = '') {
		$this->output = $output;
	}

	/**
	 * @param mixed $tag
	 * @param mixed|null $value
	 * @return \Klapuch\Output\Format
	 */
	public function with($tag, $value = null): Format {
		return new self(sprintf('%s|%s|%s|', $this->serialization(), $tag, $value));
	}

	/**
	 * @param mixed $tag
	 * @param callable $adjustment
	 * @return \Klapuch\Output\Format
	 */
	public function adjusted($tag, callable $adjustment): Format {
		return $this;
	}

	public function serialization(): string {
		return $this->output;
	}
}
