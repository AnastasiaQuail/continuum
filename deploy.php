<?php

declare(strict_types=1);

namespace Deployer;

require __DIR__ . '/vendor/deployer/deployer/recipe/symfony.php';

host('production')
    ->setHostname(getenv('APP_HOSTNAME'))
    ->setRemoteUser(getenv('APP_REMOTE_USER'))
    ->setDeployPath('~/domains/{{hostname}}')
    ->set('http_user', '{{remote_user}}');

set('repository', 'https://github.com/AnastasiaQuail/continuum.git');
set('keep_releases', 3);
set('deploy_paths', [
    'bin',
    'config',
    'data',
    'migrations',
    'public',
    'src',
    'templates',
    'var',
    'vendor',
    '.env.local',
    '.env.local.php',
    'composer.json',
]);
add('shared_dirs', ['var/backups']);
add('writable_dirs', ['var/backups']);

task('deploy:assets:compile', static function (): void {
    run('cd {{release_path}} && {{bin/console}} asset-map:compile {{console_options}}');
});
task('deploy:paths:clear', static function (): void {
    $paths = explode("\n", run("cd {{release_path}} && find . -maxdepth 1 -mindepth 1 -printf '%f\n'"));

    set('clear_paths', array_values(array_diff($paths, get('deploy_paths'))));
    invoke('deploy:clear_paths');
});
task('deploy:symlink:public', static function (): void {
    run('{{bin/symlink}} {{current_path}}/public {{deploy_path}}/public_html');
});

after('deploy:vendors', 'deploy:dump-env');
after('deploy:cache:clear', 'deploy:assets:compile');
after('deploy:cache:clear', 'deploy:paths:clear');
before('deploy:publish', 'database:migrate');
after('deploy:symlink', 'deploy:symlink:public');

after('deploy:failed', 'deploy:unlock');
