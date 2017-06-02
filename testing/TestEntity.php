<?php
	use DaybreakStudios\Doze\Entity\EntityInterface;

	class TestEntity implements EntityInterface {
		private static $autoIncrementId = 1;

		private $id;
		private $name;
		private $otherEntity;

		public function __construct() {
			$this->id = self::$autoIncrementId++;
			$this->name = uniqid('', true);

			if ($this->id === 1)
				$this->otherEntity = new TestEntity();
		}

		public function getId() {
			return $this->id;
		}

		/**
		 * @return string
		 */
		public function getName() {
			return $this->name;
		}

		/**
		 * @return TestEntity
		 */
		public function getOtherEntity() {
			return $this->otherEntity;
		}
	}