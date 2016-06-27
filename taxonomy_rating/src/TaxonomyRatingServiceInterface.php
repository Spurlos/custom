<?php

namespace Drupal\taxonomy_rating;

/**
 * Interface TaxonomyRatingServiceInterface.
 *
 * @package Drupal\taxonomy_rating
 */
interface TaxonomyRatingServiceInterface {

  public function calculate($genre_tid);
  
}
