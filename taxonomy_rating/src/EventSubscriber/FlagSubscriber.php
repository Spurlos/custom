<?php

namespace Drupal\taxonomy_rating\EventSubscriber;

use Drupal\flag\Event\FlagEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class FlagSubscriber.
 *
 * @package Drupal\taxonomy_rating
 */
class FlagSubscriber implements EventSubscriberInterface {


  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events[FlagEvents::ENTITY_FLAGGED] = ['onLike'];
    $events[FlagEvents::ENTITY_UNFLAGGED] = ['onUnlike'];

    return $events;
  }

  function modifyGenreRating($action, $event) {
    $config = \Drupal::config('taxonomy_rating.settings');
    $calculation_method = $config->get('calculation_method');
    if ($calculation_method == 'onEvent') {
      if ($action == 'like') {
        $flag = $event->getFlagging();
      }
      elseif ($action == 'unlike') {
        $flags = $event->getFlaggings();
        $flag = reset($flags);
      }
      else {
        return;
      }
      $flag_type = $flag->flag_id->value;
      if ($flag_type == 'like') {
        $nid = $flag->entity_id->value;
        $node_storage = \Drupal::entityManager()->getStorage('node');
        $node = $node_storage->load($nid);
        $node_type = $node->bundle();

        $term_storage = \Drupal::entityManager()->getStorage('taxonomy_term');
        if ($node_type == 'book') {
          $book_weight = $config->get('book_weight');
          $rating_storage_fieldname = $config->get('rating_storage_fieldname');
          $genre_tid = $node->field_genre->entity->id();
          $term = $term_storage->load($genre_tid);
          if ($action == 'like') {
            $term->$rating_storage_fieldname->value += $book_weight;
          }
          elseif ($action == 'unlike') {
            $term->$rating_storage_fieldname->value -= $book_weight;
          }
          $term->save();
        }
        elseif ($node_type == 'author') {
          //query: get all books of the author, get all genres of those books, sort unique genres
          $author_weight = $config->get('author_weight');
          $rating_storage_fieldname = $config->get('rating_storage_fieldname');
          $genre_tids = [];
          $author_id = $node->id();
          $query = \Drupal::entityQuery('node')
            ->condition('type', 'book')
            ->condition("field_author", $author_id, '=');
          $book_nids = $query->execute();
          $node_storage = \Drupal::entityManager()->getStorage('node');
          $book_nodes = $node_storage->loadMultiple(array_values($book_nids));
          foreach ($book_nodes as $book_node) {
            $genre_tids[] = $book_node->field_genre->entity->id();
          }
          $genre_tids = array_unique($genre_tids);
          foreach ($genre_tids as $genre_tid) {
            $term = $term_storage->load($genre_tid);
            if ($action == 'like') {
              $term->$rating_storage_fieldname->value += $author_weight;
            }
            elseif ($action == 'unlike') {
              $term->$rating_storage_fieldname->value -= $author_weight;
            }
            $term->save();
          }
        }
      }
    }
  }

  /**
   * This method is called whenever the flag.entity_flagged event is
   * dispatched.
   *
   * @param GetResponseEvent $event
   */
  public function onLike(Event $event) {
    $this->modifyGenreRating('like', $event);
  }

  /**
   * This method is called whenever the flag.entity_unflagged event is
   * dispatched.
   *
   * @param GetResponseEvent $event
   */
  public function onUnlike(Event $event) {
    $this->modifyGenreRating('unlike', $event);
  }

}
