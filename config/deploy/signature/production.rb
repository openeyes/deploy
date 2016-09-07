set :stage, :production

server 'oesign.uk',
  user: 'deploy',
  roles: %w{web}

set :deploy_to, '/var/www/signature'
set :branch, 'master'
