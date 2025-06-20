<?php

namespace App\Controller;

use App\Entity\Actualites;
use App\Entity\Joueurs;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Récupérer toutes les statistiques du dashboard depuis la BDD
     * @Route("/api/dashboard/stats", name="dashboard_stats", methods={"GET"})
     */
    public function getDashboardStats(): JsonResponse
    {
        try {
            // 1. Compter les joueurs dans l'équipe PSG
            $totalJoueurs = $this->entityManager->getRepository(Joueurs::class)
                ->createQueryBuilder('j')
                ->select('COUNT(j.id)')
                ->where('j.equipe = :equipe')
                ->setParameter('equipe', 'PSG')
                ->getQuery()
                ->getSingleScalarResult();

            // 2. Compter les produits et calculer le stock total
            $stockQuery = $this->entityManager->getRepository(Product::class)
                ->createQueryBuilder('p')
                ->select('SUM(p.stock) as totalStock, COUNT(p.id) as totalProduits')
                ->getQuery()
                ->getSingleResult();

            $totalStock = $stockQuery['totalStock'] ?? 0;
            $totalProduits = $stockQuery['totalProduits'] ?? 0;

            // 3. Compter les actualités publiées
            $totalActualites = $this->entityManager->getRepository(Actualites::class)
                ->createQueryBuilder('a')
                ->select('COUNT(a.id)')
                ->where('a.statut = :statut')
                ->setParameter('statut', 'publie')
                ->getQuery()
                ->getSingleScalarResult();

            // 4. Calculer les revenus potentiels (prix * stock)
            $revenusQuery = $this->entityManager->getRepository(Product::class)
                ->createQueryBuilder('p')
                ->select('SUM(p.prix * p.stock) as revenus')
                ->getQuery()
                ->getSingleResult();

            $revenus = $revenusQuery['revenus'] ?? 0;
            $revenusFormattes = $this->formatMoney($revenus);

            // 5. Compter les utilisateurs par rôle
            $totalUtilisateurs = $this->entityManager->getRepository(User::class)
                ->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->getQuery()
                ->getSingleScalarResult();

            $totalAdmins = $this->entityManager->getRepository(User::class)
                ->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->where('u.role = :role')
                ->setParameter('role', 'admin')
                ->getQuery()
                ->getSingleScalarResult();

            // 6. Statistiques produits par catégorie (basé sur le nom du produit)
            $produitsParCategorie = $this->entityManager->getRepository(Product::class)
                ->createQueryBuilder('p')
                ->select('
                    CASE 
                        WHEN p.nom LIKE :maillot THEN "Maillots"
                        WHEN p.nom LIKE :tshirt THEN "T-shirts" 
                        WHEN p.nom LIKE :accessoire THEN "Accessoires"
                        ELSE "Autres"
                    END as categorie,
                    COUNT(p.id) as nombre,
                    SUM(p.stock) as stock
                ')
                ->setParameter('maillot', '%maillot%')
                ->setParameter('tshirt', '%shirt%')
                ->setParameter('accessoire', '%tasse%')
                ->groupBy('categorie')
                ->getQuery()
                ->getResult();

            // 7. Top 5 des produits les plus chers
            $topProduits = $this->entityManager->getRepository(Product::class)
                ->createQueryBuilder('p')
                ->select('p.nom, p.prix, p.stock')
                ->orderBy('p.prix', 'DESC')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult();

            // 8. Dernières actualités (pour notifications)
            $dernieresActualites = $this->entityManager->getRepository(Actualites::class)
                ->createQueryBuilder('a')
                ->select('a.id, a.titre, a.datePublication')
                ->where('a.statut = :statut')
                ->setParameter('statut', 'publie')
                ->orderBy('a.datePublication', 'DESC')
                ->setMaxResults(3)
                ->getQuery()
                ->getResult();

            return new JsonResponse([
                // Statistiques principales
                'totalJoueurs' => (int)$totalJoueurs,
                'totalStock' => (int)$totalStock,
                'totalProduits' => (int)$totalProduits,
                'totalActualites' => (int)$totalActualites,
                'revenus' => $revenusFormattes,
                'totalUtilisateurs' => (int)$totalUtilisateurs,
                'totalAdmins' => (int)$totalAdmins,
                
                // Données détaillées
                'produitsParCategorie' => $produitsParCategorie,
                'topProduits' => $topProduits,
                'dernieresActualites' => $dernieresActualites,
                
                // Métadonnées
                'lastUpdate' => (new \DateTime())->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération des statistiques',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les statistiques des joueurs par poste
     * @Route("/api/dashboard/joueurs-stats", name="dashboard_joueurs_stats", methods={"GET"})
     */
    public function getJoueursStats(): JsonResponse
    {
        try {
            $statsParPoste = $this->entityManager->getRepository(Joueurs::class)
                ->createQueryBuilder('j')
                ->select('j.poste, COUNT(j.id) as nombre, AVG(j.age) as ageMoyen')
                ->where('j.equipe = :equipe')
                ->setParameter('equipe', 'PSG')
                ->groupBy('j.poste')
                ->getQuery()
                ->getResult();

            $ageStats = $this->entityManager->getRepository(Joueurs::class)
                ->createQueryBuilder('j')
                ->select('MIN(j.age) as ageMin, MAX(j.age) as ageMax, AVG(j.age) as ageMoyen')
                ->where('j.equipe = :equipe')
                ->setParameter('equipe', 'PSG')
                ->getQuery()
                ->getSingleResult();

            $paysStats = $this->entityManager->getRepository(Joueurs::class)
                ->createQueryBuilder('j')
                ->select('j.paysOrigine, COUNT(j.id) as nombre')
                ->where('j.equipe = :equipe')
                ->setParameter('equipe', 'PSG')
                ->groupBy('j.paysOrigine')
                ->orderBy('nombre', 'DESC')
                ->getQuery()
                ->getResult();

            return new JsonResponse([
                'statsParPoste' => $statsParPoste,
                'ageStats' => $ageStats,
                'paysStats' => $paysStats
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération des stats joueurs'
            ], 500);
        }
    }

    /**
     * Récupérer les statistiques de la boutique
     * @Route("/api/dashboard/boutique-stats", name="dashboard_boutique_stats", methods={"GET"})
     */
    public function getBoutiqueStats(): JsonResponse
    {
        try {
            // Produits les plus en stock
            $produitsTopStock = $this->entityManager->getRepository(Product::class)
                ->createQueryBuilder('p')
                ->select('p.nom, p.stock, p.prix')
                ->orderBy('p.stock', 'DESC')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult();

            // Produits en rupture ou stock faible
            $produitsStockFaible = $this->entityManager->getRepository(Product::class)
                ->createQueryBuilder('p')
                ->select('p.nom, p.stock, p.prix')
                ->where('p.stock <= :seuil')
                ->setParameter('seuil', 10)
                ->orderBy('p.stock', 'ASC')
                ->getQuery()
                ->getResult();

            // Valeur totale du stock
            $valeurStock = $this->entityManager->getRepository(Product::class)
                ->createQueryBuilder('p')
                ->select('SUM(p.prix * p.stock) as valeurTotale')
                ->getQuery()
                ->getSingleScalarResult();

            return new JsonResponse([
                'produitsTopStock' => $produitsTopStock,
                'produitsStockFaible' => $produitsStockFaible,
                'valeurStock' => $this->formatMoney($valeurStock ?? 0),
                'alertesStock' => count($produitsStockFaible)
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la récupération des stats boutique'
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