set :stage, :develop

server 'dev.oesign.uk',
  user: 'deploy',
  roles: %w{web}

set :deploy_to, '/var/www/signature'
set :branch, 'develop'
