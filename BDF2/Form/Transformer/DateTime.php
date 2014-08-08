<?php
namespace BDF2\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class DateTime implements DataTransformerInterface
{

	protected $format;

	public function __construct($dataFormat) {
		$this->format = $dataFormat;
	}

	public function transform($dataTime) {
		if ($dataTime !== null)
		{
			return $dataTime->format($this->format);
		}
		
		return null;
	}

	public function reverseTransform($text) {
		return \DateTime::createFromFormat($this->format, $text);
	}

}
