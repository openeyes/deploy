set :stage, :develop

server '52.49.56.3',
       user: 'deploy',
       roles: %w{web}

set :deploy_to, '/var/www/portal_frontend'
set :branch, 'develop'
set :gulp_tasks, 'build --env=development'
