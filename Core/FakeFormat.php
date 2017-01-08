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

	public function with(string $tag, $value = null): Format {
		return new self($this->serialize() . "|$tag|$value|");
	}

	public function serialize(): string {
		return $this->output;
	}
}