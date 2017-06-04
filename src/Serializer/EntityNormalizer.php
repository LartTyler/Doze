<?php
	namespace DaybreakStudios\Doze\Serializer;

	use DaybreakStudios\Doze\Entity\EntityInterface;
	use DaybreakStudios\Doze\Utility\CollectionUtil;
	use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
	use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

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
	 * @see     EntityInterface
	 */
	class EntityNormalizer extends ObjectNormalizer {
		/**
		 * {@inheritdoc}
		 */
		protected function getAttributes($object, $format = null, array $context) {
			return $this->extractAttributes($object, $format, $context);
		}

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

			if ($value instanceof EntityInterface && !$this->isExplicitlyAllowed($attribute, $context))
				return $value->getId();

			return $value;
		}

		protected function isExplicitlyAllowed($attribute, array $context) {
			if (isset($context[AbstractNormalizer::ATTRIBUTES][$attribute]))
				return true;

			if (isset($context[AbstractNormalizer::ATTRIBUTES]) && is_array($context[AbstractNormalizer::ATTRIBUTES]))
				return in_array($attribute, $context[AbstractNormalizer::ATTRIBUTES], true);

			return false;
		}
	}