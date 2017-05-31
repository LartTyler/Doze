<?php
	namespace DaybreakStudios\Doze\Errors;

	interface ApiErrorInterface {
		/**
		 * @return string
		 */
		public function getCode();

		/**
		 * @return string
		 */
		public function getMessage();

		/**
		 * @return int|null
		 */
		public function getHttpStatus();
	}