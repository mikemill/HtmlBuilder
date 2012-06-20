<?php

namespace HtmlBuilder;

class SelfClosingTest extends \PHPUnit_Framework_TestCase
{
	public function testImage()
	{
		$img = hb('img');

		$this->assertEquals('HtmlBuilder\\imgElement', get_class($img));
		$this->assertEquals((string)$img, '<img />');
	}
}
