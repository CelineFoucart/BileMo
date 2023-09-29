<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Customer;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * Product information to insert as demo data.
     */
    private array $data = [
        [
            'name' => 'Smartphone Samsung Galaxy Z Flip5 6,7" Nano SIM 5G 512 Go Graphite',
            'description' => 'Pliable et compact à la fois : Un design pliable ultra compact qui se range facilement dans votre sac ou votre poche.Un tout nouvel écran externe : Pour répondre à un message, changer de musique ou prendre un selfie sans ouvrir son smartphone.',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Apple iPhone 14 Pro Max 6,7" 5G Double SIM 128 Go Or',
            'description' => 'Votre photo. Votre typo. Vos widgets. Votre iPhone. Avec iOS 16, vous pouvez personnaliser votre écran verrouillé de façons inédites. Détourez une partie de votre photo pour la mettre en avant. Suivez l’évolution de vos anneaux Activité. Et voyez en direct les informations de vos apps préférées.Détection des accidents appelle les secours pour vous.',
            'brand' => 'Apple iPhone',
        ],
        [
            'name' => 'Apple iPhone 12 6,1" 128 Go Double SIM 5G Bleu',
            'description' => 'Apple iPhone 12 6,1 128 Go Double SIM 5G Bleu',
            'brand' => 'Apple iPhone',
        ],
        [
            'name' => 'Smartphone Xiaomi 13 6.36" Double nano SIM 5G 8 Go RAM 256 Go Noir',
            'description' => 'Optique professionnelle Leica Deux styles Leica uniques, objectif téléphoto 75mm. Snapdragon® 8 Gen 2',
            'brand' => 'Xiaomi',
        ],
        [
            'name' => 'Smartphone Xiaomi 13 Ultra 6,73" 5G Double nano SIM 512 Go Noir',
            'description' => "Un design intemporel et une durabilité optimale Le Xiaomi 13 Ultra rend hommage au style iconique des appareils photo Leica de la série M avec son module caméra circulaire, sa structure unibody en métal et sa finition effet cuir. Sa ligne courbée qui s'étend à l'arrière vient souligner son aspect et sa finition premium. ",
            'brand' => 'Xiaomi',
        ],
        [
            'name' => 'Smartphone Google Pixel 7 6.3" 5G Double SIM 128 Go Neige',
            'description' => "Découvrez Google Pixel 7. Doté de Google Tensor G2, il est rapide, sécurisé, bénéficie d'une autonomie exceptionnelle et du module photo Pixel avancé. Prenez de magnifiques photos fidèles à la réalité avec Real Tone et filmez des vidéos sublimes avec le Flou cinématique.",
            'brand' => 'Google Pixel',
        ],
        [
            'name' => 'Smartphone Oppo Find X5 Pro 6,7" 5G Double SIM Noir glacé',
            'description' => 'APPAREIL PHOTO HASSELBLAD POUR SMARTPHONE Un appareil photo capturant un milliard de couleurs et bénéfi ciant du savoir-faire uniqueD’Hasselblad.BATTERIE LONGUE DURÉE + SUPERVOOC™ 80W',
            'brand' => 'Oppo',
        ],
        [
            'name' => 'Smartphone OnePlus 9 Pro 6,7" 256 Go Double SIM 5G Noir étoile',
            'description' => 'Appareil photo Hasselblad pour smartphoneUne exclusivité OnePlus co-développée avec Hasselblad, l’Appareil photo Hasselblad pour Smartphone propose des innovations de pointe en matière de photographie mobile. ',
            'brand' => 'OnePlus',
        ],
        [
            'name' => 'Smartphone Samsung Galaxy S23 6.1" Nano SIM 5G 8 Go RAM 256 Go Noir',
            'description' => 'Des photos et vidéos à couper le souffle. Avec capteur principal de 50MP, capteur Ultra Grand Angle de 12MP et téléobjectif de 10MP avec zoom optique 3x. Une gestion de la basse lumière éblouissante  Pour des photos, vidéos et selfies plus claires, stables et colorés à la nuit tombée.',
            'brand' => 'Samsung',
        ],
        [
            'name' => 'Smartphone Samsung Galaxy S23 6.1" Nano SIM 5G 8 Go RAM 256 Go Lavande',
            'description' => 'Des photos et vidéos à couper le souffle. Avec capteur principal de 50MP, capteur Ultra Grand Angle de 12MP et téléobjectif de 10MP avec zoom optique 3x. Une gestion de la basse lumière éblouissante. Pour des photos, vidéos et selfies plus claires, stables et colorés à la nuit tombée.',
            'brand' => 'Samsung',
        ],
    ];

    public function __construct(
        private UserPasswordHasherInterface $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->data as $value) {
            $product = (new Product())
                ->setName($value['name'])
                ->setDescription($value['description'])
                ->setBrand($value['brand'])
                ->setPrice(990.99)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTime());
            $manager->persist($product);
        }

        $client1 = $this->setClient('SFR', 'sfr', 'contact@sfr.fr');
        $manager->persist($client1);
        $customer = (new Customer())
            ->setUsername('john')
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setEmail('john@domaine.com')
            ->setClient($client1)
        ;
        $manager->persist($customer);

        $client2 = $this->setClient('Orange', 'orange', 'contact@orange.fr');
        $manager->persist($client2);
        $customer2 = (new Customer())
            ->setUsername('jane')
            ->setFirstname('Jane')
            ->setLastname('Doe')
            ->setEmail('jane@domaine.com')
            ->setClient($client2)
        ;
        $manager->persist($customer2);

        $manager->flush();
    }

    private function setClient(string $organisation, string $username, string $email)
    {
        $client = (new Client())
            ->setOrganisation($organisation)
            ->setRoles(['ROLE_USER'])
            ->setEmail($email)
            ->setUsername($username)
            ->setCreatedAt(new \DateTimeImmutable())
        ;

        $password = $this->hasher->hashPassword($client, 'XqTw78!FR45');
        $client->setPassword($password);

        return $client;
    }
}
