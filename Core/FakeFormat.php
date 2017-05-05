<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Fake format
 */
final class FakeFormat implements Format {
	private $output;

	public function __construct(string $output = '') {
		$this->output = $output;
	}

	public function with($tag, $value = null): Format {
		return new self(sprintf('%s|%s|%s|', $this->serialization(), $tag, $value));
	}

	public function adjusted($tag, callable $adjustment): Format {
		return $this;
	}

	public function serialization(): string {
		return $this->output;
	}
}