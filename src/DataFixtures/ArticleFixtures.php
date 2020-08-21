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
                '<p style="text-align:start;">
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">Robert Nesta Marley</span>
                <a href="https://fr.wikipedia.org/wiki/Nom_de_sc%C3%A8ne" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">dit</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">Bob Marley, né le</span>
                <a href="https://fr.wikipedia.org/wiki/6_f%C3%A9vrier" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">6</span>
                </a>
                <a href="https://fr.wikipedia.org/wiki/F%C3%A9vrier_1945" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">février</span>
                </a>
                <a href="https://fr.wikipedia.org/wiki/1945" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">1945</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">à</span>
                <a href="https://fr.wikipedia.org/wiki/Nine_Miles" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">Nine Mile</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">(</span>
                <a href="https://fr.wikipedia.org/wiki/Jama%C3%AFque" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">Jamaïque</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">) et mort le</span>
                <a href="https://fr.wikipedia.org/wiki/11_mai" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">11</span>
                </a>
                <a href="https://fr.wikipedia.org/wiki/Mai_1981" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">mai</span>
                </a>
                <a href="https://fr.wikipedia.org/wiki/1981" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">1981</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">à</span>
                <a href="https://fr.wikipedia.org/wiki/Miami" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">Miami</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">(</span>
                <a href="https://fr.wikipedia.org/wiki/%C3%89tats-Unis" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">États-Unis</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">) d\'un</span>
                <a href="https://fr.wikipedia.org/wiki/Cancer" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">cancer</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">généralisé, est un</span>
                <a href="https://fr.wikipedia.org/wiki/Auteur-compositeur-interpr%C3%A8te" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">auteur-compositeur-interprète</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">et</span>
                <a href="https://fr.wikipedia.org/wiki/Musicien" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">musicien</span>
                </a>
                <a href="https://fr.wikipedia.org/wiki/Jama%C3%AFque" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">jamaïcain</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">.</span>
            </p>
            <p style="text-align:start;">
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">Il rencontre de son vivant un succès mondial, et reste à ce jour le musicien le plus connu du</span>
                <a href="https://fr.wikipedia.org/wiki/Reggae" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">reggae</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">, tout en étant considéré comme celui qui a permis à la</span>
                <a href="https://fr.wikipedia.org/wiki/Musique_jama%C3%AFcaine" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">musique jamaïcaine</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">et au</span>
                <a href="https://fr.wikipedia.org/wiki/Mouvement_rastafari" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">mouvement rastafari</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">de connaître une audience</span>
                <a href="https://fr.wikipedia.org/wiki/Terre" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">planétaire</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">. Il a vendu plus de 200 millions de disques à travers le monde</span>
                <a href="https://fr.wikipedia.org/wiki/Bob_Marley#cite_note-1" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">
                        <sup>1</sup>
                    </span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">.</span>
            </p>
            <p style="text-align:start;">
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">Bob Marley commence sa carrière musicale en</span>
                <a href="https://fr.wikipedia.org/wiki/1962" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">1962</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">. En 1963, Robert Nesta Marley forme avec Neville O\'Reilly Livingston (plus tard</span>
                <a href="https://fr.wikipedia.org/wiki/Bunny_Wailer" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">Bunny Wailer</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">) et Wynston Hubert McIntosh (plus tard</span>
                <a href="https://fr.wikipedia.org/wiki/Peter_Tosh" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">Peter Tosh</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">) un trio vocal sur le modèle des groupes vocaux américains comme les Impressions. Le trio est tout d\'abord appelé les Wailing Wailers, avant de finir par s\'appeler</span>
                <a href="https://fr.wikipedia.org/wiki/The_Wailers" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">The Wailers</span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">. C\'est avec</span>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">Simmer Down, en 1964 que The Wailers rencontreront leur premier vrai succès local en Jamaïque</span>
                <a href="https://fr.wikipedia.org/wiki/Bob_Marley#cite_note-2" target="_self">
                    <span style="color: rgb(11,0,128);background-color: initial;font-size: 14px;font-family: sans-serif;">
                        <sup>2</sup>
                    </span>
                </a>
                <span style="color: rgb(32,33,34);background-color: rgb(255,255,255);font-size: 14px;font-family: sans-serif;">. Beaucoup d\'autres suivront jusqu\'à 1968... Entre-temps, Bob Marley est devenu rasta à partir de 1966, sous l\'influence de personnages importants (comme Mortimo Planno) du mouvement rastafari, alors en plein essor en Jamaïque.</span>&nbsp;
            </p>'
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
