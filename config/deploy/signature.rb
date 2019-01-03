set :application, 'Signature'
set :repo_url, 'git@github.com:openeyes/signature-capture.git'
set :linked_files, fetch(:linked_files, []).push('src/config/config.js')

Rake::Task['deploy:updated'].prerequisites.delete('npm:install')
Rake::Task['deploy:updated'].prerequisites.delete('bower:install')
Rake::Task['deploy:updated'].prerequisites.delete('gulp:install')
Rake::Task['deploy:updated'].prerequisites.delete('composer:install')
