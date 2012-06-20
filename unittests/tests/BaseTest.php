<?php

namespace HtmlBuilder;

class BaseTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	public function testBasics()
	{
		$this->assertEquals((string) hb('b')->appendText('This is a test'), '<b>This is a test</b>');
		$this->assertEquals((string) hb('i')->append(hb('u')->appendText('Another test')), '<i><u>Another test</u></i>');
		$this->assertEquals((string) hb('i')->append(hb('u'))->prepend(hb('b')), '<i><b></b><u></u></i>');

		$table = hb('table')->append(
			hb('thead')->append(hb('tr')
				->append(hb('th')->appendText('Col 1'))
				->append(hb('th')->appendText('Col 2'))
				->append(hb('th')->appendText('Col 3'))
				->append(hb('th')->appendText('Col 4'))
			)
		);

		$tbody = hb('tbody');
		for ($i = 0; $i < 5; $i++)
		{
			hb('tr')
				->append(hb('td')->appendText('Cell 1-' . $i))
				->append(hb('td')->appendText('Cell 2-' . $i))
				->append(hb('td')->appendText('Cell 3-' . $i))
				->append(hb('td')->appendText('Cell 4-' . $i))
				->appendTo($tbody);
		}

		$tbody->appendTo($table)->append(
			hb('tr')
				->append(hb('td')->appendText('Cell 1-' . $i))
				->append(hb('td')->appendText('Cell 2-' . $i))
				->append(hb('td')->appendText('Cell 3-' . $i))
				->append(hb('td')->appendText('Cell 4-' . $i))
		);

		$this->assertEquals((string) $table, '<table><thead><tr><th>Col 1</th><th>Col 2</th><th>Col 3</th><th>Col 4</th></tr></thead><tbody><tr><td>Cell 1-0</td><td>Cell 2-0</td><td>Cell 3-0</td><td>Cell 4-0</td></tr><tr><td>Cell 1-1</td><td>Cell 2-1</td><td>Cell 3-1</td><td>Cell 4-1</td></tr><tr><td>Cell 1-2</td><td>Cell 2-2</td><td>Cell 3-2</td><td>Cell 4-2</td></tr><tr><td>Cell 1-3</td><td>Cell 2-3</td><td>Cell 3-3</td><td>Cell 4-3</td></tr><tr><td>Cell 1-4</td><td>Cell 2-4</td><td>Cell 3-4</td><td>Cell 4-4</td></tr><tr><td>Cell 1-5</td><td>Cell 2-5</td><td>Cell 3-5</td><td>Cell 4-5</td></tr></tbody></table>');
	}

	public function testAttributes()
	{
		$attrs = array(
			'id' => 'foo',
			'class' => 'boo black sheep',
			'value' => 20,
		);

		$datattrs = array(
			'data-foo' => 'bar',
			'data-baz' => 'meh',
			'data-hate' => 'love',
			'value' => 30,
		);

		$obj = hb('div');

		foreach ($attrs AS $attr => $value)
		{
			$obj->attr($attr, $value);
		}

		$obj->attr($datattrs);

		$this->assertEquals((string)$obj, '<div id="foo" class="boo black sheep" value="30" data-foo="bar" data-baz="meh" data-hate="love"></div>');
		$this->assertEquals($obj->attr('id'), 'foo');
		$this->assertNull($obj->attr('adklsfjkladklf'));
	}

	public function testRecursion()
	{
		try
		{
			$b = hb('b');
			$b->append($b);
			$this->fail('Exception not thrown for simple recursive appending');
		}
		catch (HtmlBuilderException $e)
		{
		}

		try
		{
			$b = hb('b');
			$b->append(hb('u')->appendText('foo'))->append($u=hb('u'));
			$u->append($b);
			$this->fail('Exception not thrown for nested recursive appending');
		}
		catch (HtmlBuilderException $e)
		{
		}

		try
		{
			$b = hb('b');
			$b->appendTo($b);
			$this->fail('Exception not thrown for simple recursive appending');
		}
		catch (HtmlBuilderException $e)
		{
		}

		try
		{
			$b = hb('b');
			$b->prependTo($b);
			$this->fail('Exception not thrown for simple recursive prepending');
		}
		catch (HtmlBuilderException $e)
		{
		}

		try
		{
			$b = hb('b');
			$b->prepend($b);
			$this->fail('Exception not thrown for simple recursive prepending');
		}
		catch (HtmlBuilderException $e)
		{
		}

	}
}
