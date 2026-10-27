<?php

namespace ClassicO\NovaMediaLibrary\Core;

use ClassicO\NovaMediaLibrary\API;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Helper {

	static function storage()
	{
		return Storage::disk(config('nova-media-library.disk', 'public'));
	}

	static function directories()
	{
		try {
			$len = strlen(substr(self::folder(), 1));
			$paths = [];

			// Folders with a placeholder object in the bucket
			$dirs = self::storage()->allDirectories(config('nova-media-library.folder'));
			foreach ($dirs as $item) {
				if ( 'nml_temp' == $item ) continue;
				$paths[] = trim(substr($item, $len), '/');
			}

			// Folders that exist only through file keys (no placeholder object): take them from the DB
			$fromDb = 0;
			if ( 'folders' == config('nova-media-library.store') ) {
				$folders = Model::query()->distinct()->pluck('folder');
				foreach ($folders as $folder) {
					$path = trim((string)$folder, '/');
					if ( '' === $path ) continue;
					$paths[] = $path;
					$fromDb++;
				}
			}

			Log::info('[nova-media-library] directories listed', [
				'disk' => config('nova-media-library.disk'),
				'prefix' => config('nova-media-library.folder'),
				's3_count' => count($dirs),
				'db_count' => $fromDb,
				'directories' => array_slice($dirs, 0, 200),
			]);

			$array = [];
			foreach (array_unique($paths) as $path) {
				if ( '' === $path ) continue;
				$node = &$array;
				foreach (explode('/', $path) as $segment) {
					if ( '' === $segment ) continue;
					if ( !isset($node[$segment]) or !is_array($node[$segment]) ) $node[$segment] = [];
					$node = &$node[$segment];
				}
				unset($node);
			}

			return self::leaves($array);
		} catch (\Throwable $e) {
			Log::error('[nova-media-library] failed to list directories', [
				'disk' => config('nova-media-library.disk'),
				'prefix' => config('nova-media-library.folder'),
				'exception' => get_class($e),
				'message' => $e->getMessage(),
			]);

			return [];
		}
	}

	// Leaf folders are `0` in the tree the frontend expects
	private static function leaves(array $tree)
	{
		foreach ($tree as $key => $child)
			$tree[$key] = $child ? self::leaves($child) : 0;

		return $tree;
	}

	static function replace($str)
	{
		return preg_replace('/(\/)\\1+/', '$1', str_replace('\\', '/', $str));
	}

	static function folder($path = '')
	{
		return self::replace('/'. (string)config('nova-media-library.folder', '') .'/'. $path);
	}

	static function size($bytes)
	{
		if ( $bytes / 1073741824 >= 1 )
			return round($bytes / 1073741824, 2) .' '. __('gb');

		if ( $bytes / 1048576 >= 1 )
			return round($bytes / 1048576, 2) .' '. __('mb');

		if ( $bytes / 1024 >= 1 )
			return round($bytes / 1024, 2) .' '. __('kb');

		return $bytes .' '. __('b');
	}

	static function isPrivate($folder)
	{
		$disk = config('nova-media-library.disk');
		$private = false;

		if ( 's3' == $disk )
			$private = config('nova-media-library.private') ?? false;
		else if ( 'local' == $disk )
			$private = '/public/' != substr(self::folder($folder), 0, 8);

		return $private;
	}

	static function visibility($bool)
	{
		return $bool ? 'private' : 'public';
	}

	static function preview($item, $size)
	{
		if ( !in_array($size, data_get($item, 'options.img_sizes', [])) ) return null;

		$url = data_get($item, 'url');

		return data_get($item, 'private') ? $url . '&img_size='. $size : API::getImageSize($url, $size);
	}

	static function localPublic($folder, $private)
	{
		return (
			'local' == config('nova-media-library.disk') and
			!$private and
			'/public/' == substr(self::folder($folder), 0, 8)
		);
	}

}
