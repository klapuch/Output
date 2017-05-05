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

	public function with($tag, $content = null): Format {
		return $this->origin->with($tag, $content);
	}

	public function adjusted($tag, callable $adjustment): Format {
		return $this->origin->adjusted($tag, $adjustment);
	}

	public function serialization(): string {
		$previous = libxml_use_internal_errors(true);
		$xml = new \DOMDocument();
		$xml->loadXML($this->origin->serialization());
		$valid = $xml->schemaValidate($this->schema);
		$errors = $this->errors(...libxml_get_errors());
		libxml_use_internal_errors($previous);
		if ($valid)
			return $xml->saveXML();
		throw new \InvalidArgumentException(sprintf('XML is not valid: "%s"', $errors));
	}

	private function errors(\LibXMLError ...$errors): string {
		return implode(
			' | ',
			array_map(
				function(\LibXMLError $error): string {
					return trim($error->message);
				},
				$errors
			)
		);
	}
}