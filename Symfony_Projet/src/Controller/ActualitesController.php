<?php

namespace App\Controller;

use App\Entity\Joueurs;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ActualitesController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/dashboard/stats', name: 'dashboard_stats', methods: ['GET'])]
    public function getDashboardStats(): JsonResponse
    {
        try {
            // Compter les joueurs PSG
            $totalJoueurs = $this->entityManager->getRepository(Joueurs::class)
                ->createQueryBuilder('j')
                ->select('COUNT(j.id)')
                ->where('j.equipe = :equipe')
                ->setParameter('equipe', 'PSG')
                ->getQuery()
                ->getSingleScalarResult();

            // Compter les produits et stock
            $stockQuery = $this->entityManager->getRepository(Product::class)
                ->createQueryBuilder('p')
                ->select('SUM(p.stock) as totalStock, COUNT(p.id) as totalProduits')
                ->getQuery()
                ->getSingleResult();

            $totalStock = $stockQuery['totalStock'] ?? 0;
            $totalProduits = $stockQuery['totalProduits'] ?? 0;

            // Calculer les revenus
            $revenusQuery = $this->entityManager->getRepository(Product::class)
                ->createQueryBuilder('p')
                ->select('SUM(p.prix * p.stock) as revenus')
                ->getQuery()
                ->getSingleResult();

            $revenus = $revenusQuery['revenus'] ?? 0;
            $revenusFormattes = $this->formatMoney($revenus);

            // Top produits
            $topProduits = $this->entityManager->getRepository(Product::class)
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
                'totalActualites' => 0,
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

    #[Route('/api/dashboard/joueurs-stats', name: 'dashboard_joueurs_stats', methods: ['GET'])]
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

    #[Route('/api/dashboard/boutique-stats', name: 'dashboard_boutique_stats', methods: ['GET'])]
    public function getBoutiqueStats(): JsonResponse
    {
        try {
            // Produits en stock faible
            $produitsStockFaible = $this->entityManager->getRepository(Product::class)
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

    #[Route('/api/actualites', name: 'get_actualites', methods: ['GET'])]
    public function getActualites(): JsonResponse
    {
        return new JsonResponse([
            [
                'id' => 1,
                'titre' => 'Bienvenue sur le dashboard PSG',
                'contenu' => 'Le système d\'actualités sera bientôt disponible.',
                'date_publication' => date('Y-m-d'),
                'auteur' => 'Système',
                'likes' => 0,
                'commentaires' => 0
            ]
        ]);
    }

    #[Route('/api/actualites', name: 'add_actualite', methods: ['POST'])]
    public function addActualite(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        return new JsonResponse([
            'message' => 'Actualité simulée ajoutée',
            'data' => $data
        ], 201);
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