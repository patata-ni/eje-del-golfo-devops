<?php

$root = dirname(__DIR__);
$hashes = [];
foreach (['app', 'bootstrap', 'config', 'database/migrations', 'resources', 'routes', 'public/css', 'public/images', 'public/build'] as $folder) {
    if (!is_dir($root.'/'.$folder)) {
        continue;
    }
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/'.$folder, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
        if ($file->isFile() && !str_starts_with($relative, 'bootstrap/cache/')) {
            $hashes[$relative] = hash_file('sha256', $file->getPathname());
        }
    }
}
ksort($hashes);
$manifest = [
    'commit' => getenv('VCS_REF') ?: 'local',
    'source_hash' => hash('sha256', json_encode($hashes, JSON_UNESCAPED_SLASHES)),
    'built_at' => gmdate('c'),
];
@mkdir($root.'/storage/app', 0775, true);
file_put_contents($root.'/storage/app/build-version.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo 'Manifiesto generado para '.$manifest['commit'].PHP_EOL;
