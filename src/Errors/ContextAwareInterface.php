<?php
	namespace DaybreakStudios\Doze\Errors;

	interface ContextAwareInterface {
		/**
		 * @return array
		 */
		public function getContext();
	}