<?php
	namespace DaybreakStudios\Doze\Serializer;

	use DaybreakStudios\Doze\Entity\EntityInterface;
	use DaybreakStudios\Doze\Utility\CollectionUtil;
	use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

	/**
	 * Allows normalization of objects that implement EntityInterface.
	 *
	 * This normalizer makes a few improvements on the standard ObjectNormalizer.
	 *    1. Circular references are automatically handled by returning the entity's ID.
	 *    2. References to other entities are indicated by their ID, rather than by following the relationship and
	 *        serializing child entities.
	 *    3. Iterables containing entities are also serialized in the same way as described in #2 for entities.
	 *
	 * @package DaybreakStudios\Doze\Serializer
	 * @see EntityInterface
	 */
	class EntityNormalizer extends ObjectNormalizer {
		/**
		 * {@inheritdoc}
		 */
		public function handleCircularReference($object) {
			if (!($object instanceof EntityInterface))
				return parent::handleCircularReference($object);

			return $object->getId();
		}

		/**
		 * {@inheritdoc}
		 */
		public function supportsNormalization($data, $format = null) {
			return is_object($data) && $data instanceof EntityInterface;
		}

		/**
		 * {@inheritdoc}
		 */
		protected function getAttributeValue($object, $attribute, $format = null, array $context = []) {
			$value = parent::getAttributeValue($object, $attribute, $format, $context);
			if ($value instanceof EntityInterface)
				return $value->getId();
			else if (CollectionUtil::isIterable($value)) {
				$coll = [];
				foreach ($value as $k => $v) {
					if ($v instanceof EntityInterface)
						$v = $v->getId();
					$coll[$k] = $v;
				}
				$value = $coll;
			}

			return $value;
		}
	}