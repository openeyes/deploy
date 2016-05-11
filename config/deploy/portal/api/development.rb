set :stage, :develop

server '52.49.56.3',
       user: 'deploy',
       roles: %w{web}

set :deploy_to, '/var/www/portal_api'
set :branch, 'develop'
set :deploy_subdir, 'api'
