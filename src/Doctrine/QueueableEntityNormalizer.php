<?php
namespace Workana\AsyncJobs\Doctrine;

use Bernard\Normalizer\AbstractAggregateNormalizerAware;
use Workana\AsyncJobs\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Carlos Frutos <charly@workana.com>
 */
class QueueableEntityNormalizer extends AbstractAggregateNormalizerAware implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * QueueableEntityNormalizer constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return [
            'class' => ClassUtils::getRealClass($object),
            'id' => $object->getId(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return ($data instanceof QueueableEntityInterface);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        return $this->entityManager->getReference($data['class'], $data['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return is_a($type, QueueableEntityInterface::class, true);
    }
}