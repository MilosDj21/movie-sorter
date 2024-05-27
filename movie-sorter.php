<?php

/**
 * Plugin Name: Movie Sorter
 * Description: Sorting movies
 * Author:      MDj21
 * Version:     1.0.0
 */

defined('ABSPATH') or die("Cannot access pages directly.");

class MovieSorter {
  private $orderBy = null;
  private $order = null;

  function __construct() {
    add_shortcode('renderMovieSorter', array($this, 'parseShortcode'));
    add_action('init', array($this, 'handleForm'));
    add_action('pre_get_posts', array($this, 'modifyQuery'));
  }

  function parseShortcode() {

    $selected = null;
    if (isset($_GET["selectedSorter"])) {
      $selected = $_GET["selectedSorter"];
    }

    ob_start();?>
      <form method="GET">
        <select name="selectedSorter" onchange="this.form.submit()">
          <option value="datedesc" <?php echo $selected === "datedesc" || !$selected ? "selected" : ""; ?>>Datumu Silazno</option>
          <option value="dateasc" <?php echo $selected === "dateasc" ? "selected" : ""; ?>>Datumu Uzlazno</option>
          <option value="imdbdesc" <?php echo $selected === "imdbdesc" ? "selected" : ""; ?>>IMDB Silazno</option>
          <option value="imdbasc" <?php echo $selected === "imdbasc" ? "selected" : ""; ?>>IMDB Uzlazno</option>
          <option value="viewsdesc" <?php echo $selected === "viewsdesc" ? "selected" : ""; ?>>Pregledima Silazno</option>
          <option value="viewsasc" <?php echo $selected === "viewsasc" ? "selected" : ""; ?>>Pregledima Uzlazno</option>
        </select>
        <input type="hidden" name="justsubmitted" value="true">
      </form>
    <?php
return ob_get_clean();
  }

  function handleForm() {
    if (isset($_GET['justsubmitted']) && $_GET['justsubmitted'] == "true") {
      if (isset($_GET['selectedSorter'])) {
        switch ($_GET['selectedSorter']) {
        case "dateasc":
          $this->order = 'ASC';
          break;
        case "datedesc":
          $this->order = 'DESC';
          break;
        case "imdbdesc":
          $this->orderBy = 'imdb';
          $this->order = 'DESC';
          break;
        case "imdbasc":
          $this->orderBy = 'imdb';
          $this->order = 'ASC';
          break;
        case "viewsdesc":
          $this->orderBy = 'total_views_count';
          $this->order = 'DESC';
          break;
        case "viewsasc":
          $this->orderBy = 'total_views_count';
          $this->order = 'ASC';
          break;
        }
      }
    }
  }

  function modifyQuery($query) {
    if (!is_admin() && $query->is_main_query()) {
      if ($this->orderBy) {
        $query->set('orderby', 'meta_value_num');
        $query->set('meta_key', $this->orderBy);
      }

      if ($this->order) {
        $query->set('order', $this->order);
      }
    }
  }
}

$movieSorter = new MovieSorter();