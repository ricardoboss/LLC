<?php

namespace App;

use Exceptions\LeveledException;

class Template {
	const CACHE_TIME = 1800;
	//const CACHE_TIME = 0;
	const CACHE_FOLDER = CACHE . "templates" . DIRECTORY_SEPARATOR;
	const INCLUDE_PATTERN = /** @lang RegExp */
		"/@include\(\"([^\"]+?)\"\)/";
	const SECTION_PATTERN = /** @lang RegExp */
		"/@section\(\"([^\"]+?)\"\)((?s).*?)@endSection/";
	const PASTE_PATTERN = /** @lang RegExp */
		"/@paste\(\"([^\"]+?)\"\)/";
	private static $sections = array();

	/**
	 * @param string $name The path to be resolved (e.g. 'app.master').
	 *
	 * @return string The path to the file.
	 *
	 * @throws \Exceptions\LeveledException If the template file cannot be found.
	 */
	private static function resolvePath(string $name): string {
		$name = str_replace('.', DIRECTORY_SEPARATOR, $name);
		$name .= ".template.php";

		$path = TEMPLATES . $name;

		if (!file_exists($path))
			throw new LeveledException("Template file does not exist: " . $name, LeveledException::LEVEL_WARNING);

		return $path;
	}

	/**
	 * Process a template file.
	 *
	 * @param string $name  The name of the template file.
	 * @param bool   $isCache Whether or not the file is in cache only.
	 *
	 * @return string The processed template file.
	 *
	 * @throws \Exceptions\LeveledException If the file cannot be found.
	 */
	private static function process(string $name, $isCache = false): string {
		if (!file_exists(Template::CACHE_FOLDER) || !is_dir(Template::CACHE_FOLDER))
			mkdir(Template::CACHE_FOLDER);

		$cacheFile = Template::CACHE_FOLDER . $name;

		if ($isCache || !file_exists($cacheFile) || filemtime($cacheFile) > Template::CACHE_TIME) {
			if (!$isCache) {
				$file = static::resolvePath($name);
				$content = file_get_contents($file);
			} else {
				$content = file_get_contents($cacheFile);
			}

			$matches = array();

			preg_match_all(Template::SECTION_PATTERN, $content, $matches);
			if (count($matches[0]) > 0) {
				for ($i = 0; $i < count($matches[0]); $i++) {
					$cacheName = "$name.{$matches[1][$i]}";

					file_put_contents(Template::CACHE_FOLDER . $cacheName, $matches[2][$i]);

					static::$sections[$matches[1][$i]] = static::process($cacheName, true);
				}

				$content = preg_replace(Template::SECTION_PATTERN, "", $content);
			}

			preg_match_all(Template::PASTE_PATTERN, $content, $matches);
			if (count($matches[0]) > 0) {
				foreach ($matches[1] as $section) {
					if (array_key_exists($section, static::$sections))
						$content = str_replace("@paste(\"$section\")", static::$sections[$section], $content);
					else
						$content = str_replace("@paste(\"$section\")", "", $content);
						//throw new LeveledException("Section not defined: " . $section, LeveledException::LEVEL_ERROR);
				}
			}

			preg_match_all(Template::INCLUDE_PATTERN, $content, $matches);
			if (count($matches[0]) > 0)
				foreach ($matches[1] as $include) {
					$content = str_replace("@include(\"$include\")", static::process($include), $content);
				}

			$content = trim($content);

			$success = file_put_contents($cacheFile, $content);

			if ($success === false)
				throw new LeveledException("Could not save compiled template in cache!", LeveledException::LEVEL_ERROR);
		}

		return file_get_contents($cacheFile);
	}

	/**
	 * Prints a processed template file.
	 *
	 * @param string $name The name of the template file.
	 *
	 * @throws \Exceptions\LeveledException If the template file cannot be found.
	 */
	public static function display(string $name) {
		print static::process($name);
	}

	/**
	 * Tests whether or not a template file exists.
	 *
	 * @param string $name The name of the template file.
	 *
	 * @return bool True if the template exists, false otherwise.
	 */
	public static function exists(string $name): bool {
		try {
			static::resolvePath($name);

			return true;
		} catch (LeveledException $e) {
			return false;
		}
	}
}