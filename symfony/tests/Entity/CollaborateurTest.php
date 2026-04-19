<?php

namespace App\Tests\Entity;

use App\Entity\Collaborateur;
use PHPUnit\Framework\TestCase;

class CollaborateurTest extends TestCase
{
    public function testGetUserIdentifierReturnsEmail(): void
    {
        $c = (new Collaborateur())->setEmail('test@wcdo.fr');
        $this->assertSame('test@wcdo.fr', $c->getUserIdentifier());
    }

    public function testDefaultRolesContainsRoleUser(): void
    {
        $c = new Collaborateur();
        $this->assertContains('ROLE_USER', $c->getRoles());
    }

    public function testSetAdministrateurTrueGrantsRoleAdmin(): void
    {
        $c = (new Collaborateur())->setAdministrateur(true);
        $this->assertTrue($c->isAdministrateur());
        $this->assertContains('ROLE_ADMIN', $c->getRoles());
    }

    public function testSetAdministrateurFalseRemovesRoleAdmin(): void
    {
        $c = (new Collaborateur())->setAdministrateur(true);
        $c->setAdministrateur(false);
        $this->assertFalse($c->isAdministrateur());
        $this->assertNotContains('ROLE_ADMIN', $c->getRoles());
        $this->assertContains('ROLE_USER', $c->getRoles());
    }
}
