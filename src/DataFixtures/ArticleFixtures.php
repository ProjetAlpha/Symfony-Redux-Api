<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($count = 0; $count < 20; $count++) {
            $article = new Article();
            $article->setIsDraft(false);
            $article->setTitle('Bob marley');
            $article->setDescription("Robert Nesta Marley dit Bob Marley, né le 6 février 1945 à Nine Mile (Jamaïque) et mort le 11 mai 1981 à Miami (États-Unis) d'un cancer généralisé, est un auteur-compositeur-interprète et musicien jamaïcain.");
            $article->setRawData(
                "<p>Il rencontre de son vivant un succès mondial, et reste à ce jour le musicien le plus connu du reggae, tout en étant considéré comme celui qui a permis à la musique jamaïcaine et au mouvement rastafari de connaître une audience planétaire. Il a vendu plus de 200 millions de disques à travers le monde1.

                Bob Marley commence sa carrière musicale en 1962. En 1963, Robert Nesta Marley forme avec Neville O'Reilly Livingston (plus tard Bunny Wailer) et Wynston Hubert McIntosh (plus tard Peter Tosh) un trio vocal sur le modèle des groupes vocaux américains comme les Impressions. Le trio est tout d'abord appelé les Wailing Wailers, avant de finir par s'appeler The Wailers. C'est avec Simmer Down, en 1964 que The Wailers rencontreront leur premier vrai succès local en Jamaïque2. Beaucoup d'autres suivront jusqu'à 1968... Entre-temps, Bob Marley est devenu rasta à partir de 1966, sous l'influence de personnages importants (comme Mortimo Planno) du mouvement rastafari, alors en plein essor en Jamaïque.
            
                Entre 1968 et 1971, les Wailers, alors composés de Bob Marley, Bunny Livingston et Peter McIntosh, collaboreront avec le producteur Lee « Scratch » Perry, une collaboration très fructueuse qui aboutira sur quatre remarquables albums synthétisés en 1972 par le label Trojan sur l'album African Herbsman. Tout début 1973, sort sous le nom de groupe The Wailers Catch A Fire, puis Burnin' en avril 1973, tous deux chez Island Record, le label fondé par Chris Blackwell. C'est à l'issue de la tournée anglaise Burnin' Tour 1973 que Bunny Livingston, puis Peter McIntosh quittent le groupe fin 1973.</p>"
            );
            $article->setUserId($this->getReference(AdminFixtures::ADMIN_USER_REFERENCE));
            $article->setCoverId($this->getReference(ImageFixtures::IMAGE_REFERENCE.$count)->getId());

            $manager->persist($article);
            $manager->flush();
        }
    }

    public function getDependencies()
    {
        return [
            AdminFixtures::class,
            ImageFixtures::class
        ];
    }
}
