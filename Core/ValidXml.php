<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Valid XML determined by XML Schema
 */
final class ValidXml implements Format {
	private $origin;
	private $schema;

	public function __construct(Format $origin, string $schema) {
		$this->origin = $origin;
		$this->schema = $schema;
	}

	public function with(string $tag, $content = null): Format {
		return $this->origin->with($tag, $content);
	}

	public function serialization(): string {
		$previous = libxml_use_internal_errors(true);
		$xml = new \DOMDocument();
		$xml->loadXML($this->origin->serialization());
		$valid = $xml->schemaValidate($this->schema);
		libxml_use_internal_errors($previous);
		if($valid)
			return $xml->saveXml();
		throw new \InvalidArgumentException('XML is not valid');
	}
}