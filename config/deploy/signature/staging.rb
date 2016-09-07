set :stage, :develop

server 'release.oesign.uk',
  user: 'deploy',
  roles: %w{web}

set :deploy_to, '/var/www/signature'
set :branch, ENV.fetch('branch', 'master')
