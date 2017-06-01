<?php
	namespace DaybreakStudios\Doze\Serializer;

	/**
	 * Class FieldSelector
	 *
	 * @package DaybreakStudios\Doze\Serializer
	 */
	class FieldSelector {
		/**
		 * @var string
		 */
		private $key;

		/**
		 * @var array
		 */
		private $subfields;

		/**
		 * @var bool
		 */
		private $root;

		/**
		 * @var bool
		 */
		private $selectAll;

		/**
		 * FieldSelector constructor.
		 *
		 * @param string $key
		 * @param array  $subfields
		 */
		public function __construct($key, $subfields) {
			$this->key = $key;
			$this->subfields = $subfields;

			$this->root = !$key;
			$this->selectAll = sizeof($subfields) === 1 && array_values($subfields)[0] === '*';
		}

		/**
		 * @return string
		 */
		public function getKey() {
			return $this->key;
		}

		/**
		 * @return array
		 */
		public function getSubfields() {
			return $this->subfields;
		}

		/**
		 * @return boolean
		 */
		public function isRoot() {
			return $this->root;
		}

		/**
		 * @return boolean
		 */
		public function isSelectAll() {
			return $this->selectAll;
		}
	}