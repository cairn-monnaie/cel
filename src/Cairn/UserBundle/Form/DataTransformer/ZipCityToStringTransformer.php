<?php
/**
 * Created by PhpStorm.
 * User: gjanssens
 * Date: 03/03/19
 * Time: 09:17
 */

namespace Cairn\UserBundle\Form\DataTransformer;


use Cairn\UserBundle\Entity\ZipCity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ZipCityToStringTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
    * Transforms an object (zipcity) to a string (string).
    *
    * @param  ZipCity|null $zipCity
    * @return string
    */
    public function transform($zipCity)
    {
        if (null === $zipCity) {
            return '';
        }

        return $zipCity->getName();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $autocomplete
     * @return ZipCity|null
     * @throws TransformationFailedException if object (zipcity) is not found.
     */
    public function reverseTransform($autocomplete)
    {
        if (!$autocomplete) {
            return;
        }

        $zipcity = $this->entityManager
            ->getRepository(ZipCity::class)
            ->findByName($autocomplete)
        ;

        if (null === $zipcity) {
            throw new TransformationFailedException(sprintf(
                'An zipcity with name "%s" does not exist!',
                $autocomplete
            ));
        }

        return $zipcity;
    }
}