<?php

namespace Drupal\cat_widget;

use Drupal\Component\Serialization\Json;

class CatClient {

  /**
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * CatClient constructor.
   *
   * @param $http_client_factory \Drupal\Core\Http\ClientFactory
   */
  public function __construct($http_client_factory) {
    $this->client = $http_client_factory->fromOptions([
      'base_uri' => 'https://api.thecatapi.com/v1/',
    ]);
  }

  /**
   * Get a random cat image.
   *
   * @return array
   */
  public function random() {
    $response = $this->client->get('images/search/');
    return Json::decode($response->getBody());
  }

  /**
   * Get a random cat image based on breed.
   *
   * @param string $breed_id
   *   The breed ID.
   * @param int $limit
   *   The number of images to return
   *
   * @return array
   */
  public function randomByBreed($breed_id, $limit = 1) {
    $response = $this->client->get('images/search', [
      'query' => [
        'breed_ids' => $breed_id,
        'limit' => $limit,
      ],
    ]);
    return Json::decode($response->getBody());
  }

  /**
   * Get all available breeds. We cache the data as the information on the cat
   * breeds will not change that often.
   *
   * @return array
   */
  public function getBreeds() {
    $cid = 'cat_widget.breeds_data';

    // Check if data exists in cache.
    $cache = \Drupal::cache()->get($cid);

    if ($cache) {
      // Return the cached data.
      return $cache->data;
    }

    // If not in cache, fetch data from the API.
    $response = $this->client->get('breeds');
    $data = Json::decode($response->getBody());

    if (!empty($data)) {
      // Set the cache (for 24 hours) if data is retrieved.
      \Drupal::cache()->set($cid, $data, strtotime('+24 hours'));

      return $data;
    }

    return [];
  }

}
