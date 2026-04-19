<?php

namespace App\Tests\Entity;

use App\Entity\Affectation;
use App\Entity\Collaborateur;
use App\Entity\Fonction;
use App\Entity\Restaurant;
use PHPUnit\Framework\TestCase;

class AffectationTest extends TestCase
{
    public function testIsActiveTrueWhenDateFinNull(): void
    {
        $a = (new Affectation())->setDateDebut(new \DateTime('2024-01-01'));
        $this->assertTrue($a->isActive());
    }

    public function testIsActiveFalseWhenDateFinSet(): void
    {
        $a = (new Affectation())
            ->setDateDebut(new \DateTime('2023-01-01'))
            ->setDateFin(new \DateTime('2024-06-30'));
        $this->assertFalse($a->isActive());
    }

    public function testRelationsAreSet(): void
    {
        $c = (new Collaborateur())->setNom('N')->setPrenom('P')->setEmail('a@b.c');
        $r = (new Restaurant())->setNom('R')->setAdresse('A')->setCodePostal('75000')->setVille('Paris');
        $f = (new Fonction())->setIntitule('Manager');
        $a = (new Affectation())->setCollaborateur($c)->setRestaurant($r)->setFonction($f);
        $this->assertSame($c, $a->getCollaborateur());
        $this->assertSame($r, $a->getRestaurant());
        $this->assertSame($f, $a->getFonction());
    }
}
