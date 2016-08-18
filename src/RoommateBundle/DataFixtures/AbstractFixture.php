<?php

namespace RoommateBundle\DataFixtures;

abstract class AbstractFixture extends \Doctrine\Common\DataFixtures\AbstractFixture
{
    public function getReferencesMatching($pattern)
    {
        $items = $this->referenceRepository->getReferences();
        $references = array_keys($items);

        return array_values(preg_grep($pattern, $references));
    }

    public function getRandomReferenceMatching(string $pattern) : string
    {
        $matching = $this->getReferencesMatching($pattern);
        return $matching[random_int(0, count($matching) - 1)];
    }
}
