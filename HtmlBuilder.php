<?php
namespace HtmlBuilder;

class Base
{
	static protected $objcounter = 0;
	protected $uniqid = null;
	protected $elem = null;
	protected $attributes = array();
	protected $children = array();

	public function __construct($elem)
	{
		$this->uniqid = ++self::$objcounter;
		$this->elem = $elem;
	}

	public function __toString()
	{
		$ret = '<' . $this->elem;

		foreach ($this->attributes AS $attr => $value)
			$ret .= ' ' . $attr . '="' . $value . '"';

		$ret .= '>';

		foreach ($this->children AS $child)
			$ret .= $child;

		$ret .= '</' . $this->elem . '>';

		return $ret;
	}

	/**
	 *
	 * @param Base $obj
	 */
	protected function recursive_check($obj)
	{
		$this->_recursive_check($obj);
		$obj->_recursive_check($this);
	}
	
	protected function _recursive_check($obj)
	{
		if ($this->uniqid == $obj->uniqid)
			throw new HtmlBuilderException('Can not recursivly add objects');

		foreach ($this->children AS $child)
		{
			$child->recursive_check($obj);
		}
	}

	public function append($obj)
	{
		$this->recursive_check($obj);
		$this->children[] = $obj;
		return $this;
	}

	public function appendTo($obj)
	{
		$obj->append($this);
		return $this;
	}

	public function appendText($text)
	{
		return $this->append(new Text($text));
	}

	public function prepend($obj)
	{
		$this->recursive_check($obj);
		array_unshift($this->children, $obj);
		return $this;
	}

	public function prependTo($obj)
	{
		$obj->prepend($this);
		return $this;
	}

	public function attr($attr, $value = null)
	{
		if ($value === null)
		{
			if (is_array($attr))
			{
				$this->attributes = array_merge($this->attributes, $attr);
			}
			elseif (isset($this->attributes[$attr]))
			{
				return $this->attributes[$attr];
			}
			else
			{
				return null;
			}
		}
		else
		{
			$this->attributes[$attr] = $value;
		}
	}
}

class Text extends Base
{
	public function __construct($text)
	{
		$this->uniqid = ++self::$objcounter;
		$this->elem = $text;
	}

	public function __toString()
	{
		return $this->elem;
	}
}

class SelfClosing extends Base
{
	public function __toString()
	{
		$ret = '<' . $this->elem;

		foreach ($this->attributes AS $attr => $value)
			$ret .= ' ' . $attr . '="' . $value . '"';

		$ret .= ' />';

		return $ret;
	}
	
	public function append($elem)
	{
		throw new HtmlBuilderException('Can not append a child node to a self closing element.');
	}

	public function prepend($elem)
	{
		throw new HtmlBuilderException('Can not prepend a child node to a self closing element.');
	}
}

class imgElement extends SelfClosing
{
	public function __construct()
	{
		parent::__construct('img');
	}
}

class HtmlBuilderException extends \Exception
{
}

function hb($elem)
{
	if (class_exists($classname = '\\HtmlBuilder\\' . strtolower($elem) . 'Element', false))
		return new $classname;
	return new Base($elem);
}
