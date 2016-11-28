set :stage, :production

server 'oegateway.org.uk',
  user: 'deploy',
  roles: %w{web}

set :deploy_to, '/var/www/portal_api'
set :branch, 'master'
