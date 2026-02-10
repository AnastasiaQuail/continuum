<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/symfony.php';

set('repository', 'https://github.com/AnastasiaQuail/continuum.git');

host('production')
    ->setHostname(getenv('APP_HOSTNAME'))
    ->setRemoteUser(getenv('APP_REMOTE_USER'))
    ->setDeployPath('~/domains/{{hostname}}')
    ->set('http_user', '{{remote_user}}');

set('keep_releases', 3);
set('clear_paths', [
    '.github',
    'assets',
    'frankenphp',
    'tests',
    '.dockerignore',
    '.editorconfig',
    '.env',
    '.env.dev',
    '.env.test',
    '.gitattributes',
    '.gitignore',
    'compose.override.yaml',
    'compose.prod.yaml',
    'compose.yaml',
    'deploy.php',
    'Dockerfile',
    'importmap.php',
    'phpunit.dist.xml',
    'Readme.md',
]);

task('deploy:assets:compile', static function (): void {
    run('cd {{release_or_current_path}} && {{bin/console}} asset-map:compile {{console_options}}');
});
task('deploy:symlink:public', static function (): void {
    run('{{bin/symlink}} {{current_path}}/public {{deploy_path}}/public_html');
});

after('deploy:vendors', 'deploy:dump-env');
after('deploy:cache:clear', 'deploy:assets:compile');
after('deploy:cache:clear', 'deploy:clear_paths');
before('deploy:publish', 'database:migrate');
after('deploy:symlink', 'deploy:symlink:public');

after('deploy:failed', 'deploy:unlock');
