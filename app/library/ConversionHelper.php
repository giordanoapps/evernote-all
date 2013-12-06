<?php

class ConversionHelper
{
	/**
	 * Return all properties from object.
	 *
	 * @return array
	 */
	public static function getProperties($object)
	{
		return get_object_vars($object);
	}

	/**
	 * Transform the instance in an array.
	 */
	public static function toArray(&$object)
	{
		$object = ConversionHelper::getProperties($object);
	}

	/**
	 * Transform the instance in JSON.
	 */
	public static function toJson(&$object)
	{
		$object = json_encode
							(
								iconv("ISO-8859-1",
											"UTF-8",
											ConversionHelper::getProperties($object)
								)
							);
	}
}