set :application, 'Signature'
set :repo_url, 'https://github.com/openeyes/signature-capture.git'

Rake::Task['deploy:updated'].prerequisites.delete('npm:install')
Rake::Task['deploy:updated'].prerequisites.delete('bower:install')
Rake::Task['deploy:updated'].prerequisites.delete('gulp:install')
Rake::Task['deploy:updated'].prerequisites.delete('composer:install')
