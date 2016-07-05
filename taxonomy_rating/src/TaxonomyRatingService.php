<?php

namespace Drupal\taxonomy_rating;

use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityManager;
use Drupal\flag\FlagService;

/**
 * Class TaxonomyRatingService.
 *
 * @package Drupal\taxonomy_rating
 */
class TaxonomyRatingService implements TaxonomyRatingServiceInterface {

  /**
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQuery;
  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;
  /**
   * Drupal\Core\Entity\EntityManager definition.
   *
   * @var \Drupal\Core\Entity\EntityManager
   */
  protected $entityManager;
  /**
   * Drupal\flag\FlagService definition.
   *
   * @var \Drupal\flag\FlagService
   */
  protected $flag;

  /**
   * Constructor.
   */
  public function __construct(QueryFactory $entity_query, ConfigFactory $config_factory, EntityManager $entity_manager, FlagService $flag) {
    $this->entityQuery = $entity_query;
    $this->configFactory = $config_factory;
    $this->entityManager = $entity_manager;
    $this->flag = $flag;
  }

  public function calculate($genre_tid) {
    $config = $this->configFactory->get('taxonomy_rating.settings');
    $book_weight = $config->get('book_weight');
    $author_weight = $config->get('author_weight');
    $query = $this->entityQuery->get('node')
      ->condition('type', 'book')
      ->condition("field_genre", $genre_tid, '=');
    $book_nids = $query->execute();
    $node_storage = $this->entityManager->getStorage('node');
    $book_nodes = $node_storage->loadMultiple(array_values($book_nids));

    $flag_service = $this->flag;
    $flag = $flag_service->getFlagByID('like');

    $total_flags = 0;
    foreach ($book_nodes as $book_node) {
      $node_flags = $flag_service->getFlaggings($flag, $book_node);
      if ($node_flags) {
        $total_flags += count($node_flags);
      }
    }
    $books_rating = $total_flags * $book_weight;

    $total_flags = 0;
    $book_authors = [];
    foreach ($book_nodes as $book_node) {
      /** @var \Drupal\node\Entity\Node $book_node */
      foreach ($book_node->field_author as $author) {
        $book_authors[$author->entity->id()] = $author->entity;
      }
    }
    foreach ($book_authors as $book_author) {
      /** @var \Drupal\flag\FlagService $flag_service */
      $node_flags = $flag_service->getFlaggings($flag, $book_author);
      if ($node_flags) {
        $total_flags += count($node_flags);
      }
    }
    $authors_rating = $total_flags * $author_weight;

    $genre_rating = $books_rating + $authors_rating;
    return $genre_rating;
  }

}
