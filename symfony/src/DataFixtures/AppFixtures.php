<?php

namespace App\DataFixtures;

use App\Entity\Affectation;
use App\Entity\Collaborateur;
use App\Entity\Fonction;
use App\Entity\Restaurant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $fonctions = [];
        foreach (['Équipier polyvalent', 'Caissier', 'Manager', 'Préparateur', 'Directeur de restaurant'] as $intitule) {
            $f = (new Fonction())->setIntitule($intitule);
            $manager->persist($f);
            $fonctions[$intitule] = $f;
        }

        $restaurants = [];
        $restosData = [
            'nice' => ['Wacdo Nice Centre', '12 avenue Jean Médecin', '06000', 'Nice'],
            'marseille' => ['Wacdo Marseille Vieux-Port', '45 quai du Port', '13002', 'Marseille'],
            'lyon' => ['Wacdo Lyon Part-Dieu', '8 rue de la Part-Dieu', '69003', 'Lyon'],
        ];
        foreach ($restosData as $key => [$nom, $adr, $cp, $ville]) {
            $r = (new Restaurant())->setNom($nom)->setAdresse($adr)->setCodePostal($cp)->setVille($ville);
            $manager->persist($r);
            $restaurants[$key] = $r;
        }

        $collabs = [];
        $collabsData = [
            ['admin',    'Admin',   'Système',  'admin@wcdo.fr',           '2020-01-15', true,  'admin123'],
            ['marie',    'Dupont',  'Marie',    'marie.dupont@wcdo.fr',    '2021-03-10', true,  'manager1'],
            ['lucas',    'Martin',  'Lucas',    'lucas.martin@wcdo.fr',    '2022-06-01', false, null],
            ['sophie',   'Bernard', 'Sophie',   'sophie.bernard@wcdo.fr',  '2021-09-15', false, null],
            ['thomas',   'Petit',   'Thomas',   'thomas.petit@wcdo.fr',    '2023-01-20', false, null],
            ['emma',     'Leroy',   'Emma',     'emma.leroy@wcdo.fr',      '2022-11-05', false, null],
            ['antoine',  'Moreau',  'Antoine',  'antoine.moreau@wcdo.fr',  '2020-07-12', false, null],
            ['camille',  'Garcia',  'Camille',  'camille.garcia@wcdo.fr',  '2023-05-01', false, null],
        ];
        foreach ($collabsData as [$key, $nom, $prenom, $email, $embauche, $admin, $password]) {
            $c = (new Collaborateur())
                ->setNom($nom)->setPrenom($prenom)->setEmail($email)
                ->setDateEmbauche(new \DateTime($embauche))
                ->setAdministrateur($admin);
            if ($admin && $password !== null) {
                $c->setMotDePasse($this->hasher->hashPassword($c, $password));
            }
            $manager->persist($c);
            $collabs[$key] = $c;
        }

        $affectationsData = [
            ['lucas',   'nice',      'Équipier polyvalent',     '2022-06-15', null],
            ['sophie',  'nice',      'Caissier',                 '2021-10-01', null],
            ['thomas',  'marseille', 'Équipier polyvalent',     '2023-02-01', null],
            ['emma',    'marseille', 'Manager',                  '2023-01-10', null],
            ['antoine', 'lyon',      'Directeur de restaurant', '2020-08-01', null],
            ['antoine', 'nice',      'Manager',                  '2020-08-01', '2022-12-31'],
            ['lucas',   'marseille', 'Préparateur',              '2022-06-15', '2023-01-31'],
            ['sophie',  'lyon',      'Équipier polyvalent',     '2021-10-01', '2022-03-15'],
            ['marie',   'nice',      'Manager',                  '2021-03-15', null],
            ['camille', 'lyon',      'Caissier',                 '2023-05-15', null],
        ];
        foreach ($affectationsData as [$collabKey, $restoKey, $fonctionKey, $debut, $fin]) {
            $a = (new Affectation())
                ->setCollaborateur($collabs[$collabKey])
                ->setRestaurant($restaurants[$restoKey])
                ->setFonction($fonctions[$fonctionKey])
                ->setDateDebut(new \DateTime($debut))
                ->setDateFin($fin !== null ? new \DateTime($fin) : null);
            $manager->persist($a);
        }

        $manager->flush();
    }
}
