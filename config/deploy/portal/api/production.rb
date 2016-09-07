set :stage, :production

server '52.16.74.252',
  user: 'deploy',
  roles: %w{web}

set :deploy_to, '/var/www/portal_api'
set :branch, 'master'
