<?php

namespace App\Controller;

use App\Entity\Joueurs;
use App\Entity\Product;
use App\Entity\Actualites;
use App\Entity\Entrainements;
use App\Entity\EntrainementJoueurs;
use App\Entity\Matchs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class JoueurController extends AbstractController
{
    public function getJoueurs(EntityManagerInterface $entityManager): JsonResponse
    {
        $joueurs = $entityManager->getRepository(Joueurs::class)->findAll();
        $joueursData = array_map(function ($joueur) {
            return [
                'id' => $joueur->getId(),
                'nom' => $joueur->getNom(),
                'prenom' => $joueur->getPrenom(),
                'poste' => $joueur->getPoste(),
                'equipe' => $joueur->getEquipe(),
                'pays_origine' => $joueur->getPaysOrigine(), 
                'age' => $joueur->getAge(),
                'taille_cm' => $joueur->getTailleCm(),
                'poids_kg' => $joueur->getPoidsKg(),
                'numero_maillot' => $joueur->getNumeroMaillot(),
            ];
        }, $joueurs);

        return new JsonResponse($joueursData);
    }

    public function addJoueur(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $joueur = new Joueurs();
        $joueur->setNom($data['nom']);
        $joueur->setPrenom($data['prenom']);
        $joueur->setPoste($data['poste']);
        $joueur->setEquipe($data['equipe']);
        $joueur->setPaysOrigine($data['pays_origine']); 
        $joueur->setAge($data['age']);
        $joueur->setTailleCm($data['taille_cm']);
        $joueur->setPoidsKg($data['poids_kg']);
        $joueur->setNumeroMaillot($data['numero_maillot']);

        $entityManager->persist($joueur);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Joueur ajouté avec succès'], 201);
    }

    public function updateJoueur(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $joueur = $entityManager->getRepository(Joueurs::class)->find($id);

        if (!$joueur) {
            return new JsonResponse(['message' => 'Joueur non trouvé'], 404);
        }

        $joueur->setNom($data['nom']);
        $joueur->setPrenom($data['prenom']);
        $joueur->setPoste($data['poste']);
        $joueur->setEquipe($data['equipe']);
        $joueur->setPaysOrigine($data['pays_origine']); 
        $joueur->setAge($data['age']);
        $joueur->setTailleCm($data['taille_cm']);
        $joueur->setPoidsKg($data['poids_kg']);
        $joueur->setNumeroMaillot($data['numero_maillot']);

        $entityManager->flush();

        return new JsonResponse(['message' => 'Joueur modifié avec succès']);
    }

    public function deleteJoueur(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $joueur = $entityManager->getRepository(Joueurs::class)->find($id);

        if (!$joueur) {
            return new JsonResponse(['message' => 'Joueur non trouvé'], 404);
        }

        $entityManager->remove($joueur);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Joueur supprimé avec succès']);
    }

    public function getDashboardStats(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $totalJoueurs = $entityManager->getRepository(Joueurs::class)
                ->createQueryBuilder('j')
                ->select('COUNT(j.id)')
                ->where('j.equipe = :equipe')
                ->setParameter('equipe', 'PSG')
                ->getQuery()
                ->getSingleScalarResult();

            $stockQuery = $entityManager->getRepository(Product::class)
                ->createQueryBuilder('p')
                ->select('SUM(p.stock) as totalStock, COUNT(p.id) as totalProduits')
                ->getQuery()
                ->getSingleResult();

            $totalStock = $stockQuery['totalStock'] ?? 0;
            $totalProduits = $stockQuery['totalProduits'] ?? 0;

            $revenusQuery = $entityManager->getRepository(Product::class)
                ->createQueryBuilder('p')
                ->select('SUM(p.prix * p.stock) as revenus')
                ->getQuery()
                ->getSingleResult();

            $revenus = $revenusQuery['revenus'] ?? 0;
            $revenusFormattes = $this->formatMoney($revenus);

            $totalActualites = 0;
            try {
                $totalActualites = $entityManager->getRepository(Actualites::class)
                    ->createQueryBuilder('a')
                    ->select('COUNT(a.id)')
                    ->where('a.statut = :statut')
                    ->setParameter('statut', 'publie')
                    ->getQuery()
                    ->getSingleScalarResult();
            } catch (\Exception $e) {
            }

            $topProduits = $entityManager->getRepository(Product::class)
                ->createQueryBuilder('p')
                ->select('p.nom, p.prix, p.stock')
                ->orderBy('p.prix', 'DESC')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult();

            return new JsonResponse([
                'totalJoueurs' => (int)$totalJoueurs,
                'totalStock' => (int)$totalStock,
                'totalProduits' => (int)$totalProduits,
                'totalActualites' => (int)$totalActualites,
                'revenus' => $revenusFormattes,
                'topProduits' => $topProduits,
                'lastUpdate' => (new \DateTime())->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération des statistiques',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getJoueursStats(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $statsParPoste = $entityManager->getRepository(Joueurs::class)
                ->createQueryBuilder('j')
                ->select('j.poste, COUNT(j.id) as nombre, AVG(j.age) as ageMoyen')
                ->where('j.equipe = :equipe')
                ->setParameter('equipe', 'PSG')
                ->groupBy('j.poste')
                ->getQuery()
                ->getResult();

            return new JsonResponse([
                'statsParPoste' => $statsParPoste
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération des stats joueurs',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getBoutiqueStats(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $produitsStockFaible = $entityManager->getRepository(Product::class)
                ->createQueryBuilder('p')
                ->select('p.nom, p.stock, p.prix')
                ->where('p.stock <= :seuil')
                ->setParameter('seuil', 10)
                ->orderBy('p.stock', 'ASC')
                ->getQuery()
                ->getResult();

            return new JsonResponse([
                'produitsStockFaible' => $produitsStockFaible,
                'alertesStock' => count($produitsStockFaible)
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération des stats boutique',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getActualites(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $actualites = $entityManager->getRepository(Actualites::class)
                ->findBy(['statut' => 'publie'], ['datePublication' => 'DESC']);

            $actualitesData = array_map(function ($actualite) {
                return [
                    'id' => $actualite->getId(),
                    'titre' => $actualite->getTitre(),
                    'contenu' => $actualite->getContenu(),
                    'date_publication' => $actualite->getDatePublication()->format('Y-m-d'),
                    'auteur' => $actualite->getAuteur(),
                    'image_url' => $actualite->getImageUrl(),
                    'likes' => $actualite->getLikes(),
                    'commentaires' => $actualite->getCommentaires(),
                ];
            }, $actualites);

            return new JsonResponse($actualitesData);

        } catch (\Exception $e) {
            return new JsonResponse([
                [
                    'id' => 1,
                    'titre' => 'Table actualités non créée',
                    'contenu' => 'Veuillez exécuter le script SQL pour créer la table actualites. Erreur: ' . $e->getMessage(),
                    'date_publication' => date('Y-m-d'),
                    'auteur' => 'Système',
                    'likes' => 0,
                    'commentaires' => 0
                ]
            ]);
        }
    }

    public function addActualite(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (empty($data['titre']) || empty($data['contenu']) || empty($data['auteur'])) {
                return new JsonResponse([
                    'error' => 'Les champs titre, contenu et auteur sont obligatoires'
                ], 400);
            }

            $actualite = new Actualites();
            $actualite->setTitre($data['titre']);
            $actualite->setContenu($data['contenu']);
            $actualite->setAuteur($data['auteur']);
            
            if (isset($data['image_url'])) {
                $actualite->setImageUrl($data['image_url']);
            }
            
            if (isset($data['statut'])) {
                $actualite->setStatut($data['statut']);
            }

            $entityManager->persist($actualite);
            $entityManager->flush();

            return new JsonResponse([
                'message' => 'Actualité ajoutée avec succès',
                'id' => $actualite->getId(),
                'data' => [
                    'titre' => $actualite->getTitre(),
                    'contenu' => $actualite->getContenu(),
                    'auteur' => $actualite->getAuteur(),
                    'date_publication' => $actualite->getDatePublication()->format('Y-m-d H:i:s')
                ]
            ], 201);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de l\'ajout de l\'actualité',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function updateActualite(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $actualite = $entityManager->getRepository(Actualites::class)->find($id);

            if (!$actualite) {
                return new JsonResponse(['error' => 'Actualité non trouvée'], 404);
            }

            $data = json_decode($request->getContent(), true);

            if (isset($data['titre'])) {
                $actualite->setTitre($data['titre']);
            }
            if (isset($data['contenu'])) {
                $actualite->setContenu($data['contenu']);
            }
            if (isset($data['auteur'])) {
                $actualite->setAuteur($data['auteur']);
            }
            if (isset($data['image_url'])) {
                $actualite->setImageUrl($data['image_url']);
            }
            if (isset($data['statut'])) {
                $actualite->setStatut($data['statut']);
            }

            $actualite->setUpdatedAt(new \DateTime());
            
            $entityManager->flush();

            return new JsonResponse([
                'message' => 'Actualité modifiée avec succès',
                'data' => [
                    'id' => $actualite->getId(),
                    'titre' => $actualite->getTitre(),
                    'contenu' => $actualite->getContenu(),
                    'auteur' => $actualite->getAuteur(),
                    'date_publication' => $actualite->getDatePublication()->format('Y-m-d H:i:s'),
                    'updated_at' => $actualite->getUpdatedAt()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la modification',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteActualite(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $actualite = $entityManager->getRepository(Actualites::class)->find($id);

            if (!$actualite) {
                return new JsonResponse(['error' => 'Actualité non trouvée'], 404);
            }

            $entityManager->remove($actualite);
            $entityManager->flush();

            return new JsonResponse([
                'message' => 'Actualité supprimée avec succès',
                'id' => $id
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la suppression',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getEntrainements(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entrainements = $entityManager->getRepository(Entrainements::class)
                ->findBy([], ['dateEntrainement' => 'ASC']);

            $entrainementsData = [];
            foreach ($entrainements as $entrainement) {
                $joueursConvoques = $entityManager->createQueryBuilder()
                    ->select('j.id, j.nom, j.prenom, ej.present, ej.excuse')
                    ->from(EntrainementJoueurs::class, 'ej')
                    ->join(Joueurs::class, 'j', 'WITH', 'j.id = ej.joueurId')
                    ->where('ej.entrainementId = :entrainementId')
                    ->setParameter('entrainementId', $entrainement->getId())
                    ->getQuery()
                    ->getResult();

                $entrainementsData[] = [
                    'id' => $entrainement->getId(),
                    'titre' => $entrainement->getTitre(),
                    'description' => $entrainement->getDescription(),
                    'date_entrainement' => $entrainement->getDateEntrainement()->format('Y-m-d H:i:s'),
                    'lieu' => $entrainement->getLieu(),
                    'duree_minutes' => $entrainement->getDureeMinutes(),
                    'type_entrainement' => $entrainement->getTypeEntrainement(),
                    'statut' => $entrainement->getStatut(),
                    'joueurs_convoques' => $joueursConvoques,
                    'nb_joueurs_convoques' => count($joueursConvoques)
                ];
            }

            return new JsonResponse($entrainementsData);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération des entraînements',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function addEntrainement(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (empty($data['titre']) || empty($data['date_entrainement']) || empty($data['lieu']) || empty($data['type_entrainement'])) {
                return new JsonResponse([
                    'error' => 'Les champs titre, date_entrainement, lieu et type_entrainement sont obligatoires'
                ], 400);
            }

            $entrainement = new Entrainements();
            $entrainement->setTitre($data['titre']);
            $entrainement->setDescription($data['description'] ?? '');
            $entrainement->setDateEntrainement(new \DateTime($data['date_entrainement']));
            $entrainement->setLieu($data['lieu']);
            $entrainement->setDureeMinutes($data['duree_minutes'] ?? 120);
            $entrainement->setTypeEntrainement($data['type_entrainement']);

            $entityManager->persist($entrainement);
            $entityManager->flush();

            if (isset($data['joueurs_ids']) && is_array($data['joueurs_ids'])) {
                foreach ($data['joueurs_ids'] as $joueurId) {
                    $entrainementJoueur = new EntrainementJoueurs();
                    $entrainementJoueur->setEntrainementId($entrainement->getId());
                    $entrainementJoueur->setJoueurId($joueurId);
                    
                    $entityManager->persist($entrainementJoueur);
                }
                $entityManager->flush();
            }

            return new JsonResponse([
                'message' => 'Entraînement créé avec succès',
                'id' => $entrainement->getId(),
                'data' => [
                    'titre' => $entrainement->getTitre(),
                    'date_entrainement' => $entrainement->getDateEntrainement()->format('Y-m-d H:i:s'),
                    'lieu' => $entrainement->getLieu(),
                    'nb_joueurs_assignes' => count($data['joueurs_ids'] ?? [])
                ]
            ], 201);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la création de l\'entraînement',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function updateEntrainement(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entrainement = $entityManager->getRepository(Entrainements::class)->find($id);

            if (!$entrainement) {
                return new JsonResponse(['error' => 'Entraînement non trouvé'], 404);
            }

            $data = json_decode($request->getContent(), true);

            if (isset($data['titre'])) {
                $entrainement->setTitre($data['titre']);
            }
            if (isset($data['description'])) {
                $entrainement->setDescription($data['description']);
            }
            if (isset($data['date_entrainement'])) {
                $entrainement->setDateEntrainement(new \DateTime($data['date_entrainement']));
            }
            if (isset($data['lieu'])) {
                $entrainement->setLieu($data['lieu']);
            }
            if (isset($data['duree_minutes'])) {
                $entrainement->setDureeMinutes($data['duree_minutes']);
            }
            if (isset($data['type_entrainement'])) {
                $entrainement->setTypeEntrainement($data['type_entrainement']);
            }
            if (isset($data['statut'])) {
                $entrainement->setStatut($data['statut']);
            }

            $entrainement->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            return new JsonResponse([
                'message' => 'Entraînement modifié avec succès',
                'data' => [
                    'id' => $entrainement->getId(),
                    'titre' => $entrainement->getTitre(),
                    'date_entrainement' => $entrainement->getDateEntrainement()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la modification',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteEntrainement(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entrainement = $entityManager->getRepository(Entrainements::class)->find($id);

            if (!$entrainement) {
                return new JsonResponse(['error' => 'Entraînement non trouvé'], 404);
            }

            $entityManager->remove($entrainement);
            $entityManager->flush();

            return new JsonResponse([
                'message' => 'Entraînement supprimé avec succès',
                'id' => $id
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la suppression',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function assignerJoueurEntrainement(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (empty($data['entrainement_id']) || empty($data['joueur_id'])) {
                return new JsonResponse([
                    'error' => 'Les champs entrainement_id et joueur_id sont obligatoires'
                ], 400);
            }

            $entrainement = $entityManager->getRepository(Entrainements::class)->find($data['entrainement_id']);
            $joueur = $entityManager->getRepository(Joueurs::class)->find($data['joueur_id']);

            if (!$entrainement || !$joueur) {
                return new JsonResponse(['error' => 'Entraînement ou joueur non trouvé'], 404);
            }

            $existingAssignment = $entityManager->getRepository(EntrainementJoueurs::class)
                ->findOneBy([
                    'entrainementId' => $data['entrainement_id'],
                    'joueurId' => $data['joueur_id']
                ]);

            if ($existingAssignment) {
                return new JsonResponse(['error' => 'Joueur déjà assigné à cet entraînement'], 409);
            }

            $entrainementJoueur = new EntrainementJoueurs();
            $entrainementJoueur->setEntrainementId($data['entrainement_id']);
            $entrainementJoueur->setJoueurId($data['joueur_id']);

            $entityManager->persist($entrainementJoueur);
            $entityManager->flush();

            return new JsonResponse([
                'message' => 'Joueur assigné avec succès',
                'data' => [
                    'entrainement' => $entrainement->getTitre(),
                    'joueur' => $joueur->getPrenom() . ' ' . $joueur->getNom()
                ]
            ], 201);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de l\'assignation',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function retirerJoueurEntrainement(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (empty($data['entrainement_id']) || empty($data['joueur_id'])) {
                return new JsonResponse([
                    'error' => 'Les champs entrainement_id et joueur_id sont obligatoires'
                ], 400);
            }

            $assignment = $entityManager->getRepository(EntrainementJoueurs::class)
                ->findOneBy([
                    'entrainementId' => $data['entrainement_id'],
                    'joueurId' => $data['joueur_id']
                ]);

            if (!$assignment) {
                return new JsonResponse(['error' => 'Assignation non trouvée'], 404);
            }

            $entityManager->remove($assignment);
            $entityManager->flush();

            return new JsonResponse([
                'message' => 'Joueur retiré avec succès de l\'entraînement'
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors du retrait',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getMatchs(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $matchs = $entityManager->getRepository(Matchs::class)
                ->findBy([], ['dateMatch' => 'ASC']);

            $matchsData = [];
            foreach ($matchs as $match) {
                $matchsData[] = [
                    'id' => $match->getId(),
                    'equipe_domicile' => $match->getEquipeDomicile(),
                    'equipe_exterieure' => $match->getEquipeExterieure(),
                    'equipe_adverse' => $match->getEquipeAdverse(),
                    'lieu' => $match->getLieu(),
                    'date_match' => $match->getDateMatch()->format('Y-m-d H:i:s'),
                    'competition' => $match->getCompetition(),
                    'stade' => $match->getStade(),
                    'statut' => $match->getStatut(),
                    'score_domicile' => $match->getScoreDomicile(),
                    'score_exterieure' => $match->getScoreExterieure(),
                    'score_psg' => $match->getScorePsg(),
                    'score_adverse' => $match->getScoreAdverse(),
                    'is_psg_domicile' => $match->isPsgDomicile(),
                    'created_at' => $match->getCreatedAt()->format('Y-m-d H:i:s')
                ];
            }

            return new JsonResponse($matchsData);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération des matchs',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function addMatch(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (empty($data['equipe_adverse']) || empty($data['date_match']) || 
                empty($data['competition']) || empty($data['lieu']) || empty($data['stade'])) {
                return new JsonResponse([
                    'error' => 'Les champs équipe adverse, date, compétition, lieu et stade sont obligatoires'
                ], 400);
            }

            $match = new Matchs();
            
            if ($data['lieu'] === 'Domicile') {
                $match->setEquipeDomicile('PSG');
                $match->setEquipeExterieure($data['equipe_adverse']);
            } else {
                $match->setEquipeDomicile($data['equipe_adverse']);
                $match->setEquipeExterieure('PSG');
            }
            
            $match->setDateMatch(new \DateTime($data['date_match']));
            $match->setCompetition($data['competition']);
            $match->setStade($data['stade']);
            
            if (isset($data['statut'])) {
                $match->setStatut($data['statut']);
            }
            
            if (isset($data['score_psg']) && isset($data['score_adverse'])) {
                if ($data['lieu'] === 'Domicile') {
                    $match->setScoreDomicile($data['score_psg']);
                    $match->setScoreExterieure($data['score_adverse']);
                } else {
                    $match->setScoreDomicile($data['score_adverse']);
                    $match->setScoreExterieure($data['score_psg']);
                }
            }

            $entityManager->persist($match);
            $entityManager->flush();

            return new JsonResponse([
                'message' => 'Match créé avec succès',
                'id' => $match->getId(),
                'data' => [
                    'equipe_adverse' => $match->getEquipeAdverse(),
                    'date_match' => $match->getDateMatch()->format('Y-m-d H:i:s'),
                    'competition' => $match->getCompetition(),
                    'lieu' => $match->getLieu(),
                    'stade' => $match->getStade()
                ]
            ], 201);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la création du match',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function updateMatch(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $match = $entityManager->getRepository(Matchs::class)->find($id);

            if (!$match) {
                return new JsonResponse(['error' => 'Match non trouvé'], 404);
            }

            $data = json_decode($request->getContent(), true);

            if (isset($data['equipe_adverse']) && isset($data['lieu'])) {
                if ($data['lieu'] === 'Domicile') {
                    $match->setEquipeDomicile('PSG');
                    $match->setEquipeExterieure($data['equipe_adverse']);
                } else {
                    $match->setEquipeDomicile($data['equipe_adverse']);
                    $match->setEquipeExterieure('PSG');
                }
            }

            if (isset($data['date_match'])) {
                $match->setDateMatch(new \DateTime($data['date_match']));
            }
            if (isset($data['competition'])) {
                $match->setCompetition($data['competition']);
            }
            if (isset($data['stade'])) {
                $match->setStade($data['stade']);
            }
            if (isset($data['statut'])) {
                $match->setStatut($data['statut']);
            }

            if (isset($data['score_psg']) && isset($data['score_adverse'])) {
                if ($match->isPsgDomicile()) {
                    $match->setScoreDomicile($data['score_psg']);
                    $match->setScoreExterieure($data['score_adverse']);
                } else {
                    $match->setScoreDomicile($data['score_adverse']);
                    $match->setScoreExterieure($data['score_psg']);
                }
            }
            
            $entityManager->flush();

            return new JsonResponse([
                'message' => 'Match modifié avec succès',
                'data' => [
                    'id' => $match->getId(),
                    'equipe_adverse' => $match->getEquipeAdverse(),
                    'date_match' => $match->getDateMatch()->format('Y-m-d H:i:s'),
                    'competition' => $match->getCompetition(),
                    'lieu' => $match->getLieu(),
                    'stade' => $match->getStade(),
                    'statut' => $match->getStatut()
                ]
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la modification',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteMatch(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $match = $entityManager->getRepository(Matchs::class)->find($id);

            if (!$match) {
                return new JsonResponse(['error' => 'Match non trouvé'], 404);
            }

            $entityManager->remove($match);
            $entityManager->flush();

            return new JsonResponse([
                'message' => 'Match supprimé avec succès',
                'id' => $id
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la suppression',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getMatchsStats(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $totalMatchs = $entityManager->getRepository(Matchs::class)
                ->createQueryBuilder('m')
                ->select('COUNT(m.id)')
                ->where('m.equipeDomicile = :psg OR m.equipeExterieure = :psg')
                ->setParameter('psg', 'PSG')
                ->getQuery()
                ->getSingleScalarResult();

            $matchsJoues = $entityManager->getRepository(Matchs::class)
                ->createQueryBuilder('m')
                ->select('COUNT(m.id)')
                ->where('(m.equipeDomicile = :psg OR m.equipeExterieure = :psg)')
                ->andWhere('m.statut = :statut')
                ->setParameter('psg', 'PSG')
                ->setParameter('statut', 'termine')
                ->getQuery()
                ->getSingleScalarResult();

            $victoires = $entityManager->getRepository(Matchs::class)
                ->createQueryBuilder('m')
                ->select('COUNT(m.id)')
                ->where('m.statut = :statut')
                ->andWhere('(
                    (m.equipeDomicile = :psg AND m.scoreDomicile > m.scoreExterieure) OR
                    (m.equipeExterieure = :psg AND m.scoreExterieure > m.scoreDomicile)
                )')
                ->setParameter('statut', 'termine')
                ->setParameter('psg', 'PSG')
                ->getQuery()
                ->getSingleScalarResult();

            $defaites = $entityManager->getRepository(Matchs::class)
                ->createQueryBuilder('m')
                ->select('COUNT(m.id)')
                ->where('m.statut = :statut')
                ->andWhere('(
                    (m.equipeDomicile = :psg AND m.scoreDomicile < m.scoreExterieure) OR
                    (m.equipeExterieure = :psg AND m.scoreExterieure < m.scoreDomicile)
                )')
                ->setParameter('statut', 'termine')
                ->setParameter('psg', 'PSG')
                ->getQuery()
                ->getSingleScalarResult();

            $nuls = $entityManager->getRepository(Matchs::class)
                ->createQueryBuilder('m')
                ->select('COUNT(m.id)')
                ->where('m.statut = :statut')
                ->andWhere('(m.equipeDomicile = :psg OR m.equipeExterieure = :psg)')
                ->andWhere('m.scoreDomicile = m.scoreExterieure')
                ->setParameter('statut', 'termine')
                ->setParameter('psg', 'PSG')
                ->getQuery()
                ->getSingleScalarResult();

            $prochainsMatchs = $entityManager->getRepository(Matchs::class)
                ->createQueryBuilder('m')
                ->select('COUNT(m.id)')
                ->where('m.dateMatch > :now')
                ->andWhere('(m.equipeDomicile = :psg OR m.equipeExterieure = :psg)')
                ->andWhere('m.statut = :statut')
                ->setParameter('now', new \DateTime())
                ->setParameter('psg', 'PSG')
                ->setParameter('statut', 'programme')
                ->getQuery()
                ->getSingleScalarResult();

            return new JsonResponse([
                'totalMatchs' => (int)$totalMatchs,
                'matchsJoues' => (int)$matchsJoues,
                'victoires' => (int)$victoires,
                'defaites' => (int)$defaites,
                'nuls' => (int)$nuls,
                'prochainsMatchs' => (int)$prochainsMatchs,
                'pourcentageVictoires' => $matchsJoues > 0 ? round(($victoires / $matchsJoues) * 100, 1) : 0
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération des statistiques',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    private function formatMoney(float $amount): string
    {
        if ($amount >= 1000000) {
            return number_format($amount / 1000000, 1) . 'M€';
        } elseif ($amount >= 1000) {
            return number_format($amount / 1000, 1) . 'K€';
        } else {
            return number_format($amount, 2) . '€';
        }
    }
}