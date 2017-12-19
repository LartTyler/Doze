<?php
	namespace DaybreakStudios\Doze\Utility;

	/**
	 * Contains methods that are useful for working with Doctrine's collections library.
	 *
	 * @package DaybreakStudios\Doze\Utility
	 */
	final class CollectionUtil {
		/**
		 * @param mixed $item
		 *
		 * @return bool
		 */
		public static function isIterable($item) {
			return is_array($item) || $item instanceof \Traversable;
		}
	}