@servers(['web' => 'ubuntu@localhost'])

@setup
    $repository = 'repo_url';
    $release_dir = '/var/www/html/' . date('Ymd-His');
    $release_path = '/var/www/html/';
    $env = '/var/www/.env'; // Just an example should be hosted on s3 bucket or other secure storage
    $app_folder = 'app';
@endsetup

@story('deploy')
    clone
    composer
    env
    deploy
@endstory

@task('clone')
    echo 'Cloning {{ $repository }} ...'
    git clone --depth 1 {{ $repository }} {{ $release_dir }}
    cd {{ $release_dir }}
    git reset --hard {{ $commit }}
@endtask

@task('composer')
    echo "Installing composer dependencies ..."
    cd {{ $release_dir }}
    composer install --prefer-dist -o --no-dev
@endtask

@task('env')
    echo "Setting ENV ..."
    cd {{ $release_dir }}
    cp {{ $env }} .env
    chmod -R 777 storage
@endtask

@task('deploy')
    echo "Setting release ..."
    cd {{ $release_path }}
    rm -rf {{ $app_folder }}
    mv {{ $release_dir }} {{ $app_folder }}
@endtask
