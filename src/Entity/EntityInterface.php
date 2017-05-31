<?php
	namespace DaybreakStudios\Doze\Entity;

	/**
	 * An interface to identify database entities. For use with the EntityNormalizer.
	 *
	 * @package DaybreakStudios\Doze\Entity
	 */
	interface EntityInterface {
		/**
		 * @return mixed|null
		 */
		public function getId();
	}