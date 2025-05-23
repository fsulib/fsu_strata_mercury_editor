<?php

namespace Drupal\fsu_strata_mercury_editor;

/**
 * FsuStrataMercuryVideoUrlParser service.
 */
class FsuStrataMercuryVideoUrl {

  /**
   * Returns the video id from a url.
   */
  public function getVideoId(string $url) {
    $video_id = FALSE;

    // select video parser
    if ($this->isYoutubeUrl($url)) {
      $video_id = $this->getYoutubeVideoId($url);
    }
    elseif ($this->isVimeoUrl($url)) {
      $video_id = $this->getVimeoVideoId($url);
    }

    return $video_id;
  }

  /**
   *  Indicates if given url is for YouTube.
   */
  public function isYoutubeUrl(string $url) {
    $url_host = $this->getUrlHost($url);
    $result = false;
    if (str_contains($url_host,'youtube') || str_contains($url_host,'youtu.be')) {
      $result = true;
    }

    return $result;
  }

  /**
   * Indicated if given url is for vimeo.
   */
  public function isVimeoUrl(string $url) {
    $url_host = $this->getUrlHost($url);
    $result = false;
    if ($url_host == 'vimeo') {
      $result = true;
    }

    return $result;
  }

  /**
   * Parses url and returns hostname
   */
  private function getUrlHost(string $url) {
    $url_host = '';
    $url_array = parse_url($url);

    if (array_key_exists('scheme', $url_array)
      && array_key_exists('host', $url_array)
      && array_key_exists('path', $url_array)
    ) {
      $url_host = $url_array['host'];
    }

    return $url_host;
  }

  /**
   * Returns video id from a YouTube url
   *
   * parser code found https://snippets.cacher.io/snippet/5e213e9727458692f322
   */
  private function getYoutubeVideoId(string $url) {
    $video_id = FALSE;
    $pattern =
      '%^# Match any youtube URL
		(?:https?://)?  # Optional scheme. Either http or https
		(?:www\.)?      # Optional www subdomain
		(?:             # Group host alternatives
			youtu\.be/    # Either youtu.be,
		| youtube\.com  # or youtube.com
			(?:           # Group path alternatives
				/embed/     # Either /embed/
			| /v/         # or /v/
			| /watch\?v=  # or /watch\?v=
			)             # End path alternatives.
		)               # End host alternatives.
		([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
		%x';
    $result = preg_match($pattern, $url, $matches);
    if (false !== $result) {
      $video_id = $matches[1];
    }
    return  $video_id;
  }

  /**
   * Returns video id from a Vimeo url
   *
   * parser code found https://snippets.cacher.io/snippet/5e213e9727458692f322
   */
  private function getVimeoVideoId(string $url) {
    $video_id = FALSE;
    if (preg_match("/https?:\/\/(?:www\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/", $url, $id)){
      $video_id = $id[3];
    }

    return $video_id;
  }

}
