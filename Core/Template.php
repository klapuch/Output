<?php
declare(strict_types = 1);
namespace Klapuch\Output;

interface Template {
    /**
     * Render the template
     * @return string
     */
    public function render(): string;
}
